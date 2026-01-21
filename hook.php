<?php

/**
 * Wikit Semantics Chat - AI Answer Generation
 * Copyright (C) 2026 by the Wikit Development Team.
 */

use GlpiPlugin\Wikitsemanticschat\Profile;

/**
 * Check plugin prerequisites before installation
 *
 * @return bool True if prerequisites are met, false otherwise
 */
function plugin_wikitsemanticschat_check_prerequisites(): bool {
   if (version_compare(GLPI_VERSION, PLUGIN_WIKITSEMANTICSCHAT_MIN_GLPI_VERSION, 'lt')
        || version_compare(GLPI_VERSION, PLUGIN_WIKITSEMANTICSCHAT_MAX_GLPI_VERSION, 'gt')
    ) {
       echo 'This plugin requires GLPI >= ' . PLUGIN_WIKITSEMANTICSCHAT_MIN_GLPI_VERSION
           . ' and < ' . PLUGIN_WIKITSEMANTICSCHAT_MAX_GLPI_VERSION;
       return false;
   }

   if (version_compare(PHP_VERSION, '8.0.0', 'lt')) {
       echo 'This plugin requires PHP >= 8.0.0';
       return false;
   }

    return true;
}

/**
 * Check plugin configuration after installation
 *
 * @param bool $verbose Whether to display messages
 * @return bool True if configuration is valid
 */
function plugin_wikitsemanticschat_check_config(bool $verbose = false): bool {
   if ($verbose) {
       echo 'Installed / not configured';
   }
    return true;
}

/**
 * Plugin install process
 *
 * Creates database tables and initializes plugin configuration
 *
 * @return bool True on success, false on failure
 */
function plugin_wikitsemanticschat_install(): bool {
    global $DB;

    $migration = new Migration(PLUGIN_WIKITSEMANTICSCHAT_VERSION);

    // Create config table if not exists
   if (!$DB->tableExists('glpi_plugin_wikitsemanticschat_configs')) {
       $DB->runFile(PLUGIN_WIKITSEMANTICSCHAT_DIR . '/install/sql/empty-1.0.0.sql');
   }

    // Add audit fields if they don't exist (for upgrades)
   if (!$DB->fieldExists('glpi_plugin_wikitsemanticschat_configs', 'date_creation')) {
       $migration->addField(
           'glpi_plugin_wikitsemanticschat_configs',
           'date_creation',
           'timestamp',
           ['null' => true]
       );
       $migration->addKey('glpi_plugin_wikitsemanticschat_configs', 'date_creation');
   }

   if (!$DB->fieldExists('glpi_plugin_wikitsemanticschat_configs', 'date_mod')) {
       $migration->addField(
           'glpi_plugin_wikitsemanticschat_configs',
           'date_mod',
           'timestamp',
           ['null' => true]
       );
       $migration->addKey('glpi_plugin_wikitsemanticschat_configs', 'date_mod');
   }

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
 *
 * @return bool True on success
 */
function plugin_wikitsemanticschat_uninstall(): bool {
    global $DB;

    // Drop plugin tables
    $tables = [
        'glpi_plugin_wikitsemanticschat_configs',
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
