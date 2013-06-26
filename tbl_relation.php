<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Display table relations for viewing and editing
 *
 * includes phpMyAdmin relations and InnoDB relations
 *
 * @todo fix name handling: currently names with dots (.) are not properly handled
 * for internal relations (but foreign keys relations are correct)
 * @todo foreign key constraints require both fields being of equal type and size
 * @todo check foreign fields to be from same type and size, all other makes no sense
 * @todo if above todos are fullfilled we can add all fields meet requirements
 * in the select dropdown
 * @package PhpMyAdmin
 */

/**
 * Gets some core libraries
 */
require_once 'libraries/common.inc.php';
require_once 'libraries/index.lib.php';
require_once 'libraries/tbl_relation.lib.php';

$response = PMA_Response::getInstance();

// Send table of column names to populate corresponding dropdowns depending
// on the current selection
if (isset($_REQUEST['getDropdownValues'])
    && $_REQUEST['getDropdownValues'] === 'true'
) {
    $foreignDb = $_REQUEST['foreignDb'];

    if (isset($_REQUEST['foreignTable'])) { // if both db and table are selected
        $foreignTable = $_REQUEST['foreignTable'];
        $table_obj = new PMA_Table($foreignTable, $foreignDb);
        $columns = array();
        foreach ($table_obj->getUniqueColumns(false, false) as $column) {
            $columns[] = htmlspecialchars($column);
        }
        $response->addJSON('columns', $columns);

    } else { // if only the db is selected
        $foreign = isset($_REQUEST['foreign']) && $_REQUEST['foreign'] === 'true';
        if ($foreign) {
            $query = 'SHOW TABLE STATUS FROM ' . PMA_Util::backquote($foreignDb);
            $tbl_storage_engine = strtoupper(
                PMA_Table::sGetStatusInfo(
                    $_REQUEST['db'],
                    $_REQUEST['table'],
                    'Engine'
                )
            );
        } else {
            $query = 'SHOW TABLES FROM ' . PMA_Util::backquote($foreignDb);
        }
        $tables_rs = $GLOBALS['dbi']->query(
            $query,
            null,
            PMA_DatabaseInterface::QUERY_STORE
        );
        $tables = array();
        while ($row = $GLOBALS['dbi']->fetchRow($tables_rs)) {
            if ($foreign) {
                if (isset($row[1])
                    && strtoupper($row[1]) == $tbl_storage_engine
                ) {
                    $tables[] = htmlspecialchars($row[0]);
                }
            } else {
                $tables[] = htmlspecialchars($row[0]);
            }
        }
        $response->addJSON('tables', $tables);
    }
    exit;
}

$header   = $response->getHeader();
$scripts  = $header->getScripts();
$scripts->addFile('tbl_relation.js');
$scripts->addFile('indexes.js');

/**
 * Sets globals from $_POST
 */
$post_params = array(
    'destination_db',
    'destination_table',
    'destination_column',
    'destination_foreign_db',
    'destination_foreign_table',
    'destination_foreign_column',
    'display_field',
    'fields_name',
    'on_delete',
    'on_update'
);

foreach ($post_params as $one_post_param) {
    if (isset($_POST[$one_post_param])) {
        $GLOBALS[$one_post_param] = $_POST[$one_post_param];
    }
}

/**
 * Gets tables informations
 */
require_once 'libraries/tbl_info.inc.php';

$options_array = array(
    'CASCADE'   => 'CASCADE',
    'SET_NULL'  => 'SET NULL',
    'NO_ACTION' => 'NO ACTION',
    'RESTRICT'  => 'RESTRICT',
);

/**
 * Gets the relation settings
 */
$cfgRelation = PMA_getRelationsParam();

/**
 * Updates
 */
if ($cfgRelation['relwork']) {
    $existrel = PMA_getForeigners($db, $table, '', 'internal');
}
if (PMA_Util::isForeignKeySupported($tbl_storage_engine)) {
    $existrel_foreign = PMA_getForeigners($db, $table, '', 'foreign');
}
if ($cfgRelation['displaywork']) {
    $disp     = PMA_getDisplayField($db, $table);
}

// will be used in the logic for internal relations and foreign keys:
$multi_edit_columns_name = isset($_REQUEST['fields_name'])
    ? $_REQUEST['fields_name']
    : null;

$html_output = '';

// u p d a t e s   f o r   I n t e r n a l    r e l a t i o n s
if (isset($destination_db) && $cfgRelation['relwork']) {

    foreach ($destination_db as $master_field_md5 => $foreign_db) {
        $upd_query = false;

        // Map the fieldname's md5 back to its real name
        $master_field = $multi_edit_columns_name[$master_field_md5];

        $foreign_table = $destination_table[$master_field_md5];
        $foreign_field = $destination_column[$master_field_md5];
        if (! empty($foreign_db)
            && ! empty($foreign_table)
            && ! empty($foreign_field)
        ) {
            if (! isset($existrel[$master_field])) {
                $upd_query  = 'INSERT INTO '
                    . PMA_Util::backquote($GLOBALS['cfgRelation']['db'])
                    . '.' . PMA_Util::backquote($cfgRelation['relation'])
                    . '(master_db, master_table, master_field, foreign_db,'
                    . ' foreign_table, foreign_field)'
                    . ' values('
                    . '\'' . PMA_Util::sqlAddSlashes($db) . '\', '
                    . '\'' . PMA_Util::sqlAddSlashes($table) . '\', '
                    . '\'' . PMA_Util::sqlAddSlashes($master_field) . '\', '
                    . '\'' . PMA_Util::sqlAddSlashes($foreign_db) . '\', '
                    . '\'' . PMA_Util::sqlAddSlashes($foreign_table) . '\','
                    . '\'' . PMA_Util::sqlAddSlashes($foreign_field) . '\')';

            } elseif ($existrel[$master_field]['foreign_db'] != $foreign_db
                || $existrel[$master_field]['foreign_table'] != $foreign_table
                || $existrel[$master_field]['foreign_field'] != $foreign_field
            ) {
                $upd_query  = 'UPDATE '
                    . PMA_Util::backquote($GLOBALS['cfgRelation']['db'])
                    . '.' . PMA_Util::backquote($cfgRelation['relation']) . ' SET'
                    . ' foreign_db       = \''
                    . PMA_Util::sqlAddSlashes($foreign_db) . '\', '
                    . ' foreign_table    = \''
                    . PMA_Util::sqlAddSlashes($foreign_table) . '\', '
                    . ' foreign_field    = \''
                    . PMA_Util::sqlAddSlashes($foreign_field) . '\' '
                    . ' WHERE master_db  = \''
                    . PMA_Util::sqlAddSlashes($db) . '\''
                    . ' AND master_table = \''
                    . PMA_Util::sqlAddSlashes($table) . '\''
                    . ' AND master_field = \''
                    . PMA_Util::sqlAddSlashes($master_field) . '\'';
            } // end if... else....
        } elseif (isset($existrel[$master_field])) {
            $upd_query = 'DELETE FROM '
                . PMA_Util::backquote($GLOBALS['cfgRelation']['db'])
                . '.' . PMA_Util::backquote($cfgRelation['relation'])
                . ' WHERE master_db  = \'' . PMA_Util::sqlAddSlashes($db) . '\''
                . ' AND master_table = \'' . PMA_Util::sqlAddSlashes($table) . '\''
                . ' AND master_field = \'' . PMA_Util::sqlAddSlashes($master_field)
                . '\'';
        } // end if... else....
        if ($upd_query) {
            PMA_queryAsControlUser($upd_query);
        }
    } // end while
} // end if (updates for internal relations)

// u p d a t e s    f o r    f o r e i g n    k e y s
// (for now, one index name only; we keep the definitions if the
// foreign db is not the same)

if (isset($destination_foreign_db)) {
    $display_query = '';
    $seen_error = false;
    foreach ($destination_foreign_db as $master_field_md5 => $foreign_db) {
        $create = false;
        $drop = false;

        // Map the fieldname's md5 back to it's real name
        $master_field = $multi_edit_columns_name[$master_field_md5];

        $foreign_table = $destination_foreign_table[$master_field_md5];
        $foreign_field = $destination_foreign_column[$master_field_md5];
        if (! empty($foreign_db)
            && ! empty($foreign_table)
            && ! empty($foreign_field)
        ) {
            if (! isset($existrel_foreign[$master_field])) {
                // no key defined for this field
                $create = true;
            } elseif ($existrel_foreign[$master_field]['foreign_db'] != $foreign_db
                || $existrel_foreign[$master_field]['foreign_table'] != $foreign_table
                || $existrel_foreign[$master_field]['foreign_field'] != $foreign_field
                || $_REQUEST['constraint_name'][$master_field_md5] != $existrel_foreign[$master_field]['constraint']
                || ($_REQUEST['on_delete'][$master_field_md5] != (! empty($existrel_foreign[$master_field]['on_delete']) ? $existrel_foreign[$master_field]['on_delete'] : 'RESTRICT'))
                || ($_REQUEST['on_update'][$master_field_md5] != (! empty($existrel_foreign[$master_field]['on_update']) ? $existrel_foreign[$master_field]['on_update'] : 'RESTRICT'))
            ) {
                // another foreign key is already defined for this field
                // or an option has been changed for ON DELETE or ON UPDATE
                $drop = true;
                $create = true;
            } // end if... else....
        } elseif (isset($existrel_foreign[$master_field])) {
            $drop = true;
        } // end if... else....

        $tmp_error_drop = false;
        if ($drop) {
            $drop_query = PMA_getSQLToDropForeignKey(
                $table, $existrel_foreign[$master_field]['constraint']
            );
            $display_query .= $drop_query . "\n";
            $GLOBALS['dbi']->tryQuery($drop_query);
            $tmp_error_drop = $GLOBALS['dbi']->getError();

            if (! empty($tmp_error_drop)) {
                $seen_error = true;
                $html_output .= PMA_Util::mysqlDie(
                    $tmp_error_drop, $drop_query, false, '', false
                );
                continue;
            }
        }
        $tmp_error_create = false;
        if ($create) {
            $create_query = PMA_getSQLToCreateForeignKey(
                $table, $master_field, $foreign_db, $foreign_table, $foreign_field,
                $_REQUEST['constraint_name'][$master_field_md5],
                $options_array[$_REQUEST['on_delete'][$master_field_md5]],
                $options_array[$_REQUEST['on_update'][$master_field_md5]]
            );

            $display_query .= $create_query . "\n";
            $GLOBALS['dbi']->tryQuery($create_query);
            $tmp_error_create = $GLOBALS['dbi']->getError();
            if (! empty($tmp_error_create)) {
                $seen_error = true;

                if (substr($tmp_error_create, 1, 4) == '1005') {
                    $message = PMA_Message::error(
                        __('Error creating foreign key on %1$s (check data types)')
                    );
                    $message->addParam($master_field);
                    $message->display();
                } else {
                    $html_output .= PMA_Util::mysqlDie(
                        $tmp_error_create, $create_query, false, '', false
                    );
                }
                $html_output .= PMA_Util::showMySQLDocu(
                    'manual_Table_types', 'InnoDB_foreign_key_constraints'
                ) . "\n";
            }

            // this is an alteration and the old constraint has been dropped
            // without creation of a new one
            if ($drop && $create && empty($tmp_error_drop)
                && ! empty($tmp_error_create)
            ) {
                // a rollback may be better here
                $sql_query_recreate = '# Restoring the dropped constraint...' . "\n";
                $sql_query_recreate .= PMA_getSQLToCreateForeignKey(
                    $table,
                    $master_field,
                    $existrel_foreign[$master_field]['foreign_db'],
                    $existrel_foreign[$master_field]['foreign_table'],
                    $existrel_foreign[$master_field]['foreign_field'],
                    $existrel_foreign[$master_field]['constraint'],
                    $options_array[$existrel_foreign[$master_field]['on_delete']],
                    $options_array[$existrel_foreign[$master_field]['on_update']]
                );
                $display_query .= $sql_query_recreate . "\n";
                $GLOBALS['dbi']->tryQuery($sql_query_recreate);
            }
        }
    } // end foreach
    if (! empty($display_query) && ! $seen_error) {
        $html_output .= PMA_Util::getMessage(
            __('Your SQL query has been executed successfully'),
            null, 'success'
        );
    }
} // end if isset($destination_foreign)


// U p d a t e s   f o r   d i s p l a y   f i e l d

if ($cfgRelation['displaywork'] && isset($display_field)) {
    $upd_query = false;
    if ($disp) {
        if ($display_field != '') {
            $upd_query = 'UPDATE '
                . PMA_Util::backquote($GLOBALS['cfgRelation']['db'])
                . '.' . PMA_Util::backquote($cfgRelation['table_info'])
                . ' SET display_field = \''
                . PMA_Util::sqlAddSlashes($display_field) . '\''
                . ' WHERE db_name  = \'' . PMA_Util::sqlAddSlashes($db) . '\''
                . ' AND table_name = \'' . PMA_Util::sqlAddSlashes($table) . '\'';
        } else {
            $upd_query = 'DELETE FROM '
                . PMA_Util::backquote($GLOBALS['cfgRelation']['db'])
                . '.' . PMA_Util::backquote($cfgRelation['table_info'])
                . ' WHERE db_name  = \'' . PMA_Util::sqlAddSlashes($db) . '\''
                . ' AND table_name = \'' . PMA_Util::sqlAddSlashes($table) . '\'';
        }
    } elseif ($display_field != '') {
        $upd_query = 'INSERT INTO '
            . PMA_Util::backquote($GLOBALS['cfgRelation']['db'])
            . '.' . PMA_Util::backquote($cfgRelation['table_info'])
            . '(db_name, table_name, display_field) VALUES('
            . '\'' . PMA_Util::sqlAddSlashes($db) . '\','
            . '\'' . PMA_Util::sqlAddSlashes($table) . '\','
            . '\'' . PMA_Util::sqlAddSlashes($display_field) . '\')';
    }

    if ($upd_query) {
        PMA_queryAsControlUser($upd_query);
    }
} // end if

// If we did an update, refresh our data
if (isset($destination_db) && $cfgRelation['relwork']) {
    $existrel = PMA_getForeigners($db, $table, '', 'internal');
}
if (isset($destination_foreign_db)
    && PMA_Util::isForeignKeySupported($tbl_storage_engine)
) {
    $existrel_foreign = PMA_getForeigners($db, $table, '', 'foreign');
}

if ($cfgRelation['displaywork']) {
    $disp     = PMA_getDisplayField($db, $table);
}


/**
 * Dialog
 */

// common form
$html_output .= '<form method="post" action="tbl_relation.php">' . "\n"
    . PMA_generate_common_hidden_inputs($db, $table);

// Now find out the columns of our $table
// need to use PMA_DatabaseInterface::QUERY_STORE with $GLOBALS['dbi']->numRows() in mysqli
$columns = $GLOBALS['dbi']->getColumns($db, $table);

if (count($columns) > 0) {

    foreach ($columns as $row) {
        $save_row[] = $row;
    }

    $saved_row_cnt  = count($save_row);
    $html_output .= '<fieldset>'
        . '<legend>' . __('Relations'). '</legend>'
        . '<table id="relationalTable">'
        . '<tr><th>' . __('Column') . '</th>';

    if ($cfgRelation['relwork']) {
        $html_output .= '<th>' . __('Internal relation');
        if (PMA_Util::isForeignKeySupported($tbl_storage_engine)) {
            $html_output .= PMA_Util::showHint(
                __(
                    'An internal relation is not necessary when a corresponding'
                    . ' FOREIGN KEY relation exists.'
                )
            );
        }
        $html_output .= '</th>';
    }

    if (PMA_Util::isForeignKeySupported($tbl_storage_engine)) {
        // this does not have to be translated, it's part of the MySQL syntax
        $html_output .= '<th colspan="2">' . __('Foreign key constraint')
            . ' (' . $tbl_storage_engine . ')';
        $html_output .= '</th>';
    }
    $html_output .= '</tr>';

    $odd_row = true;
    for ($i = 0; $i < $saved_row_cnt; $i++) {
        $myfield = $save_row[$i]['Field'];
        // Use an md5 as array index to avoid having special characters
        // in the name atttibure (see bug #1746964 )
        $myfield_md5 = md5($myfield);
        $myfield_html = htmlspecialchars($myfield);

        $html_output .= '<tr class="' . ($odd_row ? 'odd' : 'even') . '">'
            . '<td class="center">'
            . '<strong>' . $myfield_html . '</strong>'
            . '<input type="hidden" name="fields_name[' . $myfield_md5 . ']"'
            . ' value="' . $myfield_html . '"/>'
            . '</td>';
        $odd_row = ! $odd_row;

        if ($cfgRelation['relwork']) {
            $html_output .= '<td>';

            $foreign_db = false;
            $foreign_table = false;
            $foreign_column = false;

            // database dropdown
            if (isset($existrel[$myfield])) {
                $foreign_db = $existrel[$myfield]['foreign_db'];
            } else {
                $foreign_db = $db;
            }
            $html_output .= PMA_generateRelationalDropdown(
                'destination_db[' . $myfield_md5 . ']',
                $GLOBALS['pma']->databases,
                $foreign_db
            );
            // end of database dropdown

            // table dropdown
            $tables = array();
            if ($foreign_db) {
                if (isset($existrel[$myfield])) {
                    $foreign_table = $existrel[$myfield]['foreign_table'];
                }
                $tables_rs = $GLOBALS['dbi']->query(
                    'SHOW TABLES FROM ' . PMA_Util::backquote($foreign_db),
                    null,
                    PMA_DatabaseInterface::QUERY_STORE
                );
                while ($row = $GLOBALS['dbi']->fetchRow($tables_rs)) {
                    $tables[] = $row[0];
                }
            }
            $html_output .= PMA_generateRelationalDropdown(
                'destination_table[' . $myfield_md5 . ']',
                $tables,
                $foreign_table
            );
            // end of table dropdown

            // column dropdown
            $columns = array();
            if ($foreign_db && $foreign_table) {
                if (isset($existrel[$myfield])) {
                    $foreign_column = $existrel[$myfield]['foreign_field'];
                }
                $table_obj = new PMA_Table($foreign_table, $foreign_db);
                $columns = $table_obj->getUniqueColumns(false, false);
            }
            $html_output .= PMA_generateRelationalDropdown(
                'destination_column[' . $myfield_md5 . ']',
                $columns,
                $foreign_column
            );
            // end of column dropdown

            $html_output .= '</td>';
        } // end if (internal relations)

        if (PMA_Util::isForeignKeySupported($tbl_storage_engine)) {
            $html_output .= '<td>';
            if (! empty($save_row[$i]['Key'])) {

                $foreign_db = false;
                $foreign_table = false;
                $foreign_column = false;

                // foreign database dropdown
                if (isset($existrel_foreign[$myfield])) {
                    $foreign_db = $existrel_foreign[$myfield]['foreign_db'];
                } else {
                    $foreign_db = $db;
                }
                $html_output .= '<span class="formelement clearfloat">';
                $html_output .= PMA_generateRelationalDropdown(
                    'destination_foreign_db[' . $myfield_md5 . ']',
                    $GLOBALS['pma']->databases,
                    $foreign_db
                );
                // end of foreign database dropdown

                // foreign table dropdown
                $tables = array();
                if ($foreign_db) {
                    if (isset($existrel_foreign[$myfield])) {
                        $foreign_table = $existrel_foreign[$myfield]['foreign_table'];
                    }
                    $tables_rs = $GLOBALS['dbi']->query(
                        'SHOW TABLE STATUS FROM ' . PMA_Util::backquote($foreign_db),
                        null,
                        PMA_DatabaseInterface::QUERY_STORE
                    );
                    while ($row = $GLOBALS['dbi']->fetchRow($tables_rs)) {
                        if (isset($row[1])
                            && strtoupper($row[1]) == $tbl_storage_engine
                        ) {
                            $tables[] = $row[0];
                        }
                    }
                }
                $html_output .= PMA_generateRelationalDropdown(
                    'destination_foreign_table[' . $myfield_md5 . ']',
                    $tables,
                    $foreign_table
                );
                // end of foreign table dropdown

                // foreign column dropdown
                $columns = array();
                if ($foreign_db && $foreign_table) {
                    if (isset($existrel_foreign[$myfield])) {
                        $foreign_column = $existrel_foreign[$myfield]['foreign_field'];
                    }
                    $table_obj = new PMA_Table($foreign_table, $foreign_db);
                    $columns = $table_obj->getUniqueColumns(false, false);
                }
                $html_output .= PMA_generateRelationalDropdown(
                    'destination_foreign_column[' . $myfield_md5 . ']',
                    $columns,
                    $foreign_column
                );
                $html_output .= '</span>';
                // end of foreign column dropdown

                // For constraint name
                $html_output .= '<span class="formelement clearfloat">';
                $constraint_name = isset($existrel_foreign[$myfield]['constraint'])
                    ? $existrel_foreign[$myfield]['constraint'] : '';
                $html_output .= __('Constraint name');
                $html_output .= '<input type="text" name="constraint_name['
                    . $myfield_md5 . ']"'
                    . ' value="' . $constraint_name . '"/>';
                $html_output .= '</span>' . "\n";

                $html_output .= '<span class="formelement clearfloat">';
                // For ON DELETE and ON UPDATE, the default action
                // is RESTRICT as per MySQL doc; however, a SHOW CREATE TABLE
                // won't display the clause if it's set as RESTRICT.
                $on_delete = isset($existrel_foreign[$myfield]['on_delete'])
                    ? $existrel_foreign[$myfield]['on_delete'] : 'RESTRICT';
                $html_output .= PMA_generateDropdown(
                    'ON DELETE',
                    'on_delete[' . $myfield_md5 . ']',
                    $options_array,
                    $on_delete
                );
                $html_output .= '</span>' . "\n";

                $html_output .= '<span class="formelement clearfloat">' . "\n";
                $on_update = isset($existrel_foreign[$myfield]['on_update'])
                    ? $existrel_foreign[$myfield]['on_update'] : 'RESTRICT';
                $html_output .= PMA_generateDropdown(
                    'ON UPDATE',
                    'on_update[' . $myfield_md5 . ']',
                    $options_array,
                    $on_update
                );
                $html_output .= '</span>' . "\n";
            } else {
                $html_output .= __('No index defined! Create one below');
            } // end if (a key exists)
            $html_output .= '</td>';
        } // end if (InnoDB)
        $html_output .= '</tr>';
    } // end for

    unset( $myfield, $myfield_md5, $myfield_html);
    $html_output .= '</table>' . "\n"
        . '</fieldset>' . "\n";

    if ($cfgRelation['displaywork']) {
        // Get "display_field" infos
        $disp = PMA_getDisplayField($db, $table);
        $html_output .= '<fieldset>'
            . '<label>' . __('Choose column to display:') . '</label>'
            . '<select name="display_field">'
            . '<option value="">---</option>';

        foreach ($save_row as $row) {
            $html_output .= '<option value="'
                . htmlspecialchars($row['Field']) . '"';
            if (isset($disp) && $row['Field'] == $disp) {
                $html_output .= ' selected="selected"';
            }
            $html_output .= '>' . htmlspecialchars($row['Field'])
                . '</option>'. "\n";
        } // end while

        $html_output .= '</select>'
            . '</fieldset>';
    } // end if (displayworks)

    $html_output .= '<fieldset class="tblFooters">'
        . '<input type="submit" value="' . __('Save') . '" />'
        . '</fieldset>'
        . '</form>';
} // end if (we have columns in this table)

if (PMA_Util::isForeignKeySupported($tbl_storage_engine)) {
    $html_output .= '<div id="index_div" class="ajax" >'
        . PMA_getHtmlForDisplayIndexes();
}
// Render HTML output
PMA_Response::getInstance()->addHTML($html_output);
?>
