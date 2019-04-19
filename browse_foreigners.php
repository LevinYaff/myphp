<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * display selection for relational field values
 *
 * @package PhpMyAdmin
 */
declare(strict_types=1);

use PhpMyAdmin\BrowseForeigners;
use PhpMyAdmin\Controllers\BrowseForeignersController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Di\Container;
use PhpMyAdmin\Relation;
use PhpMyAdmin\Response;
use PhpMyAdmin\Util;

if (! defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
}

require_once ROOT_PATH . 'libraries/common.inc.php';

Util::checkParameters(['db', 'table', 'field'], true);

$container = Container::getDefaultContainer();
$container->set(Response::class, Response::getInstance());

/** @var Response $response */
$response = $container->get(Response::class);

/** @var DatabaseInterface $dbi */
$dbi = $container->get(DatabaseInterface::class);

$controller = new BrowseForeignersController(
    $response,
    $dbi,
    new BrowseForeigners(
        $GLOBALS['cfg']['LimitChars'],
        $GLOBALS['cfg']['MaxRows'],
        $GLOBALS['cfg']['RepeatCells'],
        $GLOBALS['cfg']['ShowAll'],
        $GLOBALS['pmaThemeImage']
    ),
    new Relation($dbi)
);

$response->getFooter()->setMinimal();
$header = $response->getHeader();
$header->disableMenuAndConsole();
$header->setBodyId('body_browse_foreigners');

$response->addHTML($controller->index([
    'db' => $_POST['db'] ?? null,
    'table' => $_POST['table'] ?? null,
    'field' => $_POST['field'] ?? null,
    'fieldkey' => $_POST['fieldkey'] ?? null,
    'data' => $_POST['data'] ?? null,
    'foreign_showAll' => $_POST['foreign_showAll'] ?? null,
    'foreign_filter' => $_POST['foreign_filter'] ?? null,
]));
