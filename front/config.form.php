<?php
/**
 * Wikit Semantics Chat - AI Answer Generation
 * Copyright (C) 2026 by the Wikit Development Team.
 */

include ('../../../inc/includes.php');

use GlpiPlugin\Wikitsemanticschat\Config;

Session::checkLoginUser();
Session::checkRight('config', UPDATE);

$config = new Config();

// Handle form submission
if (isset($_POST['update'])) {
    $config->check(1, UPDATE, $_POST);
    $config->update($_POST);
    Html::back();
}

// Display page
Html::header(
    __('Wikit Semantics Chat Configuration', 'wikitsemanticschat'),
    $_SERVER['PHP_SELF'],
    'config',
    'plugins'
);

$config->display([
    'id' => 1,
]);

Html::footer();
