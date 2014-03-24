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
 * @param string $db         database
 * @param string $table      table
 * @param string $action     action
 * @param int    $num_fields number of fields
 * @param bool   $selected   selected
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
        . PMA_Util::showMySQLDocu('Storage_engines')
        . '</th>'
        . '<td width="25">&nbsp;</td>'
        . '<th>' . __('Collation:') . '</th>'
        . '</tr>'
        . '<tr><td><input type="text" name="comment" size="40" maxlength="80"'
        . ' value="'
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
            . PMA_Util::showMySQLDocu('Partitioning')
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
        . '" class="textfield" autofocus required />'
        . '</td>'
        . '<td>';
    $html .= sprintf(
        __('Add %s column(s)'), '<input type="text" id="added_fields" '
        . 'name="added_fields" size="2" value="1" onfocus="this.select'
        . '()" />'
    );

    $html .= '<input type="submit" name="submit_num_fields"'
        . 'value="' . __('Go') . '"'
        . ' onclick="return'
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
        . PMA_Util::showMySQLDocu('CREATE_TABLE') . '</caption>';

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
    $html .= PMA_URL_getHiddenInputs($form_params);

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
 * @param bool   $is_backup  whether backup or not
 * @param array  $columnMeta column meta data
 * @param bool   $mimework   whether mimework or not
 * @param string $db         current database
 * @param string $table      current table
 *
 * @return array
 */
function PMA_getHeaderCells($is_backup, $columnMeta, $mimework, $db, $table)
{
    $header_cells = array();
    $header_cells[] = __('Name');
    $header_cells[] = __('Type')
        . PMA_Util::showMySQLDocu('data-types');
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

    if (isset($columnMeta)) {
        $header_cells[] = __('Move column');
    }

    if ($mimework && $GLOBALS['cfg']['BrowseMIME']) {
        $header_cells[] = __('MIME type');
        $header_cells[] = '<a href="transformation_overview.php?'
            . PMA_URL_getCommon($db, $table)
            . '" title="' . __(
                'List of available transformations and their options'
            )
            . '" target="_blank">'
            . __('Browser transformation')
            . '</a>';
        $header_cells[] = __('Transformation options')
            . PMA_Util::showHint(
                __(
                    'Please enter the values for transformation options using this'
                    . ' format: \'a\', 100, b,\'c\'…<br />If you ever need to put'
                    . ' a backslash ("\") or a single quote ("\'") amongst those'
                    . ' values, precede it with a backslash (for example \'\\\\xyz\''
                    . ' or \'a\\\'b\').'
                )
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
 * Function to get row data for regenerating previous when error occurred.
 *
 * @param int   $columnNumber    column number
 * @param array $submit_fulltext submit full text
 *
 * @return array
 */
function PMA_getRowDataForRegeneration($columnNumber, $submit_fulltext)
{
    $columnMeta['Field'] = isset($_REQUEST['field_name'][$columnNumber])
        ? $_REQUEST['field_name'][$columnNumber]
        : false;
    $columnMeta['Type'] = isset($_REQUEST['field_type'][$columnNumber])
        ? $_REQUEST['field_type'][$columnNumber]
        : false;
    $columnMeta['Collation'] = isset($_REQUEST['field_collation'][$columnNumber])
        ? $_REQUEST['field_collation'][$columnNumber]
        : '';
    $columnMeta['Null'] = isset($_REQUEST['field_null'][$columnNumber])
        ? $_REQUEST['field_null'][$columnNumber]
        : '';

    $columnMeta['Key'] = '';
    if (isset($_REQUEST['field_key'][$columnNumber])) {
        $parts = explode('_', $_REQUEST['field_key'][$columnNumber], 2);
        if (count($parts) == 2 && $parts[1] == $columnNumber) {
            switch ($parts[0]) {
            case 'primary':
                $columnMeta['Key'] = 'PRI';
                break;
            case 'index':
                $columnMeta['Key'] = 'MUL';
                break;
            case 'unique':
                $columnMeta['Key'] = 'UNI';
                break;
            case 'fulltext':
                $columnMeta['Key'] = 'FULLTEXT';
                break;
            }
        }
    }

    // put None in the drop-down for Default, when someone adds a field
    $columnMeta['DefaultType']
        = isset($_REQUEST['field_default_type'][$columnNumber])
            ? $_REQUEST['field_default_type'][$columnNumber]
            : 'NONE';
    $columnMeta['DefaultValue']
        = isset($_REQUEST['field_default_value'][$columnNumber])
            ? $_REQUEST['field_default_value'][$columnNumber]
            : '';

    switch ($columnMeta['DefaultType']) {
    case 'NONE' :
        $columnMeta['Default'] = null;
        break;
    case 'USER_DEFINED' :
        $columnMeta['Default'] = $columnMeta['DefaultValue'];
        break;
    case 'NULL' :
    case 'CURRENT_TIMESTAMP' :
        $columnMeta['Default'] = $columnMeta['DefaultType'];
        break;
    }

    $columnMeta['Extra']
        = (isset($_REQUEST['field_extra'][$columnNumber])
        ? $_REQUEST['field_extra'][$columnNumber]
        : false);
    $columnMeta['Comment']
        = (isset($submit_fulltext[$columnNumber])
            && ($submit_fulltext[$columnNumber] == $columnNumber)
        ? 'FULLTEXT'
        : false);

    return $columnMeta;
}

/**
 * Function to get submit properties for regenerating previous when error occurred.
 *
 * @param int $columnNumber column number
 *
 * @return array
 */
function PMA_getSubmitPropertiesForRegeneration($columnNumber)
{
    $submit_length
        = (isset($_REQUEST['field_length'][$columnNumber])
        ? $_REQUEST['field_length'][$columnNumber]
        : false);
    $submit_attribute
        = (isset($_REQUEST['field_attribute'][$columnNumber])
        ? $_REQUEST['field_attribute'][$columnNumber]
        : false);

    $submit_default_current_timestamp
        = (isset($_REQUEST['field_default_current_timestamp'][$columnNumber])
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
 * @param int   $columnNumber    column number
 * @param array $submit_fulltext submit full text
 * @param array $comments_map    comments map
 * @param array $mime_map        mime map
 *
 * @return array
 */
function PMA_handleRegeneration($columnNumber, $submit_fulltext, $comments_map,
    $mime_map
) {
    $columnMeta = PMA_getRowDataForRegeneration(
        $columnNumber, isset($submit_fulltext) ? $submit_fulltext : null
    );

    list($submit_length, $submit_attribute, $submit_default_current_timestamp)
        = PMA_getSubmitPropertiesForRegeneration($columnNumber);

    if (isset($_REQUEST['field_comments'][$columnNumber])) {
        $comments_map[$columnMeta['Field']]
            = $_REQUEST['field_comments'][$columnNumber];
    }

    if (isset($_REQUEST['field_mimetype'][$columnNumber])) {
        $mime_map[$columnMeta['Field']]['mimetype']
            = $_REQUEST['field_mimetype'][$columnNumber];
    }

    if (isset($_REQUEST['field_transformation'][$columnNumber])) {
        $mime_map[$columnMeta['Field']]['transformation']
            = $_REQUEST['field_transformation'][$columnNumber];
    }

    if (isset($_REQUEST['field_transformation_options'][$columnNumber])) {
        $mime_map[$columnMeta['Field']]['transformation_options']
            = $_REQUEST['field_transformation_options'][$columnNumber];
    }

    return array(
        $columnMeta, $submit_length, $submit_attribute,
        $submit_default_current_timestamp, $comments_map, $mime_map
    );
}

/**
 * Function to get row data for $columnMeta set
 *
 * @param array $columnMeta column meta
 * @param bool  $isDefault  whether the row value is default
 *
 * @return array
 */
function PMA_getColumnMetaForDefault($columnMeta, $isDefault)
{
    switch ($columnMeta['Default']) {
    case null:
        if ($columnMeta['Null'] == 'YES') {
            $columnMeta['DefaultType']  = 'NULL';
            $columnMeta['DefaultValue'] = '';
            // SHOW FULL COLUMNS does not report the case
            // when there is a DEFAULT value which is empty so we need to use the
            // results of SHOW CREATE TABLE
        } elseif ($isDefault) {
            $columnMeta['DefaultType']  = 'USER_DEFINED';
            $columnMeta['DefaultValue'] = $columnMeta['Default'];
        } else {
            $columnMeta['DefaultType']  = 'NONE';
            $columnMeta['DefaultValue'] = '';
        }
        break;
    case 'CURRENT_TIMESTAMP':
        $columnMeta['DefaultType']  = 'CURRENT_TIMESTAMP';
        $columnMeta['DefaultValue'] = '';
        break;
    default:
        $columnMeta['DefaultType']  = 'USER_DEFINED';
        $columnMeta['DefaultValue'] = $columnMeta['Default'];
        break;
    }

    return $columnMeta;
}

/**
 * Function to get html for the column name
 *
 * @param int   $columnNumber column number
 * @param int   $ci           cell index
 * @param int   $ci_offset    cell index offset
 * @param array $columnMeta   column meta
 *
 * @return string
 */
function PMA_getHtmlForColumnName($columnNumber, $ci, $ci_offset, $columnMeta)
{
    $title = '';
    $title .= (isset($columnMeta['column_status']) && $columnMeta['column_status']['isReferenced']) ? __('Referenced by ')
        . implode(",", $columnMeta['column_status']['references']) . "." : '';
    $title .= (!empty($title) && isset($columnMeta['column_status']) && $columnMeta['column_status']['isForeignKey']) ? "\n" : '';
    $title .= (isset($columnMeta['column_status']) && $columnMeta['column_status']['isForeignKey']) ? __('Is a Foreign Key.') : '';
    $title .= (empty($title)) ? __('Column') : '';
    $html = '<input' . (isset($columnMeta['column_status']) && !$columnMeta['column_status']['isEditable']?' disabled="disabled" ':' ')
        . 'id="field_' . $columnNumber . '_' . ($ci - $ci_offset)
        . '"' . ' type="text" name="field_name[' . $columnNumber . ']"'
        . ' maxlength="64" class="textfield" title="' . $title . '"'
        . ' size="10"'
        . ' value="'
        . (isset($columnMeta['Field'])
            ? htmlspecialchars($columnMeta['Field']) : '')
        . '"' . ' />';

    return $html;
}

/**
 * Function to get html for the column type
 *
 * @param int    $columnNumber column number
 * @param int    $ci           cell index
 * @param int    $ci_offset    cell index offset
 * @param string $type_upper   type inuppercase
 *
 * @return string
 */
function PMA_getHtmlForColumnType($columnNumber, $ci, $ci_offset, $type_upper, $columnMeta)
{
    $select_id = 'field_' . $columnNumber . '_' . ($ci - $ci_offset);
    $html = '<select' . (isset($columnMeta['column_status']) && !$columnMeta['column_status']['isEditable']?' disabled="disabled" ':' ') . 'class="column_type" name="field_type[' .
        $columnNumber . ']"' . ' id="' . $select_id . '">';
    $html .= PMA_Util::getSupportedDatatypes(true, $type_upper);
    $html .= '    </select>';

    return $html;
}

/**
 * Function to get html for transformation option
 *
 * @param int   $columnNumber column number
 * @param int   $ci           cell index
 * @param int   $ci_offset    cell index offset
 * @param array $columnMeta   column meta
 * @param array $mime_map     mime map
 *
 * @return string
 */
function PMA_getHtmlForTransformationOption($columnNumber, $ci, $ci_offset,
    $columnMeta, $mime_map
) {
    $val = isset($columnMeta['Field'])
            && isset($mime_map[$columnMeta['Field']]['transformation_options'])
                ? htmlspecialchars(
                    $mime_map[$columnMeta['Field']]
                    ['transformation_options']
                )
                : '';

    $html = '<input id="field_' . $columnNumber . '_'
                . ($ci - $ci_offset) . '"' . ' type="text" '
                . 'name="field_transformation_options[' . $columnNumber . ']"'
                . ' size="16" class="textfield"'
                . ' value="' . $val . '"'
                . ' />';

    return $html;
}

/**
 * Function to get html for mime type
 *
 * @param int   $columnNumber   column number
 * @param int   $ci             cell index
 * @param int   $ci_offset      cell index offset
 * @param array $available_mime available mime
 * @param array $columnMeta     column meta
 * @param array $mime_map       mime map
 *
 * @return string
 */
function PMA_getHtmlForMimeType($columnNumber, $ci, $ci_offset,
    $available_mime, $columnMeta, $mime_map
) {
    $html = '<select id="field_' . $columnNumber . '_'
            . ($ci - $ci_offset)
            . '" size="1" name="field_mimetype[' . $columnNumber . ']">';
    $html .= '    <option value="">&nbsp;</option>';

    if (is_array($available_mime['mimetype'])) {
        foreach ($available_mime['mimetype'] as $mimetype) {
            $checked = (isset($columnMeta['Field'])
                && isset($mime_map[$columnMeta['Field']]['mimetype'])
                && ($mime_map[$columnMeta['Field']]['mimetype']
                    == str_replace('/', '_', $mimetype))
                ? 'selected '
                : '');
            $html .= '    <option value="'
                . str_replace('/', '_', $mimetype) . '" ' . $checked . '>'
                . htmlspecialchars($mimetype) . '</option>';
        }
    }

    $html .= '</select>';

    return $html;
}

/**
 * Function to get html for browser transformation
 *
 * @param int   $columnNumber   column number
 * @param int   $ci             cell index
 * @param int   $ci_offset      cell index offset
 * @param array $available_mime available mime
 * @param array $columnMeta     column meta
 * @param array $mime_map       mime map
 *
 * @return string
 */
function PMA_getHtmlForBrowserTransformation($columnNumber, $ci, $ci_offset,
    $available_mime, $columnMeta, $mime_map
) {
    $html = '<select id="field_' . $columnNumber . '_'
            . ($ci - $ci_offset) . '" size="1" name="field_transformation['
            . $columnNumber . ']">';
    $html .= '    <option value="" title="' . __('None')
            . '"></option>';
    if (is_array($available_mime['transformation'])) {
        foreach ($available_mime['transformation'] as $mimekey => $transform) {
            $checked = isset($columnMeta['Field'])
                && isset($mime_map[$columnMeta['Field']]['transformation'])
                && preg_match(
                    '@' . preg_quote(
                        $available_mime['transformation_file'][$mimekey]
                    ) . '3?@i',
                    $mime_map[$columnMeta['Field']]['transformation']
                )
                ? 'selected '
                : '';
            $tooltip = PMA_getTransformationDescription(
                $available_mime['transformation_file'][$mimekey], false
            );
            $html .= '<option value="'
                . $available_mime['transformation_file'][$mimekey] . '" '
                . $checked . ' title="' . htmlspecialchars($tooltip) . '">'
                . htmlspecialchars($transform) . '</option>';
        }
    }

    $html .= '</select>';

    return $html;
}

/**
 * Function to get html for move column
 *
 * @param int   $columnNumber column number
 * @param int   $ci           cell index
 * @param int   $ci_offset    cell index offset
 * @param array $move_columns move columns
 * @param array $columnMeta   column meta
 *
 * @return string
 */
function PMA_getHtmlForMoveColumn($columnNumber, $ci, $ci_offset, $move_columns,
    $columnMeta
) {
    $html = '<select id="field_' . $columnNumber . '_'
        . ($ci - $ci_offset) . '"' . ' name="field_move_to[' . $columnNumber
        . ']" size="1" width="5em">'
        . '<option value="" selected="selected">&nbsp;</option>';
    // find index of current column
    $current_index = 0;
    for ($mi = 0, $cols = count($move_columns); $mi < $cols; $mi++) {
        if ($move_columns[$mi]->name == $columnMeta['Field']) {
            $current_index = $mi;
            break;
        }
    }

    $html .= '<option value="-first"'
            . ($current_index == 0 ? ' disabled="disabled"' : '')
            . '>' . __('first') . '</option>';
    for ($mi = 0, $cols = count($move_columns); $mi < $cols; $mi++) {
        $html .=
            '<option value="' . htmlspecialchars($move_columns[$mi]->name) . '"'
            . (($current_index == $mi || $current_index == $mi + 1)
                ? ' disabled="disabled"'
                : '')
            . '>'
            . sprintf(
                __('after %s'),
                PMA_Util::backquote(
                    htmlspecialchars(
                        $move_columns[$mi]->name
                    )
                )
            )
            . '</option>';
    }

    $html .= '</select>';

    return $html;
}

/**
 * Function to get html for column comment
 *
 * @param int   $columnNumber column number
 * @param int   $ci           cell index
 * @param int   $ci_offset    cell index offset
 * @param array $columnMeta   column meta
 * @param array $comments_map comments map
 *
 * @return string
 */
function PMA_getHtmlForColumnComment($columnNumber, $ci, $ci_offset, $columnMeta,
    $comments_map
) {
    $html = '<input id="field_' . $columnNumber . '_' . ($ci - $ci_offset)
        . '"' . ' type="text" name="field_comments[' . $columnNumber
        . ']" size="12"'
        . ' value="' . (isset($columnMeta['Field'])
                && is_array($comments_map)
                && isset($comments_map[$columnMeta['Field']])
            ?  htmlspecialchars($comments_map[$columnMeta['Field']])
            : '') . '"'
        . ' class="textfield" />';

    return $html;
}

/**
 * Function get html for column auto increment
 *
 * @param int   $columnNumber column number
 * @param int   $ci           cell index
 * @param int   $ci_offset    cell index offset
 * @param array $columnMeta   column meta
 *
 * @return string
 */
function PMA_getHtmlForColumnAutoIncrement($columnNumber, $ci, $ci_offset,
    $columnMeta
) {
    $html = '<input name="field_extra[' . $columnNumber . ']"'
        . ' id="field_' . $columnNumber . '_' . ($ci - $ci_offset) . '"';
    if (isset($columnMeta['Extra'])
        && strtolower($columnMeta['Extra']) == 'auto_increment'
    ) {
        $html .= ' checked="checked"';
    }

    $html .= ' type="checkbox" value="AUTO_INCREMENT" />';

    return $html;
}

/**
 * Function to get html for the column indexes
 *
 * @param int   $columnNumber column number
 * @param int   $ci           cell index
 * @param int   $ci_offset    cell index offset
 * @param array $columnMeta   column meta
 *
 * @return string
 */
function PMA_getHtmlForColumnIndexes($columnNumber, $ci, $ci_offset, $columnMeta)
{
    $html = '<select name="field_key[' . $columnNumber . ']"'
        . ' id="field_' . $columnNumber . '_' . ($ci - $ci_offset) . '">';
    $html .= '<option value="none_' . $columnNumber . '">---</option>';

    $html .= PMA_getHtmlForIndexTypeOption(
        $columnNumber, $columnMeta, 'Primary', 'PRI'
    );
    $html .= PMA_getHtmlForIndexTypeOption(
        $columnNumber, $columnMeta, 'Unique', 'UNI'
    );
    $html .= PMA_getHtmlForIndexTypeOption(
        $columnNumber, $columnMeta, 'Index', 'MUL'
    );
    if (!PMA_DRIZZLE) {
        $html .= PMA_getHtmlForIndexTypeOption(
            $columnNumber, $columnMeta, 'Fulltext', 'FULLTEXT'
        );
    }

    $html .= '</select>';

    return $html;
}

/**
 * Function to get html for the index options
 *
 * @param int    $columnNumber column number
 * @param array  $columnMeta   column meta
 * @param string $type         index type
 * @param string $key          column meta key
 *
 * @return string
 */

function PMA_getHtmlForIndexTypeOption($columnNumber, $columnMeta, $type, $key)
{
    $html = '<option value="' . strtolower($type) . '_' . $columnNumber
        . '" title="'
        . __($type) . '"';
    if (isset($columnMeta['Key']) && $columnMeta['Key'] == $key) {
        $html .= ' selected="selected"';
    }
    $html .= '>' . strtoupper($type) . '</option>';

    return $html;
}


/**
 * Function to get html for column null
 *
 * @param int   $columnNumber column number
 * @param int   $ci           cell index
 * @param int   $ci_offset    cell index offset
 * @param array $columnMeta   column meta
 *
 * @return string
 */
function PMA_getHtmlForColumnNull($columnNumber, $ci, $ci_offset, $columnMeta)
{
    $html = '<input name="field_null[' . $columnNumber . ']"'
        . ' id="field_' . $columnNumber . '_' . ($ci - $ci_offset) . '"';
    if (! empty($columnMeta['Null'])
        && $columnMeta['Null'] != 'NO'
        && $columnMeta['Null'] != 'NOT NULL'
    ) {
        $html .= ' checked="checked"';
    }

    $html .= ' type="checkbox" value="NULL" class="allow_null"/>';

    return $html;
}

/**
 * Function to get html for column attribute
 *
 * @param int   $columnNumber                     column number
 * @param int   $ci                               cell index
 * @param int   $ci_offset                        cell index offset
 * @param array $extracted_columnspec             extracted column
 * @param array $columnMeta                       column meta
 * @param bool  $submit_attribute                 submit attribute
 * @param array $analyzed_sql                     analyzed sql
 * @param bool  $submit_default_current_timestamp submit default current time stamp
 *
 * @return string
 */
function PMA_getHtmlForColumnAttribute($columnNumber, $ci, $ci_offset,
    $extracted_columnspec, $columnMeta, $submit_attribute, $analyzed_sql,
    $submit_default_current_timestamp
) {
    $html = '<select style="font-size: 70%;"'
        . ' name="field_attribute[' . $columnNumber . ']"'
        . ' id="field_' . $columnNumber . '_' . ($ci - $ci_offset) . '">';

    $attribute     = '';
    if (isset($extracted_columnspec)) {
        $attribute = $extracted_columnspec['attribute'];
    }

    if (isset($columnMeta['Extra'])
        && $columnMeta['Extra'] == 'on update CURRENT_TIMESTAMP'
    ) {
        $attribute = 'on update CURRENT_TIMESTAMP';
    }

    if (isset($submit_attribute) && $submit_attribute != false) {
        $attribute = $submit_attribute;
    }

    // here, we have a TIMESTAMP that SHOW FULL COLUMNS reports as having the
    // NULL attribute, but SHOW CREATE TABLE says the contrary. Believe
    // the latter.
    $create_table_fields = $analyzed_sql[0]['create_table_fields'];
    if (PMA_MYSQL_INT_VERSION < 50025
        && isset($columnMeta['Field'])
        && isset($create_table_fields[$columnMeta['Field']]['type'])
        && $create_table_fields[$columnMeta['Field']]['type'] == 'TIMESTAMP'
        && $create_table_fields[$columnMeta['Field']]['timestamp_not_null'] == true
    ) {
        $columnMeta['Null'] = '';
    }

    // MySQL 4.1.2+ TIMESTAMP options
    // (if on_update_current_timestamp is set, then it's TRUE)
    if (isset($columnMeta['Field'])) {
        $field = $create_table_fields[$columnMeta['Field']];
    }

    if (isset($field)
        && isset($field['on_update_current_timestamp'])
    ) {
        $attribute = 'on update CURRENT_TIMESTAMP';
    }
    if ((isset($columnMeta['Field'])
        && isset($field['default_current_timestamp']))
        || (isset($submit_default_current_timestamp)
        && $submit_default_current_timestamp)
    ) {
        $default_current_timestamp = true;
    } else {
        $default_current_timestamp = false;
    }

    $attribute_types = $GLOBALS['PMA_Types']->getAttributes();
    $cnt_attribute_types = count($attribute_types);
    for ($j = 0; $j < $cnt_attribute_types; $j++) {
        $html
            .= '                <option value="' . $attribute_types[$j] . '"';
        if (strtoupper($attribute) == strtoupper($attribute_types[$j])) {
            $html .= ' selected="selected"';
        }
        $html .= '>' . $attribute_types[$j] . '</option>';
    }

    $html .= '</select>';

    return $html;
}

/**
 * Function to get html for column collation
 *
 * @param int   $columnNumber column number
 * @param int   $ci           cell index
 * @param int   $ci_offset    cell index offset
 * @param array $columnMeta   column meta
 *
 * @return string
 */
function PMA_getHtmlForColumnCollation($columnNumber, $ci, $ci_offset, $columnMeta)
{
    $tmp_collation
        = empty($columnMeta['Collation']) ? null : $columnMeta['Collation'];
    $html = PMA_generateCharsetDropdownBox(
        PMA_CSDROPDOWN_COLLATION, 'field_collation[' . $columnNumber . ']',
        'field_' . $columnNumber . '_' . ($ci - $ci_offset), $tmp_collation, false
    );

    return $html;
}

/**
 * Function get html for column length
 *
 * @param int $columnNumber             column number
 * @param int $ci                       cell index
 * @param int $ci_offset                cell index offset
 * @param int $length_values_input_size length values input size
 * @param int $length_to_display        length to disply
 *
 * @return string
 */
function PMA_getHtmlForColumnLength($columnNumber, $ci, $ci_offset,
    $length_values_input_size, $length_to_display
) {
    $html = '<input id="field_' . $columnNumber . '_' . ($ci - $ci_offset)
        . '"' . ' type="text" name="field_length[' . $columnNumber . ']" size="'
        . $length_values_input_size . '"' . ' value="' . htmlspecialchars(
            $length_to_display
        )
        . '"'
        . ' class="textfield" />'
        . '<p class="enum_notice" id="enum_notice_' . $columnNumber . '_'
        . ($ci - $ci_offset)
        . '">';
    $html .= '<a href="#" class="open_enum_editor"> '
        . __('Edit ENUM/SET values') . '</a>'
        . '</p>';

    return $html;
}

/**
 * Function to get html for the default column
 *
 * @param int    $columnNumber              column number
 * @param int    $ci                        cell index
 * @param int    $ci_offset                 cell index offset
 * @param string $type_upper                type upper
 * @param string $default_current_timestamp default current timestamp
 * @param array  $columnMeta                column meta
 *
 * @return string
 */
function PMA_getHtmlForColumnDefault($columnNumber, $ci, $ci_offset, $type_upper,
    $default_current_timestamp, $columnMeta
) {
    // here we put 'NONE' as the default value of drop-down; otherwise
    // users would have problems if they forget to enter the default
    // value (example, for an INT)
    $default_options = array(
        'NONE'              =>  _pgettext('for default', 'None'),
        'USER_DEFINED'      =>  __('As defined:'),
        'NULL'              => 'NULL',
        'CURRENT_TIMESTAMP' => 'CURRENT_TIMESTAMP',
    );

    // for a TIMESTAMP, do not show the string "CURRENT_TIMESTAMP" as a default
    // value
    if ($type_upper == 'TIMESTAMP'
        && ! empty($default_current_timestamp)
        && isset($columnMeta['Default'])
    ) {
        $columnMeta['Default'] = '';
    }

    if ($type_upper == 'BIT') {
        $columnMeta['DefaultValue']
            = PMA_Util::convertBitDefaultValue($columnMeta['DefaultValue']);
    }

    $html = '<select name="field_default_type[' . $columnNumber
        . ']" id="field_' . $columnNumber . '_' . ($ci - $ci_offset)
        . '" class="default_type">';
    foreach ($default_options as $key => $value) {
        $html .= '<option value="' . $key . '"';
        // is only set when we go back to edit a field's structure
        if (isset($columnMeta['DefaultType'])
            && $columnMeta['DefaultType'] == $key
        ) {
            $html .= ' selected="selected"';
        }
        $html .= ' >' . $value . '</option>';
    }
    $html .= '</select>';
    $html .= '<br />';
    $html .= '<input type="text"'
        . ' name="field_default_value[' . $columnNumber . ']" size="12"'
        . ' value="' . (isset($columnMeta['DefaultValue'])
            ? htmlspecialchars($columnMeta['DefaultValue'])
            : '') . '"'
        . ' class="textfield default_value" />';

    return $html;
}

/**
 * Function to get html for column attributes
 *
 * @param int    $columnNumber                     column number
 * @param array  $columnMeta                       column meta
 * @param string $type_upper                       type upper
 * @param int    $length_values_input_size         length values input size
 * @param int    $length                           length
 * @param string $default_current_timestamp        default current time stamp
 * @param array  $extracted_columnspec             extracted column spec
 * @param string $submit_attribute                 submit attribute
 * @param array  $analyzed_sql                     analyzed sql
 * @param string $submit_default_current_timestamp submit default current time stamp
 * @param array  $comments_map                     comments map
 * @param array  $fields_meta                      fields map
 * @param bool   $is_backup                        is backup
 * @param array  $move_columns                     move columns
 * @param array  $cfgRelation                      configuration relation
 * @param array  $available_mime                   available mime
 * @param array  $mime_map                         mime map
 *
 * @return array
 */
function PMA_getHtmlForColumnAttributes($columnNumber, $columnMeta, $type_upper,
    $length_values_input_size, $length, $default_current_timestamp,
    $extracted_columnspec, $submit_attribute, $analyzed_sql,
    $submit_default_current_timestamp, $comments_map, $fields_meta, $is_backup,
    $move_columns, $cfgRelation, $available_mime, $mime_map
) {
    // Cell index: If certain fields get left out, the counter shouldn't change.
    $ci = 0;
    // Everytime a cell shall be left out the STRG-jumping feature, $ci_offset
    // has to be incremented ($ci_offset++)
    $ci_offset = -1;

    $content_cell = array();

    // column name
    $content_cell[$ci] = PMA_getHtmlForColumnName(
        $columnNumber, $ci, $ci_offset, isset($columnMeta) ? $columnMeta : null
    );
    $ci++;

    // column type
    $content_cell[$ci] = PMA_getHtmlForColumnType(
        $columnNumber, $ci, $ci_offset, $type_upper, isset($columnMeta) ? $columnMeta : null
    );
    $ci++;

    // column length
    $content_cell[$ci] = PMA_getHtmlForColumnLength(
        $columnNumber, $ci, $ci_offset, $length_values_input_size, $length
    );
    $ci++;

    // column default
    $content_cell[$ci] = PMA_getHtmlForColumnDefault(
        $columnNumber, $ci, $ci_offset,
        isset($type_upper) ? $type_upper : null,
        isset($default_current_timestamp) ? $default_current_timestamp : null,
        isset($columnMeta) ? $columnMeta : null
    );
    $ci++;

    // column collation
    $content_cell[$ci] = PMA_getHtmlForColumnCollation(
        $columnNumber, $ci, $ci_offset, $columnMeta
    );
    $ci++;

    // column attribute
    $content_cell[$ci] = PMA_getHtmlForColumnAttribute(
        $columnNumber, $ci, $ci_offset,
        isset($extracted_columnspec) ? $extracted_columnspec : null,
        isset($columnMeta) ? $columnMeta : null,
        isset($submit_attribute) ? $submit_attribute : null,
        isset($analyzed_sql) ? $analyzed_sql : null,
        isset($submit_default_current_timestamp)
        ? $submit_default_current_timestamp : null
    );
    $ci++;

    // column NULL
    $content_cell[$ci] = PMA_getHtmlForColumnNull(
        $columnNumber, $ci, $ci_offset, isset($columnMeta) ? $columnMeta : null
    );
    $ci++;

    // column indexes
    // See my other comment about  this 'if'.
    if (!$is_backup) {
        $content_cell[$ci] = PMA_getHtmlForColumnIndexes(
            $columnNumber, $ci, $ci_offset, $columnMeta
        );
        $ci++;
    } // end if ($action ==...)

    // column auto_increment
    $content_cell[$ci] = PMA_getHtmlForColumnAutoIncrement(
        $columnNumber, $ci, $ci_offset, $columnMeta
    );
    $ci++;

    // column comments
    $content_cell[$ci] = PMA_getHtmlForColumnComment(
        $columnNumber, $ci, $ci_offset, isset($columnMeta) ? $columnMeta : null,
        $comments_map
    );
    $ci++;

    // move column
    if (isset($fields_meta)) {
        $content_cell[$ci] = PMA_getHtmlForMoveColumn(
            $columnNumber, $ci, $ci_offset, $move_columns, $columnMeta
        );
        $ci++;
    }

    if ($cfgRelation['mimework']
        && $GLOBALS['cfg']['BrowseMIME']
        && $cfgRelation['commwork']
    ) {
        // Column Mime-type
        $content_cell[$ci] = PMA_getHtmlForMimeType(
            $columnNumber, $ci, $ci_offset, $available_mime, $columnMeta, $mime_map
        );
        $ci++;

        // Column Browser transformation
        $content_cell[$ci] = PMA_getHtmlForBrowserTransformation(
            $columnNumber, $ci, $ci_offset, $available_mime, $columnMeta, $mime_map
        );
        $ci++;

        // column Transformation options
        $content_cell[$ci] = PMA_getHtmlForTransformationOption(
            $columnNumber, $ci, $ci_offset, isset($columnMeta) ? $columnMeta : null,
            isset($mime_map) ? $mime_map : null
        );
    }

    return $content_cell;
}

/**
 * Function to get form parameters for old column
 *
 * @param array $columnMeta   column meta
 * @param int   $length       length
 * @param array $form_params  form parameters
 * @param int   $columnNumber column/field number
 *
 * @return array
 */
function PMA_getFormParamsForOldColumn(
    $columnMeta, $length, $form_params, $columnNumber
) {
    if (isset($columnMeta['Field'])) {
        $form_params['field_orig[' . $columnNumber . ']']
            = $columnMeta['Field'];
    } else {
        $form_params['field_orig[' . $columnNumber . ']'] = '';
    }
    // old column length
    $form_params['field_length_orig[' . $columnNumber . ']'] = $length;

    // old column default
    $form_params['field_default_orig[' . $columnNumber . ']']
        = (isset($columnMeta['Default']) ? $columnMeta['Default'] : '');

    return $form_params;
}
?>
