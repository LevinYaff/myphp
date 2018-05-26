<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Theme generator tool
 *
 * @package PhpMyAdmin
 */
use PhpMyAdmin\Response;
use PhpMyAdmin\ThemeGenerator;

require_once 'libraries/common.inc.php';

$response = Response::getInstance();
$header   = $response->getHeader();
$scripts  = $header->getScripts();
$scripts->addFile('theme_generator/color_picker.js');

$theme = new ThemeGenerator();

$response->addHTML($theme->colorPicker());
$response->addHTML($theme->form());
// $response->addHTML($_POST['Base_Colour']);
if (isset($_POST['Base_Colour'])) {
    $theme->createLayoutFile($_POST['theme_name']);
}