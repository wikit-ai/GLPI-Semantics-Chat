<?php
/**
 * Wikit Semantics Chat - AI Answer Generation
 * Copyright (C) 2026 by the Wikit Development Team.
 */

namespace GlpiPlugin\Wikitsemanticschat;

use CommonDBTM;
use Html;
use Session;

/**
 * Configuration class for Semantics Chat plugin
 *
 */
class Config extends CommonDBTM
{
   /** @var string $rightname Right name used for this class */
   public static $rightname = 'plugin_wikitsemanticschat_config';

   /** @var bool $dohistory Enable history tracking */
    public $dohistory = true;

    /**
     * Get the singleton configuration instance
     *
     * @since 2.0.0
     *
     * @param bool $refresh Whether to refresh from database
     * @return self Configuration instance
     */
   public static function getConfig(bool $refresh = false): self {
       static $config = null;

      if ($config === null || $refresh) {
          $config = new self();
          $config->getFromDB(1);
      }

       return $config;
   }

    /**
     * Get the localized name of the configuration type
     *
     * @since 2.0.0
     *
     * @param int $nb Number of items (for plural form)
     * @return string Localized type name
     */
   public static function getTypeName($nb = 0): string {
       return __('Wikit Semantics Chat Configuration', 'wikitsemanticschat');
   }

    /**
     * Check if current user can create configuration
     *
     * @since 2.0.0
     *
     * @return bool True if user has UPDATE right
     */
   public static function canCreate(): bool {
       return Session::haveRight(self::$rightname, UPDATE);
   }

    /**
     * Check if current user can view configuration
     *
     * @since 2.0.0
     *
     * @return bool True if user has READ right
     */
   public static function canView(): bool {
       return Session::haveRight(self::$rightname, READ);
   }

    /**
     * Check if current user can update configuration
     *
     * @since 2.0.0
     *
     * @return bool True if user has UPDATE right
     */
   public static function canUpdate(): bool {
       return Session::haveRight(self::$rightname, UPDATE);
   }

    /**
     * Define tabs available for the configuration form
     *
     * @since 2.0.0
     *
     * @param array $options Options
     * @return array Array of tabs
     */
   public function defineTabs($options = []): array {
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
   public function isNewItem(): bool {
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
   public function showForm($ID, array $options = []): bool {
       $this->initForm($ID, $options);
       $this->showFormHeader($options);

       echo '<tr class="tab_bg_1">';
       echo '<td><label for="organization_slug">' . __('Organization Slug', 'wikitsemanticschat') . '</label></td>';
       echo '<td>';
       echo Html::input('organization_slug', [
           'value' => $this->fields['organization_slug'] ?? '',
           'size'  => 50,
           'id'    => 'organization_slug',
       ]);
       echo '</td>';
       echo '</tr>';

       echo '<tr class="tab_bg_1">';
       echo '<td><label for="llm_app_slug">' . __('LLM App Slug', 'wikitsemanticschat') . '</label></td>';
       echo '<td>';
       echo Html::input('llm_app_slug', [
           'value' => $this->fields['llm_app_slug'] ?? '',
           'size'  => 50,
           'id'    => 'llm_app_slug',
       ]);
       echo '</td>';
       echo '</tr>';

       echo '<tr class="tab_bg_1">';
       echo '<td><label for="color">' . __('Button Color', 'wikitsemanticschat') . '</label></td>';
       echo '<td>';
       echo Html::input('color', [
           'value' => $this->fields['color'] ?? '#000000',
           'size'  => 10,
           'id'    => 'color',
           'type'  => 'color',
           'style' => 'width: 60px; height: 30px; padding: 0; border: none;',
       ]);
       echo '</td>';
       echo '</tr>';

       echo '<tr class="tab_bg_1">';
       echo '<td><label for="chat_button_tooltip">' . __('Button Tooltip', 'wikitsemanticschat') . '</label></td>';
       echo '<td>';
       echo Html::input('chat_button_tooltip', [
           'value' => $this->fields['chat_button_tooltip'] ?? __('Need help?', 'wikitsemanticschat'),
           'size'  => 50,
           'id'    => 'chat_button_tooltip',
       ]);
       echo '</td>';
       echo '</tr>';

       echo '<tr class="tab_bg_1">';
       echo '<td><label for="chat_button_icon_url">' . __('Button Icon URL', 'wikitsemanticschat') . '</label></td>';
       echo '<td>';
       echo Html::input('chat_button_icon_url', [
           'value'       => $this->fields['chat_button_icon_url'] ?? '',
           'size'        => 80,
           'id'          => 'chat_button_icon_url',
           'placeholder' => 'https://example.com/icon.png',
       ]);
       echo '<br><span class="text-muted">' . __('Leave empty to use default icon', 'wikitsemanticschat') . '</span>';
       echo '</td>';
       echo '</tr>';

       echo '<tr class="tab_bg_1">';
       echo '<td><label for="chat_button_width">' . __('Button Width', 'wikitsemanticschat') . '</label></td>';
       echo '<td><span style="display: inline-flex; align-items: center; gap: 5px;">';
       echo Html::input('chat_button_width', [
           'value' => $this->fields['chat_button_width'] ?? '48',
           'size'  => 5,
           'id'    => 'chat_button_width',
       ]);
       echo '<span>px</span></span>';
       echo '</td>';
       echo '</tr>';

       echo '<tr class="tab_bg_1">';
       echo '<td><label for="chat_button_height">' . __('Button Height', 'wikitsemanticschat') . '</label></td>';
       echo '<td><span style="display: inline-flex; align-items: center; gap: 5px;">';
       echo Html::input('chat_button_height', [
           'value' => $this->fields['chat_button_height'] ?? '48',
           'size'  => 5,
           'id'    => 'chat_button_height',
       ]);
       echo '<span>px</span></span>';
       echo '</td>';
       echo '</tr>';

       echo '<tr class="tab_bg_1"><th colspan="2">' . __('Position', 'wikitsemanticschat') . '</th></tr>';

       echo '<tr class="tab_bg_1">';
       echo '<td><label for="position_right">' . __('Right', 'wikitsemanticschat') . '</label></td>';
       echo '<td><span style="display: inline-flex; align-items: center; gap: 5px;">';
       echo Html::input('position_right', [
           'value' => $this->fields['position_right'] ?? '1',
           'size'  => 5,
           'id'    => 'position_right',
       ]);
       echo '<span>%</span></span>';
       echo '</td>';
       echo '</tr>';

       echo '<tr class="tab_bg_1">';
       echo '<td><label for="position_left">' . __('Left', 'wikitsemanticschat') . '</label></td>';
       echo '<td><span style="display: inline-flex; align-items: center; gap: 5px;">';
       echo Html::input('position_left', [
           'value' => $this->fields['position_left'] ?? 'auto',
           'size'  => 5,
           'id'    => 'position_left',
       ]);
       echo '<span>%</span></span>';
       echo '</td>';
       echo '</tr>';

       echo '<tr class="tab_bg_1">';
       echo '<td><label for="position_bottom">' . __('Bottom', 'wikitsemanticschat') . '</label></td>';
       echo '<td><span style="display: inline-flex; align-items: center; gap: 5px;">';
       echo Html::input('position_bottom', [
           'value' => $this->fields['position_bottom'] ?? '1',
           'size'  => 5,
           'id'    => 'position_bottom',
       ]);
       echo '<span>%</span></span>';
       echo '</td>';
       echo '</tr>';

       $this->showFormButtons(['candel' => false]);

       return true;
   }

    /**
     * Get JavaScript configuration for the chat widget
     *
     * @since 2.0.0
     *
     * @return array Configuration array for JavaScript widget
     */
   public function getJsConfig(): array {
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
   public function getScriptUrl(): string {
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
   public function isConfigured(): bool {
       return !empty($this->fields['organization_slug']);
   }
}
