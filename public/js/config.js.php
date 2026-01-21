<?php
/**
 * Wikit Semantics - AI Answer Generation
 * Copyright (C) 2026 by the Wikit Development Team.
 *
 * This file injects plugin configuration as JavaScript global variable
 */

include (__DIR__ . '/../../../../inc/includes.php');

header('Content-Type: application/javascript; charset=utf-8');

// Check if plugin constants are defined
if (!defined('PLUGIN_WIKITSEMANTICSCHAT_WEBDIR')) {
    $webdir = Plugin::getWebDir('wikitsemanticschat', false);
} else {
    $webdir = PLUGIN_WIKITSEMANTICSCHAT_WEBDIR;
}

// Output JavaScript configuration
echo sprintf(
    'window.WIKITSEMANTICSCHAT_WEBDIR = %s;',
    json_encode($webdir)
);
