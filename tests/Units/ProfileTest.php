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
use GlpiPlugin\Wikitsemanticschat\Profile;
use Profile as GlpiProfile;
use ProfileRight;

final class ProfileTest extends DbTestCase
{
    private function getPluginRight(int $profiles_id): ?int
    {
        $right = new ProfileRight();
        if (!$right->getFromDBByCrit(['profiles_id' => $profiles_id, 'name' => Config::$rightname])) {
            return null;
        }

        $rights = $right->fields['rights'];
        $this->assertIsInt($rights);

        return $rights;
    }

    public function testGetAllRights(): void
    {
        $this->assertSame([Config::$rightname], array_column(Profile::getAllRights(), 'field'));
    }

    public function testCreateFirstAccess(): void
    {
        $profile = $this->createItem(GlpiProfile::class, ['name' => 'WS chat profile']);

        Profile::createFirstAccess($profile->getID());

        $this->assertSame(READ + UPDATE, $this->getPluginRight($profile->getID()));
    }

    public function testAddDefaultProfileInfosKeepsExistingRights(): void
    {
        $profile = $this->createItem(GlpiProfile::class, ['name' => 'WS chat profile']);
        Profile::createFirstAccess($profile->getID());

        Profile::addDefaultProfileInfos($profile->getID(), [Config::$rightname => 0]);
        $this->assertSame(READ + UPDATE, $this->getPluginRight($profile->getID()));

        Profile::addDefaultProfileInfos($profile->getID(), [Config::$rightname => READ], true);
        $this->assertSame(READ, $this->getPluginRight($profile->getID()));
    }

    public function testRemoveRightsFromSession(): void
    {
        $this->login();
        $active_profile = $_SESSION['glpiactiveprofile'];
        $this->assertIsArray($active_profile);
        $active_profile[Config::$rightname] = READ;
        $_SESSION['glpiactiveprofile'] = $active_profile;

        Profile::removeRightsFromSession();

        $active_profile = $_SESSION['glpiactiveprofile'];
        $this->assertIsArray($active_profile);
        $this->assertArrayNotHasKey(Config::$rightname, $active_profile);
    }
}
