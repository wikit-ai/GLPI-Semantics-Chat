<?php
/**
 * Wikit Semantics Chat - AI Answer Generation
 * Copyright (C) 2026 by the Wikit Development Team.
 */

use Glpi\Plugin\Hooks;
use GlpiPlugin\Wikitsemanticschat\Config;
use GlpiPlugin\Wikitsemanticschat\Profile;

define('PLUGIN_WIKITSEMANTICSCHAT_VERSION', '1.0.1');
// Minimal GLPI version, inclusive
define('PLUGIN_WIKITSEMANTICSCHAT_MIN_GLPI_VERSION', '10.0.0');
// Maximum GLPI version, exclusive
define('PLUGIN_WIKITSEMANTICSCHAT_MAX_GLPI_VERSION', '10.99.99');

define('PLUGIN_WIKITSEMANTICSCHAT_DIR', Plugin::getPhpDir('wikitsemanticschat'));
define('PLUGIN_WIKITSEMANTICSCHAT_WEBDIR', Plugin::getWebDir('wikitsemanticschat'));

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 *
 * @return void
 */
function plugin_init_wikitsemanticschat(): void {
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS[Hooks::CSRF_COMPLIANT]['wikitsemanticschat'] = true;

   if (!Plugin::isPluginActive('wikitsemanticschat')) {
       return;
   }

   // Register plugin classes (always needed for profile management)
   if (Session::getLoginUserID()) {
       Plugin::registerClass(Config::class);
       Plugin::registerClass(
           Profile::class,
           ['addtabon' => 'Profile']
       );

       // Configuration page in Setup > Plugins
       if (Session::haveRight('config', UPDATE)) {
           $PLUGIN_HOOKS['config_page']['wikitsemanticschat'] = 'front/config.form.php';
       }

       // Add JavaScript only for users with READ right
       if (Session::haveRight('plugin_wikitsemanticschat_config', READ)) {
           $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT]['wikitsemanticschat'] = [
               'public/js/config.js.php',
               'public/js/wikitsemanticschat.js'
           ];
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
function plugin_version_wikitsemanticschat(): array {
    return [
        'name'         => 'Wikit Semantics Chat',
        'version'      => PLUGIN_WIKITSEMANTICSCHAT_VERSION,
        'author'       => 'Wikit',
        'license'      => 'Apache2',
        'homepage'     => 'https://github.com/wikitai/GLPI-Semantics-Chat',
        'requirements' => [
            'glpi' => [
                'min' => PLUGIN_WIKITSEMANTICSCHAT_MIN_GLPI_VERSION,
                'max' => PLUGIN_WIKITSEMANTICSCHAT_MAX_GLPI_VERSION,
            ],
            'php' => [
                'min' => '7.4',
            ],
        ],
    ];
}
