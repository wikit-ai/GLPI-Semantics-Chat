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
use PHPUnit\Framework\Attributes\DataProvider;

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

    public function testPrepareInputForUpdateAcceptsValidSettings(): void
    {
        $input = (new Config())->prepareInputForUpdate([
            'color'                => '#1A2b3C',
            'chat_button_width'    => ' 64 ',
            'chat_button_height'   => '48',
            'position_right'       => '2.5',
            'position_left'        => 'auto',
            'position_bottom'      => '1',
            'chat_button_icon_url' => 'https://example.com/icon.png',
        ]);

        $this->assertIsArray($input);
        $this->assertSame('64', $input['chat_button_width']);
    }

    /**
     * @return iterable<string, array{array<string, string>, string}>
     */
    public static function invalidSettingsProvider(): iterable
    {
        $color = 'The button color must be an hexadecimal color (#RRGGBB).';
        $dimensions = 'The button dimensions must be positive integers.';
        $positions = 'The positions must be numbers or &quot;auto&quot;.';
        $icon = 'The button icon URL must be a valid HTTPS URL.';

        yield 'color' => [['color' => 'red'], $color];
        yield 'width' => [['chat_button_width' => '48px'], $dimensions];
        yield 'height' => [['chat_button_height' => '0'], $dimensions];
        yield 'position' => [['position_bottom' => '1%;background:red'], $positions];
        yield 'icon scheme' => [['chat_button_icon_url' => 'javascript:alert(1)'], $icon];
        yield 'icon http' => [['chat_button_icon_url' => 'http://example.com/icon.png'], $icon];
    }

    /**
     * @param array<string, string> $input
     */
    #[DataProvider('invalidSettingsProvider')]
    public function testPrepareInputForUpdateRejectsInvalidSettings(array $input, string $message): void
    {
        $this->assertFalse((new Config())->prepareInputForUpdate($input));
        $this->hasSessionMessages(ERROR, [$message]);
    }

    public function testWidgetDataFollowsTheConfiguration(): void
    {
        $this->login();
        $config = Config::getConfig(true);

        $this->updateItem(Config::class, $config->getID(), ['organization_slug' => '']);
        $this->assertSame(['success' => false, 'error' => 'Plugin not configured'], Config::getWidgetData());

        // Saving the configuration clears the fields kept in the GLPI cache
        $this->updateItem(Config::class, $config->getID(), ['organization_slug' => 'wikit', 'color' => '#123456']);
        $data = Config::getWidgetData();
        $this->assertTrue($data['success']);
        $this->assertIsArray($data['config']);
        $this->assertSame('wikit', $data['config']['organizationSlug']);
        $this->assertSame('#123456', $data['config']['color']);
        $this->assertIsArray($data['userInfo']);
        // User of the GLPI test suite (TU_USER)
        $this->assertSame('_test_user', $data['userInfo']['login']);
    }

    public function testWidgetDataNeedsTheRight(): void
    {
        $this->logOut();
        $this->assertSame(['success' => false, 'error' => 'Access denied'], Config::getWidgetData());
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
