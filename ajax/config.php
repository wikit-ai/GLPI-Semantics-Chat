<?php
/**
 * Wikit Semantics Chat - AI Answer Generation
 * Copyright (C) 2026 by the Wikit Development Team.
 */

include ('../../../inc/includes.php');

use GlpiPlugin\Wikitsemanticschat\Config;

// Set JSON content type header
header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
$userId = Session::getLoginUserID();
if (!$userId) {
    echo json_encode([
        'success' => false,
        'error'   => 'Not authenticated',
    ]);
    exit;
}

// Get configuration
$config = Config::getConfig();

// Check if plugin is properly configured
if (!$config->isConfigured()) {
    echo json_encode([
        'success' => false,
        'error'   => 'Plugin not configured',
    ]);
    exit;
}

// Get current user information
$user = new User();
$user->getFromDB($userId);

$userInfo = [
    'id'        => $userId,
    'login'     => $user->fields['name'] ?? '',
    'firstname' => $user->fields['firstname'] ?? '',
    'realname'  => $user->fields['realname'] ?? '',
    'language'  => $_SESSION['glpilanguage'] ?? 'fr_FR',
];

// Return configuration
echo json_encode([
    'success'   => true,
    'config'    => $config->getJsConfig(),
    'scriptUrl' => $config->getScriptUrl(),
    'userInfo'  => $userInfo,
]);
