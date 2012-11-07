<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//echo __FILE__;

$currentDir = dirname(__FILE__);

define('_ROOT_DIR_',realpath($currentDir) . '/..');
//define('_ROOT_DIR_', $currentDir . '/..');

define('_CONFIG_DIR',_ROOT_DIR_ . '/config/');
define('_CLASS_DIR',_ROOT_DIR_ . '/class/');
define('_JS_DIR_',_ROOT_DIR_ . '/js/');
define('_CSS_DIR_',_ROOT_DIR_ . '/css/');


define('_UPLOAD_DIR_',_ROOT_DIR_ . '/upload/');
define('_UPLOAD_CSV_DIR_',_UPLOAD_DIR_ . 'csv/');



