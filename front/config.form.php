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

// Check rights
Session::checkRight(\Config::$rightname, UPDATE);

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
    'plugins',
);

$config->display([
    'id' => 1,
]);

Html::footer();
