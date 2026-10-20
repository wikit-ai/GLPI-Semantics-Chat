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

namespace GlpiPlugin\Wikitsemanticschat\Tests\Units;

use Glpi\Tests\DbTestCase;
use GlpiPlugin\Wikitsemanticschat\Config;

final class ConfigTest extends DbTestCase
{
    /**
     * @param array<string, mixed> $fields
     */
    private function getConfigWith(array $fields): Config
    {
        $config = new Config();
        $config->fields = $fields;

        return $config;
    }

    public function testGetConfigLoadsTheSingleConfiguration(): void
    {
        $config = Config::getConfig(true);

        $this->assertSame(1, $config->getID());
        $this->assertSame(Config::getConfig(), $config);
    }

    public function testGetJsConfigWithAllSettings(): void
    {
        $config = $this->getConfigWith([
            'organization_slug'    => 'wikit',
            'llm_app_slug'         => 'support-bot',
            'color'                => '#123456',
            'chat_button_tooltip'  => 'Need help?',
            'chat_button_icon_url' => 'https://example.com/icon.png',
            'chat_button_width'    => '56',
            'chat_button_height'   => '40',
            'position_right'       => '2',
            'position_left'        => 'auto',
            'position_bottom'      => '3',
        ]);

        $this->assertSame([
            'organizationSlug'  => 'wikit',
            'color'             => '#123456',
            'position'          => ['right' => '2%', 'left' => 'auto', 'bottom' => '3%'],
            'llmAppSlug'        => 'support-bot',
            'chatButtonTooltip' => ['text' => 'Need help?'],
            'chatButtonIcon'    => ['url' => 'https://example.com/icon.png', 'width' => '56px', 'height' => '40px'],
        ], $config->getJsConfig());
    }

    public function testGetJsConfigWithDefaults(): void
    {
        $this->assertSame([
            'organizationSlug' => 'wikit',
            'color'            => '#000000',
            'position'         => ['right' => '1%', 'left' => 'auto', 'bottom' => '1%'],
            'chatButtonIcon'   => ['width' => '48px', 'height' => '48px'],
        ], $this->getConfigWith(['organization_slug' => 'wikit'])->getJsConfig());
    }

    public function testIsConfigured(): void
    {
        $this->assertFalse($this->getConfigWith(['organization_slug' => ''])->isConfigured());
        $this->assertTrue($this->getConfigWith(['organization_slug' => 'wikit'])->isConfigured());
    }

    public function testGetScriptUrl(): void
    {
        $this->assertSame('https://semantics-chat.wikit.ai/chat-embed.js', (new Config())->getScriptUrl());
    }

    public function testRights(): void
    {
        $this->login();
        $this->assertTrue(Config::canView());
        $this->assertTrue(Config::canUpdate());

        $this->logOut();
        $this->assertFalse(Config::canView());
        $this->assertFalse(Config::canUpdate());
    }
}
