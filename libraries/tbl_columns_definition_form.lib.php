<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * set of functions used by tbl_columns_definitions_form.inc.php
 *
 * @package PhpMyAdmin
 */
if (!defined('PHPMYADMIN')) {
    exit;
}

/**
 * Function to get form parameters
 * 
 * @param string $db            database
 * @param string $table         table
 * @param string $action        action
 * @param int    $num_fields    number of fields
 * @param bool   $selected      selected
 * 
 * @return array $form_params form parameters
 */
function PMA_getFormsParameters($db, $table, $action, $num_fields, $selected)
{
    $form_params = array(
        'db' => $db
    );

    if ($action == 'tbl_create.php') {
        $form_params['reload'] = 1;
    } elseif ($action == 'tbl_addfield.php') {
        $form_params['field_where'] = $_REQUEST['field_where'];
        $form_params['after_field'] = $_REQUEST['after_field'];
        $form_params['table'] = $table;
    } else {
        $form_params['table'] = $table;
    }

    if (isset($num_fields)) {
        $form_params['orig_num_fields'] = $num_fields;
    }

    if (isset($_REQUEST['field_where'])) {
        $form_params['orig_field_where'] = $_REQUEST['field_where'];
    }

    if (isset($_REQUEST['after_field'])) {
        $form_params['orig_after_field'] = $_REQUEST['after_field'];
    }

    if (isset($selected) && is_array($selected)) {
        foreach ($selected as $o_fld_nr => $o_fld_val) {
            $form_params['selected[' . $o_fld_nr . ']'] = $o_fld_val;
        }
    }
    
    return $form_params;
}

/**
 * Function to get html for table comments, storage engine, collation and 
 * partition definition
 * 
 * @return string
 */
function PMA_getHtmlForTableConfigurations()
{
    $html = '<table>'
        . '<tr class="vtop">'
        . '<th>' . __('Table comments:') . '</th>'
        . '<td width="25">&nbsp;</td>'
        . '<th>' . __('Storage Engine:')
        . PMA_Util::showMySQLDocu('Storage_engines', 'Storage_engines')
        . '</th>'
        . '<td width="25">&nbsp;</td>'
        . '<th>' . __('Collation:') . '</th>'
        . '</tr>'
        . '<tr><td><input type="text" name="comment" size="40" maxlength="80"'
        . 'value="'
        . (isset($_REQUEST['comment'])
        ? htmlspecialchars($_REQUEST['comment'])
        : '')
        . '" class="textfield" />'
        . '</td>'
        . '<td width="25">&nbsp;</td>'
        . '<td>'
        . PMA_StorageEngine::getHtmlSelect(
            'tbl_storage_engine', null,
            (isset($_REQUEST['tbl_storage_engine'])
                ? $_REQUEST['tbl_storage_engine']
                : null
            )
        )
        . '</td>'
        . '<td width="25">&nbsp;</td>'
        . '<td>'
        . PMA_generateCharsetDropdownBox(
            PMA_CSDROPDOWN_COLLATION, 'tbl_collation', null,
            (isset($_REQUEST['tbl_collation'])
                ? $_REQUEST['tbl_collation']
                : null
            ),
            false, 3
        )
        . '</td>'
        . '</tr>';

    if (PMA_Partition::havePartitioning()) {
        $html .= '<tr class="vtop">'
            . '<th>' . __('PARTITION definition:') . '&nbsp;'
            . PMA_Util::showMySQLDocu('Partitioning', 'Partitioning')
            . '</th>'
            . '</tr>'
            . '<tr>'
            . '<td>'
            . '<textarea name="partition_definition" id="partitiondefinition"'
            . ' cols="' . $GLOBALS['cfg']['TextareaCols'] . '"'
            . ' rows="' . $GLOBALS['cfg']['TextareaRows'] . '"'
            . ' dir="' . $GLOBALS['text_dir'] . '">'
            . (isset($_REQUEST['partition_definition'])
                ? htmlspecialchars($_REQUEST['partition_definition'])
                : '')
            . '</textarea>'
            . '</td>'
            . '</tr>';
    }
    $html .= '</table>'
        . '<br />';
    
    return $html;
}

/**
 * Function to get html for the footer
 * 
 * @return string
 */
function PMA_getHtmlForFooter()
{
    $html = '<fieldset class="tblFooters">'
        . '<input type="submit" name="do_save_data" value="' . __('Save') . '" />'
        . '</fieldset>'
        . '<div id="properties_message"></div>'
        . '</form>';

    $html .= '<div id="popup_background"></div>';
    
    return $html;
}

/**
 * Function to get html for table create table name and number of fields
 * 
 * @return string
 */
function PMA_getHtmlForTableNameAndNoOfColumns()
{
    $html = '<table>'
        . '<tr class="vmiddle">'
        . '<td>' . __('Table name')
        . ':&nbsp;<input type="text" name="table" size="40" maxlength="80"'
        . ' value="'
        . (isset($_REQUEST['table']) ? htmlspecialchars($_REQUEST['table']) : '')
        . '" class="textfield" autofocus />'
        . '</td>'
        . '<td>';
    $html .= sprintf(
        __('Add %s column(s)'), '<input type="text" id="added_fields" '
        . 'name="added_fields" size="2" value="1" onfocus="this.select'
        . '()" />'
    );

    $html .= '<input type="submit" name="submit_num_fields"'
        . 'value="' . __('Go') . '"'
        . 'onclick="return'
        . ' checkFormElementInRange(this.form, \'added_fields\', \''
        . str_replace(
            '\'', '\\\'', __('You have to add at least one column.')
        ) . '\', 1)" />';
        
    $html .= '</td>'
        . '</tr>'
        . '</table>';
    
    return $html;
}

/**
 * Function to get html for table field definitions
 * 
 * @param array $header_cells  header cells
 * @param array $content_cells content cells
 * 
 * @return string
 */
function PMA_getHtmlForTableFieldDefinitions($header_cells, $content_cells)
{
    $html = '<table id="table_columns" class="noclick">';
    $html .= '<caption class="tblHeaders">' . __('Structure')
        . PMA_Util::showMySQLDocu('SQL-Syntax', 'CREATE_TABLE') . '</caption>';

    $html .= '<tr>';
    foreach ($header_cells as $header_val) {
        $html .= '<th>' . $header_val . '</th>';
    }
    $html .= '</tr>';

    $odd_row = true;
    foreach ($content_cells as $content_row) {
        $html .= '<tr class="' . ($odd_row ? 'odd' : 'even') . '">';
        $odd_row = ! $odd_row;

        if (is_array($content_row)) {
            foreach ($content_row as $content_row_val) {
                $html .= '<td class="center">' . $content_row_val . '</td>';
            }
        }
        $html .= '</tr>';
    }
    $html .= '</table>'
        . '<br />';
    
    return $html;
}

/**
 * Function to get html for the create table or field add view
 * 
 * @param string $action        action
 * @param array  $form_params   forms parameters
 * @param array  $content_cells content cells
 * @param array  $header_cells  header cells
 * 
 * @return string
 */
function PMA_getHtmlForTableCreateOrAddField($action, $form_params, $content_cells,
    $header_cells
) {    
    $html = '<form method="post" action="' . $action  . '" class="'
        . ($action == 'tbl_create.php' ? 'create_table' : 'append_fields')
        . '_form ajax">';
    $html .= PMA_generate_common_hidden_inputs($form_params);
    
    if ($action == 'tbl_create.php') {
        $html .= PMA_getHtmlForTableNameAndNoOfColumns();
    }
    
    if (is_array($content_cells) && is_array($header_cells)) {
        $html .= PMA_getHtmlForTableFieldDefinitions($header_cells, $content_cells);
    }
    
    if ($action == 'tbl_create.php') {
        $html .= PMA_getHtmlForTableConfigurations();
    }    
    
    $html .= PMA_getHtmlForFooter();

    return $html;
}

/**
 * Function to get header cells
 * 
 * @param bool   $is_backup   whether backup or not
 * @param array  $fields_meta fields meta data
 * @param bool   $mimework    whether mimework or not
 * @param string $db          current database
 * @param string $table       current table
 * 
 * @return string
 */
function PMA_getHeaderCells($is_backup, $fields_meta, $mimework, $db, $table)
{
    $header_cells = array();
    $header_cells[] = __('Name');
    $header_cells[] = __('Type')
        . PMA_Util::showMySQLDocu('SQL-Syntax', 'data-types');
    $header_cells[] = __('Length/Values')
        . PMA_Util::showHint(
            __(
                'If column type is "enum" or "set", please enter the values using'
                . ' this format: \'a\',\'b\',\'c\'…<br />If you ever need to put'
                . ' a backslash ("\") or a single quote ("\'") amongst those'
                . ' values, precede it with a backslash (for example \'\\\\xyz\''
                . ' or \'a\\\'b\').'
            )
        );
    $header_cells[] = __('Default')
        . PMA_Util::showHint(
            __(
                'For default values, please enter just a single value,'
                . ' without backslash escaping or quotes, using this format: a'
            )
        );
    $header_cells[] = __('Collation');
    $header_cells[] = __('Attributes');
    $header_cells[] = __('Null');
    
    // We could remove this 'if' and let the key information be shown and
    // editable. However, for this to work, structure.lib.php must be modified
    // to use the key fields, as tbl_addfield does.
    if (! $is_backup) {
        $header_cells[] = __('Index');
    }

    $header_cells[] = '<abbr title="AUTO_INCREMENT">A_I</abbr>';    
    $header_cells[] = __('Comments');
    
    if (isset($fields_meta)) {
        $header_cells[] = __('Move column');
    }
    
    if ($mimework && $GLOBALS['cfg']['BrowseMIME']) {        
        $hint = '<br />'
            . sprintf(
                __(
                    'For a list of available transformation options and their MIME'
                    . ' type transformations, click on'
                    . ' %stransformation descriptions%s'
                ),
                '<a href="transformation_overview.php?'
                . PMA_generate_common_url($db, $table)
                . '" target="_blank">',
                '</a>'
            );

        $header_cells[] = __('MIME type');
        $header_cells[] = __('Browser transformation');
        $header_cells[] = __('Transformation options')
            . PMA_Util::showHint(
                __(
                    'Please enter the values for transformation options using this'
                    . ' format: \'a\', 100, b,\'c\'…<br />If you ever need to put'
                    . ' a backslash ("\") or a single quote ("\'") amongst those'
                    . ' values, precede it with a backslash (for example \'\\\\xyz\''
                    . ' or \'a\\\'b\').'
                )
                . $hint
            );
    }
    
    return $header_cells;
}

/**
 * Function for moving, load all available column names
 * 
 * @param string $db    current database
 * @param string $table current table
 * 
 * @return array
 */
function PMA_getMoveColumns($db, $table)
{
    $move_columns_sql_query    = 'SELECT * FROM '
        . PMA_Util::backquote($db)
        . '.'
        . PMA_Util::backquote($table)
        . ' LIMIT 1';
    $move_columns_sql_result = $GLOBALS['dbi']->tryQuery($move_columns_sql_query);
    $move_columns = $GLOBALS['dbi']->getFieldsMeta($move_columns_sql_result);
    
    return $move_columns;
}

/**
 * Function to get row data for regenerating previous when error occured.
 * 
 * @param array $submit_fulltext submit full text
 * 
 * @return array
 */
function PMA_getRowDataForRegeneration($submit_fulltext)
{
    $row['Field'] = isset($_REQUEST['field_name'][$i])
        ? $_REQUEST['field_name'][$i]
        : false;
    $row['Type'] = isset($_REQUEST['field_type'][$i])
        ? $_REQUEST['field_type'][$i]
        : false;
    $row['Collation'] = isset($_REQUEST['field_collation'][$i])
        ? $_REQUEST['field_collation'][$i]
        : '';
    $row['Null'] = isset($_REQUEST['field_null'][$i])
        ? $_REQUEST['field_null'][$i]
        : '';

    if (isset($_REQUEST['field_key'][$i])
        && $_REQUEST['field_key'][$i] == 'primary_' . $i
    ) {
        $row['Key'] = 'PRI';
    } elseif (isset($_REQUEST['field_key'][$i])
        && $_REQUEST['field_key'][$i] == 'index_' . $i
    ) {
        $row['Key'] = 'MUL';
    } elseif (isset($_REQUEST['field_key'][$i])
        && $_REQUEST['field_key'][$i] == 'unique_' . $i
    ) {
        $row['Key'] = 'UNI';
    } elseif (isset($_REQUEST['field_key'][$i])
        && $_REQUEST['field_key'][$i] == 'fulltext_' . $i
    ) {
        $row['Key'] = 'FULLTEXT';
    } else {
        $row['Key'] = '';
    }

    // put None in the drop-down for Default, when someone adds a field
    $row['DefaultType'] = isset($_REQUEST['field_default_type'][$i])
        ? $_REQUEST['field_default_type'][$i]
        : 'NONE';
    $row['DefaultValue'] = isset($_REQUEST['field_default_value'][$i])
        ? $_REQUEST['field_default_value'][$i]
        : '';

    switch ($row['DefaultType']) {
    case 'NONE' :
        $row['Default'] = null;
        break;
    case 'USER_DEFINED' :
        $row['Default'] = $row['DefaultValue'];
        break;
    case 'NULL' :
    case 'CURRENT_TIMESTAMP' :
        $row['Default'] = $row['DefaultType'];
        break;
    }

    $row['Extra']
        = (isset($_REQUEST['field_extra'][$i])
        ? $_REQUEST['field_extra'][$i]
        : false);
    $row['Comment']
        = (isset($submit_fulltext[$i])
            && ($submit_fulltext[$i] == $i)
        ? 'FULLTEXT'
        : false);
    
    return $row;
}

/**
 * Function to get submit properties for regenerating previous when error occured.
 * 
 * @return array
 */
function PMA_getSubmitPropertiesForRegeneration()
{
    $submit_length
        = (isset($_REQUEST['field_length'][$i])
        ? $_REQUEST['field_length'][$i]
        : false);
    $submit_attribute
        = (isset($_REQUEST['field_attribute'][$i])
        ? $_REQUEST['field_attribute'][$i]
        : false);

    $submit_default_current_timestamp
        = (isset($_REQUEST['field_default_current_timestamp'][$i])
        ? true
        : false);
    
    return array(
        $submit_length, $submit_attribute, $submit_default_current_timestamp
    );
}

/**
 * An error happened with previous inputs, so we will restore the data
 * to embed it once again in this form.
 * 
 * @param array $submit_fulltext submit full text
 * @param array $comments_map    comments map
 * @param array $mime_map        mime map
 * 
 * @return array
 */
function PMA_handleRegeneration($submit_fulltext, $comments_map, $mime_map)
{
    $row = PMA_getRowDataForRegeneration(
        isset($submit_fulltext) ? $submit_fulltext : null
    );

    list($submit_length, $submit_attribute, $submit_default_current_timestamp)
        = PMA_getSubmitPropertiesForRegeneration();

    if (isset($_REQUEST['field_comments'][$i])) {
        $comments_map[$row['Field']] = $_REQUEST['field_comments'][$i];
    }

    if (isset($_REQUEST['field_mimetype'][$i])) {
        $mime_map[$row['Field']]['mimetype'] = $_REQUEST['field_mimetype'][$i];
    }

    if (isset($_REQUEST['field_transformation'][$i])) {
        $mime_map[$row['Field']]['transformation']
            = $_REQUEST['field_transformation'][$i];
    }

    if (isset($_REQUEST['field_transformation_options'][$i])) {
        $mime_map[$row['Field']]['transformation_options']
            = $_REQUEST['field_transformation_options'][$i];
    }
    
    return array(
        $row, $submit_length, $submit_attribute, $submit_default_current_timestamp,
        $comments_map, $mime_map
    );
}
?>
