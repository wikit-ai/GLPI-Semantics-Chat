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

namespace GlpiPlugin\Wikitsemanticschat;

use CommonDBTM;
use Glpi\Application\View\TemplateRenderer;
use Psr\SimpleCache\CacheInterface;
use Session;

use function Safe\parse_url;
use function Safe\preg_match;

/**
 * Configuration class for Semantics Chat plugin
 *
 */
class Config extends CommonDBTM
{
    /** @var string $rightname Right name used for this class */
    public static string $rightname = 'plugin_wikitsemanticschat_config';

    /** @var bool $dohistory Enable history tracking */
    public bool $dohistory = true;

    /**
     * Get the singleton configuration instance
     *
     * @since 2.0.0
     *
     * @param bool $refresh Whether to refresh from database
     * @return self Configuration instance
     */
    public static function getConfig(bool $refresh = false): self
    {
        /** @var self|null $config */
        static $config = null;

        if ($config === null || $refresh) {
            $config = new self();
            $config->getFromDB(1);
        }

        return $config;
    }

    /**
     * Name of the meta tag holding the widget data in the GLPI pages
     */
    public const WIDGET_DATA_META = 'wikitsemanticschat-widget';

    /**
     * GLPI cache key of the configuration fields, see `getWidgetData()`
     */
    private const string FIELDS_CACHE_KEY = 'wikitsemanticschat_config_fields';

    /**
     * Lifetime of the configuration fields kept in the GLPI cache, in seconds
     */
    private const int FIELDS_CACHE_TTL = 3600;

    /**
     * Data of the chat widget for the current user, added to every GLPI page.
     *
     * It needs no database query: the configuration is kept in the GLPI cache (cleared when the
     * configuration is saved) and the user information comes from the session.
     *
     * Depends on the session and the configuration.
     *
     * @phpstan-impure
     * @return array<string, mixed> Same format as `ajax/config.php`
     */
    public static function getWidgetData(): array
    {
        /** @var CacheInterface $GLPI_CACHE */
        global $GLPI_CACHE;

        $userId = Session::getLoginUserID();
        if ($userId === false || !Session::haveRight(self::$rightname, READ)) {
            return ['success' => false, 'error' => 'Access denied'];
        }

        $fields = $GLPI_CACHE->get(self::FIELDS_CACHE_KEY);
        if (!is_array($fields)) {
            $fields = self::getConfig(true)->fields;
            $GLPI_CACHE->set(self::FIELDS_CACHE_KEY, $fields, self::FIELDS_CACHE_TTL);
        }

        /** @var array<string, mixed> $fields */
        $config = new self();
        $config->fields = $fields;
        if (!$config->isConfigured()) {
            return ['success' => false, 'error' => 'Plugin not configured'];
        }

        return [
            'success'   => true,
            'config'    => $config->getJsConfig(),
            'scriptUrl' => $config->getScriptUrl(),
            'userInfo'  => [
                'id'        => $userId,
                'login'     => $_SESSION['glpiname'] ?? '',
                'firstname' => $_SESSION['glpifirstname'] ?? '',
                'realname'  => $_SESSION['glpirealname'] ?? '',
                'language'  => $_SESSION['glpilanguage'] ?? 'fr_FR',
            ],
        ];
    }

    public function post_addItem()
    {
        parent::post_addItem();
        $this->clearFieldsCache();
    }

    public function post_updateItem($history = true)
    {
        parent::post_updateItem($history);
        $this->clearFieldsCache();
    }

    private function clearFieldsCache(): void
    {
        /** @var CacheInterface $GLPI_CACHE */
        global $GLPI_CACHE;

        $GLPI_CACHE->delete(self::FIELDS_CACHE_KEY);
    }

    /**
     * Get the localized name of the configuration type
     *
     * @since 2.0.0
     *
     * @param int $nb Number of items (for plural form)
     * @return string Localized type name
     */
    public static function getTypeName($nb = 0): string
    {
        return __('Wikit Semantics Chat Configuration', 'wikitsemanticschat');
    }

    /**
     * Check if current user can create configuration
     *
     * @since 2.0.0
     *
     * @return bool True if user has UPDATE right
     */
    public static function canCreate(): bool
    {
        return Session::haveRight(self::$rightname, UPDATE);
    }

    /**
     * Check if current user can view configuration
     *
     * @since 2.0.0
     *
     * @return bool True if user has READ right
     */
    public static function canView(): bool
    {
        return Session::haveRight(self::$rightname, READ);
    }

    /**
     * Check if current user can update configuration
     *
     * @since 2.0.0
     *
     * @return bool True if user has UPDATE right
     */
    public static function canUpdate(): bool
    {
        return Session::haveRight(self::$rightname, UPDATE);
    }

    /**
     * Validate the widget settings before saving them: they are given as-is to the chat
     * widget (CSS dimensions and positions, image URL)
     *
     * @param array<string, mixed> $input Input data
     * @return array<string, mixed>|false Input data, false when a value is invalid
     */
    public function prepareInputForUpdate($input)
    {
        $errors = [];

        // Submitted values, trimmed (non scalar values are invalid)
        $values = [];
        foreach (['color', 'chat_button_width', 'chat_button_height', 'position_right', 'position_left', 'position_bottom', 'chat_button_icon_url'] as $field) {
            if (array_key_exists($field, $input)) {
                $values[$field] = is_scalar($input[$field]) ? trim((string) $input[$field]) : '';
                $input[$field] = $values[$field];
            }
        }

        if (isset($values['color']) && !preg_match('/^#[0-9a-fA-F]{6}$/', $values['color'])) {
            $errors[] = __('The button color must be an hexadecimal color (#RRGGBB).', 'wikitsemanticschat');
        }

        foreach (['chat_button_width', 'chat_button_height'] as $field) {
            if (isset($values[$field]) && !preg_match('/^[1-9]\d{0,3}$/', $values[$field])) {
                $errors[] = __('The button dimensions must be positive integers.', 'wikitsemanticschat');
                break;
            }
        }

        foreach (['position_right', 'position_left', 'position_bottom'] as $field) {
            if (isset($values[$field]) && $values[$field] !== 'auto' && !preg_match('/^\d{1,3}(\.\d{1,2})?$/', $values[$field])) {
                $errors[] = __('The positions must be numbers or "auto".', 'wikitsemanticschat');
                break;
            }
        }

        $url = $values['chat_button_icon_url'] ?? '';
        if ($url !== '') {
            $scheme = filter_var($url, FILTER_VALIDATE_URL) !== false ? parse_url($url, PHP_URL_SCHEME) : null;
            if (!is_string($scheme) || strtolower($scheme) !== 'https') {
                $errors[] = __('The button icon URL must be a valid HTTPS URL.', 'wikitsemanticschat');
            }
        }

        if ($errors !== []) {
            foreach ($errors as $error) {
                Session::addMessageAfterRedirect(htmlescape($error), false, ERROR);
            }

            return false;
        }

        return $input;
    }

    /**
     * Define tabs available for the configuration form
     *
     * @since 2.0.0
     *
     * @param array $options Options
     * @return array Array of tabs
     */
    public function defineTabs($options = []): array
    {
        $tabs = [];
        $this->addDefaultFormTab($tabs);
        return $tabs;
    }

    /**
     * Check if this is a new item (always false for singleton config)
     *
     * @since 2.0.0
     *
     * @return bool Always false as config uses singleton pattern
     */
    public function isNewItem(): bool
    {
        return false;
    }

    /**
     * Display the configuration form
     *
     * @since 2.0.0
     *
     * @param int   $ID      Item ID
     * @param array $options Display options
     * @return bool True on success
     */
    public function showForm($ID, array $options = []): bool
    {
        $this->initForm($ID, $options);

        TemplateRenderer::getInstance()->display('@wikitsemanticschat/config.html.twig', [
            'item'   => $this,
            'params' => $options,
        ]);

        return true;
    }

    /**
     * Get JavaScript configuration for the chat widget
     *
     * @since 2.0.0
     *
     * @return array Configuration array for JavaScript widget
     */
    public function getJsConfig(): array
    {
        // Build position values with units
        $posRight = $this->fields['position_right'] ?? '1';
        $posLeft = $this->fields['position_left'] ?? 'auto';
        $posBottom = $this->fields['position_bottom'] ?? '1';

        $config = [
            'organizationSlug' => $this->fields['organization_slug'] ?? '',
            'color'            => $this->fields['color'] ?? '#000000',
            'position'         => [
                'right'  => $posRight === 'auto' ? 'auto' : $posRight . '%',
                'left'   => $posLeft === 'auto' ? 'auto' : $posLeft . '%',
                'bottom' => $posBottom === 'auto' ? 'auto' : $posBottom . '%',
            ],
        ];

        // Only add llmAppSlug if configured
        if (!empty($this->fields['llm_app_slug'])) {
            $config['llmAppSlug'] = $this->fields['llm_app_slug'];
        }

        // Chat button dimensions with units
        $width = ($this->fields['chat_button_width'] ?? '48') . 'px';
        $height = ($this->fields['chat_button_height'] ?? '48') . 'px';

        // Chat button tooltip
        if (!empty($this->fields['chat_button_tooltip'])) {
            $config['chatButtonTooltip'] = [
                'text' => $this->fields['chat_button_tooltip'],
            ];
        }

        // Chat button icon
        if (!empty($this->fields['chat_button_icon_url'])) {
            $config['chatButtonIcon'] = [
                'url'    => $this->fields['chat_button_icon_url'],
                'width'  => $width,
                'height' => $height,
            ];
        } else {
            // Just set dimensions if no custom icon
            $config['chatButtonIcon'] = [
                'width'  => $width,
                'height' => $height,
            ];
        }

        return $config;
    }

    /**
     * Get the script URL for the Semantics Chat embed
     *
     * @since 2.0.0
     *
     * @return string URL of the Semantics Chat embed script
     */
    public function getScriptUrl(): string
    {
        $url = 'https://semantics-chat.wikit.ai/chat-embed.js';

        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return '';
        }

        return $url;
    }

    /**
     * Check if the configuration is valid for displaying the chat
     *
     * @since 2.0.0
     *
     * @return bool True if organization slug is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->fields['organization_slug']);
    }
}
