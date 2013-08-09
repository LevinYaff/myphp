<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Display form for changing/adding table fields/columns.
 * Included by tbl_addfield.php and tbl_create.php
 *
 * @package PhpMyAdmin
 */
if (! defined('PHPMYADMIN')) {
    exit;
}

/**
 * Check parameters
 */
require_once './libraries/Util.class.php';

PMA_Util::checkParameters(array('db', 'table', 'action', 'num_fields'));


// Get available character sets and storage engines
require_once './libraries/mysql_charsets.inc.php';
require_once './libraries/StorageEngine.class.php';

/**
 * Class for partition management
 */
require_once './libraries/Partition.class.php';

require_once './libraries/tbl_columns_definition_form.lib.php';

/**
 * We are in transition between old-style echo and new-style PMA_Response
 * so this script generates $html and at the bottom, either echos it
 * or uses addHTML on it.
 *
 * Initialize $html in case this variable was used by a caller
 * (yes, this script should be refactored into functions)
 */
$html = '';

$length_values_input_size = 8;

$_form_params = PMA_getFormsParameters(
    $db, $table, $action, isset($num_fields) ? $num_fields : null,
    isset($selected) ? $selected : null
);

$is_backup = ($action != 'tbl_create.php' && $action != 'tbl_addfield.php');

require_once './libraries/transformations.lib.php';
$cfgRelation = PMA_getRelationsParam();

$comments_map = array();
$mime_map = array();
$available_mime = array();

$comments_map = PMA_getComments($db, $table);

if (isset($fields_meta)) {
    $move_columns = PMA_getMoveColumns($db, $table);
}

if ($cfgRelation['mimework'] && $GLOBALS['cfg']['BrowseMIME']) {
    $mime_map = PMA_getMIME($db, $table);
    $available_mime = PMA_getAvailableMIMEtypes();
}

$header_cells = PMA_getHeaderCells(
    $is_backup, isset($fields_meta) ? $fields_meta : null,
    $cfgRelation['mimework'], $db, $table
);

//  workaround for field_fulltext, because its submitted indices contain
//  the index as a value, not a key. Inserted here for easier maintaineance
//  and less code to change in existing files.
if (isset($field_fulltext) && is_array($field_fulltext)) {
    foreach ($field_fulltext as $fulltext_nr => $fulltext_indexkey) {
        $submit_fulltext[$fulltext_indexkey] = $fulltext_indexkey;
    }
}

for ($columnNumber = 0; $columnNumber < $num_fields; $columnNumber++) {
    if (! empty($regenerate)) {
        list($columnMeta, $submit_length, $submit_attribute,
            $submit_default_current_timestamp, $comments_map, $mime_map)
                = PMA_handleRegeneration(
                    isset($available_mime) ? $mime_map : null,
                    $comments_map, $mime_map
                ); 
    } elseif (isset($fields_meta[$columnNumber])) {
        $columnMeta = PMA_getColumnMetaForDefault(
            $fields_meta[$columnNumber],
            isset($analyzed_sql[0]['create_table_fields']
            [$fields_meta[$columnNumber]['Field']]['default_value'])
        );
    }

    if (isset($columnMeta['Type'])) {
        $extracted_columnspec = PMA_Util::extractColumnSpec($columnMeta['Type']);
        if ($extracted_columnspec['type'] == 'bit') {
            $columnMeta['Default']
                = PMA_Util::convertBitDefaultValue($columnMeta['Default']);
        }
    }
        
    if (empty($columnMeta['Type'])) {
        // creating a column
        $columnMeta['Type'] = '';
        $type        = '';
        $length = '';
    } else {
        $type = $extracted_columnspec['type'];
        $length = $extracted_columnspec['spec_in_brackets'];
    }

    // some types, for example longtext, are reported as
    // "longtext character set latin7" when their charset and / or collation
    // differs from the ones of the corresponding database.
    $tmp = strpos($type, 'character set');
    if ($tmp) {
        $type = substr($type, 0, $tmp - 1);
    }

    if (isset($submit_length) && $submit_length != false) {
        $length = $submit_length;
    }

    // rtrim the type, for cases like "float unsigned"
    $type = rtrim($type);
    $type_upper = strtoupper($type);


    // old column attributes
    if ($is_backup) {
        if (isset($columnMeta['Field'])) {
            $_form_params['field_orig[' . $columnNumber . ']']
                = $columnMeta['Field'];
        } else {
            $_form_params['field_orig[' . $columnNumber . ']'] = '';
        }
        // old column length
        $_form_params['field_length_orig[' . $columnNumber . ']'] = $length;
    
        // old column default
        $_form_params['field_default_orig[' . $columnNumber . ']']
            = (isset($columnMeta['Default']) ? $columnMeta['Default'] : '');
    }
    
    $content_cells[$columnNumber] = PMA_getHtmlForColumnAttributes(
        $columnNumber, isset($columnMeta) ? $columnMeta : null, $type_upper,
        $length_values_input_size, $length,
        isset($default_current_timestamp) ? $default_current_timestamp : null,
        isset($extracted_columnspec) ? $extracted_columnspec : null,
        isset($submit_attribute) ? $submit_attribute : null,
        isset($analyzed_sql) ? $analyzed_sql : null,
        isset($submit_default_current_timestamp)
        ? $submit_default_current_timestamp : null,
        $comments_map, isset($fields_meta) ? $fields_meta : null, $is_backup,
        isset($move_columns) ? $move_columns : null, $cfgRelation, $available_mime,
        $mime_map
    );
} // end for

/**
 * needs to be finished
 *
 *
if ($display_type == 'horizontal') {
    $new_field = '';
    foreach ($empty_row as $content_row_val) {
        $new_field .= '<td class="center">' . $content_row_val . '</td>';
    }
    ?>
<script type="text/javascript">
// <![CDATA[
var odd_row = <?php echo $odd_row; ?>;

function addField()
{
    var new_fields = document.getElementById('added_fields').value;
    var new_field_container = document.getElementById('table_columns');
    var new_field = '<?php echo preg_replace('|\s+|', ' ', preg_replace('|\'|', '\\\'', $new_field)); ?>';
    var i = 0;
    for (i = 0; i < new_fields; i++) {
        if (odd_row) {
            new_field_container.innerHTML += '<tr class="odd">' + new_field + '</tr>';
        } else {
            new_field_container.innerHTML += '<tr class="even">' + new_field + '</tr>';
        }
        odd_row = ! odd_row;
    }

    return true;
}
// ]]>
</script>
    <?php
}
 */

$html .= PMA_getHtmlForTableCreateOrAddField(
    $action, $_form_params, $content_cells, $header_cells
);

unset($_form_params);

PMA_Response::getInstance()->addHTML($html);
?>
