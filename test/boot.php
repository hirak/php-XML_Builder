#!/usr/bin/env php
<?php
/**
 * 実験用
 *
 */
require_once dirname(__FILE__) . '/../lib/XML/Builder.php';

if ($argc === 1) {
    die('ex) ./boot.php test1_tpl.php');
}
$builder = xml_builder();

if (is_readable($argv[1])) {
    include $argv[1];
}
