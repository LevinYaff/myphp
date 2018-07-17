<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Database SQL executor
 *
 * @package PhpMyAdmin
 */
declare(strict_types=1);

use PhpMyAdmin\Config\PageSettings;
use PhpMyAdmin\Response;
use PhpMyAdmin\SqlQueryForm;

/**
 *
 */
require_once 'libraries/common.inc.php';

PageSettings::showGroup('Sql');

/**
 * Runs common work
 */
$response = Response::getInstance();
$header   = $response->getHeader();
$scripts  = $header->getScripts();
$scripts->addFile('sql');

require 'libraries/db_common.inc.php';

$sqlQueryForm = new SqlQueryForm();

// After a syntax error, we return to this script
// with the typed query in the textarea.
$goto = 'db_sql.php';
$back = 'db_sql.php';

/**
 * Query box, bookmark, insert data from textfile
 */
$response->addHTML(
    $sqlQueryForm->getHtml(
        true,
        false,
        isset($_REQUEST['delimiter'])
        ? htmlspecialchars($_REQUEST['delimiter'])
        : ';'
    )
);
