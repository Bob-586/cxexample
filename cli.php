#!/usr/bin/php
<?php

/**
 * @copyright (c) 2015
 * @author Chris Allen, Robert Strutts 
 */

if (! empty($_SERVER['HTTP_HOST'])) {
  echo "Direct access denied!";
  exit;
}

$mem_baseline = memory_get_usage();

require "../cx/includes/configure.php";
require 'config.php';
require '../cx/startup.php';

$app->load_cli_controller();

cx_get_memory_stats(); // If debug on, shows memory stats
