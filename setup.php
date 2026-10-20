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

use Glpi\Plugin\Hooks;
use GlpiPlugin\Wikitsemanticschat\Config;
use GlpiPlugin\Wikitsemanticschat\Profile;

/** @phpstan-ignore theCodingMachineSafe.function (safe to assume this isn't already defined) */
define('PLUGIN_WIKITSEMANTICSCHAT_VERSION', '3.0.0');
// Minimal GLPI version, inclusive
/** @phpstan-ignore theCodingMachineSafe.function (safe to assume this isn't already defined) */
define('PLUGIN_WIKITSEMANTICSCHAT_MIN_GLPI_VERSION', '12.0.0');
// Maximum GLPI version, exclusive
/** @phpstan-ignore theCodingMachineSafe.function (safe to assume this isn't already defined) */
define('PLUGIN_WIKITSEMANTICSCHAT_MAX_GLPI_VERSION', '12.0.99');

/** @phpstan-ignore theCodingMachineSafe.function (safe to assume this isn't already defined) */
define('PLUGIN_WIKITSEMANTICSCHAT_DIR', Plugin::getPhpDir('wikitsemanticschat'));

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 */
function plugin_init_wikitsemanticschat(): void
{
    global $PLUGIN_HOOKS;

    if (!Plugin::isPluginActive('wikitsemanticschat')) {
        return;
    }

    Plugin::registerClass(Config::class);
    Plugin::registerClass(
        Profile::class,
        ['addtabon' => 'Profile'],
    );

    if (Session::getLoginUserID()
        && isset($_SESSION['glpiactiveprofile']['id'])
        && !isset($_SESSION['glpiactiveprofile'][Config::$rightname])
    ) {
        Profile::initProfile();
    }

    // Add JavaScript on all pages for users with READ right only
    if (Session::getLoginUserID() && Session::haveRight(Config::$rightname, READ)) {
        $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT]['wikitsemanticschat'] = [
            'js/wikitsemanticschat.js',
        ];

        // Configuration page in Setup > Plugins
        if (Session::haveRight(\Config::$rightname, UPDATE)) {
            $PLUGIN_HOOKS['config_page']['wikitsemanticschat'] = 'front/config.form.php';
        }
    }
}

/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 *
 * @return array Plugin information array
 */
function plugin_version_wikitsemanticschat(): array
{
    return [
        'name'         => 'Wikit Semantics Chat',
        'version'      => PLUGIN_WIKITSEMANTICSCHAT_VERSION,
        'author'       => 'Wikit',
        'license'      => 'Apache2',
        'homepage'     => 'https://github.com/wikit-ai/GLPI-Semantics-Chat',
        'requirements' => [
            'glpi' => [
                'min' => PLUGIN_WIKITSEMANTICSCHAT_MIN_GLPI_VERSION,
                'max' => PLUGIN_WIKITSEMANTICSCHAT_MAX_GLPI_VERSION,
            ],
            'php' => [
                'min' => '8.3',
            ],
        ],
    ];
}
