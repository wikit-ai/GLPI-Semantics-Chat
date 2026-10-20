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

use Glpi\DBAL\QueryFunction;
use GlpiPlugin\Wikitsemanticschat\Config;
use GlpiPlugin\Wikitsemanticschat\Profile;

/**
 * Check plugin configuration after installation
 *
 * @param bool $verbose Whether to display messages
 * @return bool True if configuration is valid
 */
function plugin_wikitsemanticschat_check_config(bool $verbose = false): bool
{
    return true;
}

/**
 * Plugin install and upgrade process
 */
function plugin_wikitsemanticschat_install(): bool
{
    /** @var DBmysql $DB */
    global $DB;

    $migration = new Migration(PLUGIN_WIKITSEMANTICSCHAT_VERSION);
    $table = Config::getTable();

    if (!$DB->tableExists($table)) {
        $default_charset   = DBConnection::getDefaultCharset();
        $default_collation = DBConnection::getDefaultCollation();
        $default_key_sign  = DBConnection::getDefaultPrimaryKeySignOption();

        $migration->displayMessage(sprintf('Creating table %s', $table));
        $DB->doQuery(
            "CREATE TABLE `$table` (
                `id` int $default_key_sign NOT NULL AUTO_INCREMENT,
                `organization_slug` varchar(255) NOT NULL DEFAULT '',
                `llm_app_slug` varchar(255) NOT NULL DEFAULT '',
                `color` varchar(7) NOT NULL DEFAULT '#000000',
                `chat_button_tooltip` varchar(255) NOT NULL DEFAULT '',
                `chat_button_icon_url` varchar(500) NOT NULL DEFAULT '',
                `chat_button_width` varchar(10) NOT NULL DEFAULT '48',
                `chat_button_height` varchar(10) NOT NULL DEFAULT '48',
                `position_right` varchar(10) NOT NULL DEFAULT '1',
                `position_left` varchar(10) NOT NULL DEFAULT 'auto',
                `position_bottom` varchar(10) NOT NULL DEFAULT '1',
                `date_creation` timestamp NULL DEFAULT NULL,
                `date_mod` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `date_creation` (`date_creation`),
                KEY `date_mod` (`date_mod`)
            ) ENGINE=InnoDB DEFAULT CHARSET=$default_charset COLLATE=$default_collation ROW_FORMAT=DYNAMIC",
        );
        $DB->insert($table, ['id' => 1, 'date_creation' => QueryFunction::now(), 'date_mod' => QueryFunction::now()]);
    }

    // Audit fields added after 2.0.0
    $migration->addField($table, 'date_creation', 'timestamp');
    $migration->addField($table, 'date_mod', 'timestamp');
    $migration->addKey($table, 'date_creation');
    $migration->addKey($table, 'date_mod');

    $migration->executeMigration();

    // Initialize profile rights
    Profile::initProfile();

    // Create first access rights if session is available (not in CLI mode)
    if (isset($_SESSION['glpiactiveprofile']['id'])) {
        Profile::createFirstAccess($_SESSION['glpiactiveprofile']['id']);
    }

    return true;
}

/**
 * Plugin uninstall process
 *
 * Removes database tables and plugin rights
 *
 * @return bool True on success
 */
function plugin_wikitsemanticschat_uninstall(): bool
{
    global $DB;

    // Drop plugin tables
    $tables = [
        Config::getTable(),
    ];

    foreach ($tables as $table) {
        if ($DB->tableExists($table)) {
            $DB->dropTable($table);
        }
    }

    // Delete rights associated with the plugin
    $profileRight = new ProfileRight();
    foreach (Profile::getAllRights() as $right) {
        $profileRight->deleteByCriteria(['name' => $right['field']]);
    }

    Profile::removeRightsFromSession();

    return true;
}
