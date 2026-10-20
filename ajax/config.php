<?php

/**
 * -------------------------------------------------------------------------
 * Wikit Semantics Chat plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of Wikit Semantics Chat.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2026 by Wikit.
 * @license   Apache-2.0 https://www.apache.org/licenses/LICENSE-2.0
 * @link      https://github.com/wikit-ai/GLPI-Semantics-Chat
 * -------------------------------------------------------------------------
 */

use GlpiPlugin\Wikitsemanticschat\Config;

use function Safe\json_encode;

// Set JSON content type header
header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
$userId = Session::getLoginUserID();
if (!$userId) {
    echo json_encode([
        'success' => false,
        'error'   => 'Not authenticated',
    ]);
    return;
}

// Check if user has READ right on the plugin
if (!Session::haveRight(Config::$rightname, READ)) {
    echo json_encode([
        'success' => false,
        'error'   => 'Access denied',
    ]);
    return;
}

// Get configuration
$config = Config::getConfig();

// Check if plugin is properly configured
if (!$config->isConfigured()) {
    echo json_encode([
        'success' => false,
        'error'   => 'Plugin not configured',
    ]);
    return;
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
