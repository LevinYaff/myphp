<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * SQL executor
 *
 * @todo    we must handle the case if sql.php is called directly with a query
 *          that returns 0 rows - to prevent cyclic redirects or includes
 * @package PhpMyAdmin
 */

/**
 * Gets some core libraries
 */
require_once 'libraries/common.inc.php';
require_once 'libraries/Table.class.php';
require_once 'libraries/Header.class.php';
require_once 'libraries/check_user_privileges.lib.php';
require_once 'libraries/bookmark.lib.php';
require_once 'libraries/sql.lib.php';
require_once 'libraries/sqlparser.lib.php';

$response = PMA_Response::getInstance();
$header   = $response->getHeader();
$scripts  = $header->getScripts();
$scripts->addFile('jquery/jquery-ui-timepicker-addon.js');
$scripts->addFile('tbl_change.js');
// the next one needed because sql.php may do a "goto" to tbl_structure.php
$scripts->addFile('tbl_structure.js');
$scripts->addFile('indexes.js');
$scripts->addFile('gis_data_editor.js');

/**
 * Set ajax_reload in the response if it was already set
 */
if (isset($ajax_reload) && $ajax_reload['reload'] === true) {
    $response->addJSON('ajax_reload', $ajax_reload);
}


/**
 * Defines the url to return to in case of error in a sql statement
 */
// Security checkings
if (! empty($goto)) {
    $is_gotofile     = preg_replace('@^([^?]+).*$@s', '\\1', $goto);
    if (! @file_exists('' . $is_gotofile)) {
        unset($goto);
    } else {
        $is_gotofile = ($is_gotofile == $goto);
    }
} else {
    if (empty($table)) {
        $goto = $cfg['DefaultTabDatabase'];
    } else {
        $goto = $cfg['DefaultTabTable'];
    }
    $is_gotofile  = true;
} // end if

if (! isset($err_url)) {
    $err_url = (! empty($back) ? $back : $goto)
        . '?' . PMA_generate_common_url($db)
        . ((strpos(' ' . $goto, 'db_') != 1 && strlen($table))
            ? '&amp;table=' . urlencode($table)
            : ''
        );
} // end if

// Coming from a bookmark dialog
if (isset($_POST['bkm_fields']['bkm_sql_query'])) {
    $sql_query = $_POST['bkm_fields']['bkm_sql_query'];
} elseif (isset($_GET['sql_query'])) {
    $sql_query = $_GET['sql_query'];
}

// This one is just to fill $db
if (isset($_POST['bkm_fields']['bkm_database'])) {
    $db = $_POST['bkm_fields']['bkm_database'];
}


// During grid edit, if we have a relational field, show the dropdown for it.
if (isset($_REQUEST['get_relational_values'])
    && $_REQUEST['get_relational_values'] == true
) {
    PMA_getRelationalValues($db, $table, $display_field);
}

// Just like above, find possible values for enum fields during grid edit.
if (isset($_REQUEST['get_enum_values']) && $_REQUEST['get_enum_values'] == true) {
    PMA_getEnumOrSetValues($db, $table, "enum");
}


// Find possible values for set fields during grid edit.
if (isset($_REQUEST['get_set_values']) && $_REQUEST['get_set_values'] == true) {
    PMA_getEnumOrSetValues($db, $table, "set");
}

/**
 * Check ajax request to set the column order and visibility
 */
if (isset($_REQUEST['set_col_prefs']) && $_REQUEST['set_col_prefs'] == true) {
    PMA_setColumnOrderOrVisibility($table, $db);
}

// Default to browse if no query set and we have table
// (needed for browsing from DefaultTabTable)
if (empty($sql_query) && strlen($table) && strlen($db)) {
    $sql_query = PMA_getDefaultSqlQueryForBrowse($db, $table);
    
    // set $goto to what will be displayed if query returns 0 rows
    $goto = '';
} else {
    // Now we can check the parameters
    PMA_Util::checkParameters(array('sql_query'));
}

/**
 * Parse and analyze the query
 */
require_once 'libraries/parse_analyze.inc.php';


/**
 * Check rights in case of DROP DATABASE
 *
 * This test may be bypassed if $is_js_confirmed = 1 (already checked with js)
 * but since a malicious user may pass this variable by url/form, we don't take
 * into account this case.
 */
if (PMA_hasNoRightsToDropDatabase(
    $analyzed_sql_results, $cfg['AllowUserDropDatabase'], $is_superuser)
) {
    PMA_Util::mysqlDie(
        __('"DROP DATABASE" statements are disabled.'),
        '',
        '',
        $err_url
    );
} // end if

// Include PMA_Index class for use in PMA_DisplayResults class
require_once './libraries/Index.class.php';

require_once 'libraries/DisplayResults.class.php';

$displayResultsObject = new PMA_DisplayResults(
    $GLOBALS['db'], $GLOBALS['table'], $GLOBALS['goto'], $GLOBALS['sql_query']
);

$displayResultsObject->setConfigParamsForDisplayTable();

/**
 * Need to find the real end of rows?
 */
if (isset($find_real_end) && $find_real_end) {
    $unlim_num_rows = PMA_findRealEndOfRows($db, $table);
}


/**
 * Bookmark add
 */
if (isset($_POST['store_bkm'])) {
    PMA_addBookmark($cfg['PmaAbsoluteUri'], $goto);
} // end if


/**
 * Sets or modifies the $goto variable if required
 */
if ($goto == 'sql.php') {
    $is_gotofile = false;
    $goto = 'sql.php?'
          . PMA_generate_common_url($db, $table)
          . '&amp;sql_query=' . urlencode($sql_query);
} // end if


// assign default full_sql_query
$full_sql_query = $sql_query;

// Handle remembered sorting order, only for single table query
if (PMA_isRememberSortingOrder($analyzed_sql_results)) {
    PMA_handleSortOrder($db, $table, $analyzed_sql, $full_sql_query);
}

$sql_limit_to_append = '';
// Do append a "LIMIT" clause?
if (PMA_isAppendLimitClause($analyzed_sql_results)) {
    list($sql_limit_to_append,
        $full_sql_query, $analyzed_display_query, $display_query
    ) = PMA_appendLimitClause(
        $full_sql_query, $analyzed_sql, isset($display_query)
    );
}

$is_procedure = false;
// Since multiple query execution is anyway handled,
// ignore the WHERE clause of the first sql statement
// which might contain a phrase like 'call '        
if (preg_match("/\bcall\b/i", $full_sql_query)
    && empty($analyzed_sql[0]['where_clause'])
) {
    $is_procedure = true;
}

$reload = PMA_hasCurrentDbChanged($db);

// Execute the query
list($result, $num_rows, $unlim_num_rows, $profiling_results,
    $justBrowsing, $extra_data
) = PMA_executeTheQuery(
    $analyzed_sql_results, $full_sql_query, $is_gotofile, $goto, $db, $table,
    isset($find_real_end) ? $find_real_end : null,
    isset($import_text) ? $import_text : null, $cfg['Bookmark']['user'],
    isset($extra_data) ? $extra_data : null
);


// No rows returned -> move back to the calling page
if ((0 == $num_rows && 0 == $unlim_num_rows) || $is_affected) {
    // Delete related tranformation information
    if (PMA_isDeleteTransformationInfo($analyzed_sql_results)) {
        include_once 'libraries/transformations.lib.php';
        if ($analyzed_sql[0]['querytype'] == 'ALTER') {
            if (stripos($analyzed_sql[0]['unsorted_query'], 'DROP') !== false) {
                $drop_column = PMA_getColumnNameInColumnDropSql(
                    $analyzed_sql[0]['unsorted_query']
                );

                if ($drop_column != '') {
                    PMA_clearTransformations($db, $table, $drop_column);
                }
            }

        } else if (($analyzed_sql[0]['querytype'] == 'DROP') && ($table != '')) {
            PMA_clearTransformations($db, $table);
        }
    }

    if ($is_delete) {
        $message = PMA_Message::getMessageForDeletedRows($num_rows);
    } elseif ($is_insert) {
        if ($is_replace) {
            // For replace we get DELETED + INSERTED row count,
            // so we have to call it affected
            $message = PMA_Message::getMessageForAffectedRows($num_rows);
        } else {
            $message = PMA_Message::getMessageForInsertedRows($num_rows);
        }
        $insert_id = $GLOBALS['dbi']->insertId();
        if ($insert_id != 0) {
            // insert_id is id of FIRST record inserted in one insert,
            // so if we inserted multiple rows, we had to increment this
            $message->addMessage('[br]');
            // need to use a temporary because the Message class
            // currently supports adding parameters only to the first
            // message
            $_inserted = PMA_Message::notice(__('Inserted row id: %1$d'));
            $_inserted->addParam($insert_id + $num_rows - 1);
            $message->addMessage($_inserted);
        }
    } elseif ($is_affected) {
        $message = PMA_Message::getMessageForAffectedRows($num_rows);

        // Ok, here is an explanation for the !$is_select.
        // The form generated by sql_query_form.lib.php
        // and db_sql.php has many submit buttons
        // on the same form, and some confusion arises from the
        // fact that $message_to_show is sent for every case.
        // The $message_to_show containing a success message and sent with
        // the form should not have priority over errors
    } elseif (! empty($message_to_show) && ! $is_select) {
        $message = PMA_Message::rawSuccess(htmlspecialchars($message_to_show));
    } elseif (! empty($GLOBALS['show_as_php'])) {
        $message = PMA_Message::success(__('Showing as PHP code'));
    } elseif (isset($GLOBALS['show_as_php'])) {
        /* User disable showing as PHP, query is only displayed */
        $message = PMA_Message::notice(__('Showing SQL query'));
    } elseif (! empty($GLOBALS['validatequery'])) {
        $message = PMA_Message::notice(__('Validated SQL'));
    } else {
        $message = PMA_Message::success(
            __('MySQL returned an empty result set (i.e. zero rows).')
        );
    }

    if (isset($GLOBALS['querytime'])) {
        $_querytime = PMA_Message::notice('(' . __('Query took %01.4f sec') . ')');
        $_querytime->addParam($GLOBALS['querytime']);
        $message->addMessage($_querytime);
    }

    if ($GLOBALS['is_ajax_request'] == true) {
        if ($cfg['ShowSQL']) {
            $extra_data['sql_query'] = PMA_Util::getMessage(
                $message, $GLOBALS['sql_query'], 'success'
            );
        }
        if (isset($GLOBALS['reload']) && $GLOBALS['reload'] == 1) {
            $extra_data['reload'] = 1;
            $extra_data['db'] = $GLOBALS['db'];
        }
        $response = PMA_Response::getInstance();
        $response->isSuccess($message->isSuccess());
        // No need to manually send the message
        // The Response class will handle that automatically
        $query__type = PMA_DisplayResults::QUERY_TYPE_SELECT;
        if ($analyzed_sql[0]['querytype'] == $query__type) {
            $createViewHTML = $displayResultsObject->getCreateViewQueryResultOp(
                $analyzed_sql
            );
            $response->addHTML($createViewHTML.'<br />');
        }

        $response->addJSON(isset($extra_data) ? $extra_data : array());
        if (empty($_REQUEST['ajax_page_request'])) {
            $response->addJSON('message', $message);
            exit;
        }
    }

    if ($is_gotofile) {
        $goto = PMA_securePath($goto);
        // Checks for a valid target script
        $is_db = $is_table = false;
        if (isset($_REQUEST['purge']) && $_REQUEST['purge'] == '1') {
            $table = '';
            unset($url_params['table']);
        }
        include 'libraries/db_table_exists.lib.php';

        if (strpos($goto, 'tbl_') === 0 && ! $is_table) {
            if (strlen($table)) {
                $table = '';
            }
            $goto = 'db_sql.php';
        }
        if (strpos($goto, 'db_') === 0 && ! $is_db) {
            if (strlen($db)) {
                $db = '';
            }
            $goto = 'index.php';
        }
        // Loads to target script
        if (strlen($goto) > 0) {
            $active_page = $goto;
            include '' . $goto;
        } else {
            // Echo at least one character to prevent showing last page from history
            echo " ";
        }

    } else {
        // avoid a redirect loop when last record was deleted
        if (0 == $num_rows && 'sql.php' == $cfg['DefaultTabTable']) {
            $goto = str_replace('sql.php', 'tbl_structure.php', $goto);
        }
        PMA_sendHeaderLocation(
            $cfg['PmaAbsoluteUri'] . str_replace('&amp;', '&', $goto)
            . '&message=' . urlencode($message)
        );
    } // end else
    exit();
    // end no rows returned
} else {
    $html_output='';
    // At least one row is returned -> displays a table with results
    //If we are retrieving the full value of a truncated field or the original
    // value of a transformed field, show it here and exit
    if ($GLOBALS['grid_edit'] == true) {
        $row = $GLOBALS['dbi']->fetchRow($result);
        $response = PMA_Response::getInstance();
        $response->addJSON('value', $row[0]);
        exit;
    }

    if (isset($_REQUEST['ajax_request']) && isset($_REQUEST['table_maintenance'])) {
        $response = PMA_Response::getInstance();
        $header   = $response->getHeader();
        $scripts  = $header->getScripts();
        $scripts->addFile('makegrid.js');
        $scripts->addFile('sql.js');

        // Gets the list of fields properties
        if (isset($result) && $result) {
            $fields_meta = $GLOBALS['dbi']->getFieldsMeta($result);
            $fields_cnt  = count($fields_meta);
        }

        if (empty($disp_mode)) {
            // see the "PMA_setDisplayMode()" function in
            // libraries/DisplayResults.class.php
            $disp_mode = 'urdr111101';
        }

        // hide edit and delete links for information_schema
        if ($GLOBALS['dbi']->isSystemSchema($db)) {
            $disp_mode = 'nnnn110111';
        }

        if (isset($message)) {
            $message = PMA_Message::success($message);
            $html_output .= PMA_Util::getMessage(
                $message, $GLOBALS['sql_query'], 'success'
            );
        }

        // Should be initialized these parameters before parsing
        $showtable = isset($showtable) ? $showtable : null;
        $printview = isset($_REQUEST['printview']) ? $_REQUEST['printview'] : null;
        $url_query = isset($url_query) ? $url_query : null;

        if (!empty($sql_data) && ($sql_data['valid_queries'] > 1)) {

            $_SESSION['is_multi_query'] = true;
            $html_output .= getTableHtmlForMultipleQueries(
                $displayResultsObject, $db, $sql_data, $goto,
                $pmaThemeImage, $text_dir, $printview, $url_query,
                $disp_mode, $sql_limit_to_append, false
            );
        } else {
            $_SESSION['is_multi_query'] = false;
            $displayResultsObject->setProperties(
                $unlim_num_rows, $fields_meta, $is_count, $is_export, $is_func,
                $is_analyse, $num_rows, $fields_cnt, $querytime, $pmaThemeImage,
                $text_dir, $is_maint, $is_explain, $is_show, $showtable,
                $printview, $url_query, false
            );

            $html_output .= $displayResultsObject->getTable(
                $result, $disp_mode, $analyzed_sql
            );
            $response = PMA_Response::getInstance();
            $response->addHTML($html_output);
            exit();
        }
    }

    // Displays the headers
    if (isset($show_query)) {
        unset($show_query);
    }
    if (isset($_REQUEST['printview']) && $_REQUEST['printview'] == '1') {
        PMA_Util::checkParameters(array('db', 'full_sql_query'));

        $response = PMA_Response::getInstance();
        $header = $response->getHeader();
        $header->enablePrintView();

        $html_output .= PMA_getHtmlForPrintViewHeader(
            $db, $full_sql_query, $num_rows
        );
    } else {
        $response = PMA_Response::getInstance();
        $header = $response->getHeader();
        $scripts = $header->getScripts();
        $scripts->addFile('makegrid.js');
        $scripts->addFile('sql.js');

        unset($message);

        if (! $GLOBALS['is_ajax_request']) {
            if (strlen($table)) {
                include 'libraries/tbl_common.inc.php';
                $url_query .= '&amp;goto=tbl_sql.php&amp;back=tbl_sql.php';
                include 'libraries/tbl_info.inc.php';
            } elseif (strlen($db)) {
                include 'libraries/db_common.inc.php';
                include 'libraries/db_info.inc.php';
            } else {
                include 'libraries/server_common.inc.php';
            }
        } else {
            //we don't need to buffer the output in getMessage here.
            //set a global variable and check against it in the function
            $GLOBALS['buffer_message'] = false;
        }
    }

    if (strlen($db)) {
        $cfgRelation = PMA_getRelationsParam();
    }

    // Gets the list of fields properties
    if (isset($result) && $result) {
        $fields_meta = $GLOBALS['dbi']->getFieldsMeta($result);
        $fields_cnt  = count($fields_meta);
    }

    //begin the sqlqueryresults div here. container div
    $html_output .= '<div id="sqlqueryresults"';
    $html_output .= ' class="ajax"';
    $html_output .= '>';

    // Display previous update query (from tbl_replace)
    if (isset($disp_query) && ($cfg['ShowSQL'] == true) && empty($sql_data)) {
        $html_output .= PMA_Util::getMessage($disp_message, $disp_query, 'success');
    }

    if (isset($profiling_results)) {
        // pma_token/url_query needed for chart export
        $token = $_SESSION[' PMA_token '];
        $url = (isset($url_query) ? $url_query : PMA_generate_common_url($db));

        $html_output .= PMA_getHtmlForProfilingChart(
            $url, $token, $profiling_results
        );
    }

    // Displays the results in a table
    if (empty($disp_mode)) {
        // see the "PMA_setDisplayMode()" function in
        // libraries/DisplayResults.class.php
        $disp_mode = 'urdr111101';
    }

    $has_unique = PMA_resultSetContainsUniqueKey(
        $db, $table, $fields_meta
    );

    // hide edit and delete links:
    // - for information_schema
    // - if the result set does not contain all the columns of a unique key
    //   and we are not just browing all the columns of an updatable view
    $updatableView
        = $justBrowsing
        && trim($analyzed_sql[0]['select_expr_clause']) == '*'
        && PMA_Table::isUpdatableView($db, $table);
    $editable = $has_unique || $updatableView;
    if (!empty($table) && ($GLOBALS['dbi']->isSystemSchema($db) || !$editable)) {
        $disp_mode = 'nnnn110111';
        $msg = PMA_message::notice(
            __(
                'Table %s does not contain a unique column.'
                . ' Grid edit, checkbox, Edit, Copy and Delete features'
                . ' are not available.'
            )
        );
        $msg->addParam($table);
        $html_output .= $msg->getDisplay();
    }

    if (isset($_GET['label'])) {
        $msg = PMA_message::success(__('Bookmark %s created'));
        $msg->addParam($_GET['label']);
        $html_output .= $msg->getDisplay();
    }

    // Should be initialized these parameters before parsing
    $showtable = isset($showtable) ? $showtable : null;
    $printview = isset($_REQUEST['printview']) ? $_REQUEST['printview'] : null;
    $url_query = isset($url_query) ? $url_query : null;

    if (! empty($sql_data) && ($sql_data['valid_queries'] > 1) || $is_procedure) {

        $_SESSION['is_multi_query'] = true;
        $html_output .= getTableHtmlForMultipleQueries(
            $displayResultsObject, $db, $sql_data, $goto,
            $pmaThemeImage, $text_dir, $printview, $url_query,
            $disp_mode, $sql_limit_to_append, $editable
        );
    } else {
        $_SESSION['is_multi_query'] = false;
        $displayResultsObject->setProperties(
            $unlim_num_rows, $fields_meta, $is_count, $is_export, $is_func,
            $is_analyse, $num_rows, $fields_cnt, $querytime, $pmaThemeImage,
            $text_dir, $is_maint, $is_explain, $is_show, $showtable,
            $printview, $url_query, $editable
        );

        $html_output .= $displayResultsObject->getTable(
            $result, $disp_mode, $analyzed_sql
        );
        $GLOBALS['dbi']->freeResult($result);
    }

    // BEGIN INDEX CHECK See if indexes should be checked.
    if (isset($query_type)
        && $query_type == 'check_tbl'
        && isset($selected)
        && is_array($selected)
    ) {
        foreach ($selected as $idx => $tbl_name) {
            $check = PMA_Index::findDuplicates($tbl_name, $db);
            if (! empty($check)) {
                $html_output .= sprintf(
                    __('Problems with indexes of table `%s`'), $tbl_name
                );
                $html_output .= $check;
            }
        }
    } // End INDEX CHECK

    // Bookmark support if required
    if ($disp_mode[7] == '1'
        && (! empty($cfg['Bookmark']) && empty($_GET['id_bookmark']))
        && ! empty($sql_query)
    ) {
        $html_output .= "\n";
        $goto = 'sql.php?'
              . PMA_generate_common_url($db, $table)
              . '&amp;sql_query=' . urlencode($sql_query)
              . '&amp;id_bookmark=1';
        $bkm_sql_query = urlencode(
            isset($complete_query) ? $complete_query : $sql_query
        );
        $html_output .= PMA_getHtmlForBookmark(
            $db, $goto, $bkm_sql_query, $cfg['Bookmark']['user']
        );
    } // end bookmark support

    // Do print the page if required
    if (isset($_REQUEST['printview']) && $_REQUEST['printview'] == '1') {
        $html_output .= PMA_Util::getButton();
    } // end print case
    $html_output .= '</div>'; // end sqlqueryresults div
    $response = PMA_Response::getInstance();
    $response->addHTML($html_output);
} // end rows returned

$_SESSION['is_multi_query'] = false;


if (! isset($_REQUEST['table_maintenance'])) {
    exit;
}

?>
