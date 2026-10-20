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

use CommonGLPI;
use DbUtils;
use Html;
use Profile as GlpiProfile;
use ProfileRight;
use Session;

/**
 * Profile management for Semantics Chat plugin
 *
 */
class Profile extends GlpiProfile
{
    /**
     * Get tab name for item
     *
     * @since 2.0.0
     *
     * @param CommonGLPI $item         Item instance
     * @param int        $withtemplate Template flag
     * @return string Tab name or empty string
     */
    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0): string
    {
        if ($item instanceof GlpiProfile) {
            return __('Wikit Semantics Chat', 'wikitsemanticschat');
        }

        return '';
    }

    /**
     * Display tab content for item
     *
     * @since 2.0.0
     *
     * @param CommonGLPI $item         Item instance
     * @param int        $tabnum       Tab number
     * @param int        $withtemplate Template flag
     * @return bool True on success
     */
    public static function displayTabContentForItem(
        CommonGLPI $item,
        $tabnum = 1,
        $withtemplate = 0,
    ): bool {
        if ($item instanceof GlpiProfile) {
            $profile = new self();
            self::addDefaultProfileInfos(
                $item->getID(),
                [Config::$rightname => 0],
            );
            $profile->showForm($item->getID());
        }

        return true;
    }

    /**
     * Create first access rights for a profile
     *
     * @since 2.0.0
     *
     * @param int $profileId Profile ID
     */
    public static function createFirstAccess(int $profileId): void
    {
        self::addDefaultProfileInfos(
            $profileId,
            [Config::$rightname => READ + UPDATE],
            true,
        );
    }

    /**
     * Add default profile rights
     *
     * @since 2.0.0
     *
     * @param int   $profileId    Profile ID
     * @param array $rights       Rights to add (name => value)
     * @param bool  $dropExisting Whether to drop existing rights before adding
     */
    public static function addDefaultProfileInfos(
        int $profileId,
        array $rights,
        bool $dropExisting = false,
    ): void {
        $dbu = new DbUtils();
        $profileRight = new ProfileRight();

        foreach ($rights as $right => $value) {
            $count = $dbu->countElementsInTable(
                'glpi_profilerights',
                [
                    'profiles_id' => $profileId,
                    'name'        => $right,
                ],
            );

            if ($count && $dropExisting) {
                $profileRight->deleteByCriteria([
                    'profiles_id' => $profileId,
                    'name'        => $right,
                ]);
                $count = 0;
            }

            if (!$count) {
                $profileRight->add([
                    'profiles_id' => $profileId,
                    'name'        => $right,
                    'rights'      => $value,
                ]);

                // Add right to current session
                if (isset($_SESSION['glpiactiveprofile'])) {
                    $_SESSION['glpiactiveprofile'][$right] = $value;
                }
            }
        }
    }

    /**
     * Show profile form
     *
     * @param int                  $ID      Profile ID
     * @param array<string, mixed> $options `openform` and `closeform` (both true by default)
     * @return bool
     */
    public function showForm($ID, array $options = [])
    {
        $profiles_id = (int) $ID;
        $openform = $options['openform'] ?? true;
        $closeform = $options['closeform'] ?? true;

        echo '<div class="firstbloc">';

        $canEdit = Session::haveRightsOr(self::$rightname, [CREATE, UPDATE, PURGE]);

        if ($canEdit && $openform) {
            $profile = new GlpiProfile();
            echo '<form method="post" action="' . htmlescape($profile->getFormURL()) . '">';
        }

        $profile = new GlpiProfile();
        $profile->getFromDB($profiles_id);

        $rights = self::getAllRights();
        $profile->displayRightsChoiceMatrix($rights, [
            'canedit'       => $canEdit,
            'default_class' => 'tab_bg_2',
            'title'         => __('General'),
        ]);

        if ($canEdit && $closeform) {
            echo '<div class="center">';
            echo Html::hidden('id', ['value' => $profiles_id]);
            echo Html::submit(_sx('button', 'Save'), ['name' => 'update']);
            echo '</div>';
            Html::closeForm();
        }

        echo '</div>';

        return true;
    }

    /**
     * Get all plugin rights
     *
     * @since 2.0.0
     *
     * @param bool $all Get all rights or only visible ones
     * @return array Array of rights definitions
     */
    public static function getAllRights(bool $all = false): array
    {
        return [
            [
                'rights' => [
                    READ   => __('Read'),
                    UPDATE => __('Update'),
                ],
                'label'  => __('Wikit Semantics Chat', 'wikitsemanticschat'),
                'field'  => Config::$rightname,
            ],
        ];
    }

    /**
     * Initialize profiles and migrate if necessary
     *
     * @since 2.0.0
     */
    public static function initProfile(): void
    {
        global $DB;

        $dbu = new DbUtils();

        // Add new rights in glpi_profilerights table
        foreach (self::getAllRights(true) as $data) {
            $count = $dbu->countElementsInTable(
                'glpi_profilerights',
                ['name' => $data['field']],
            );

            if ($count === 0) {
                ProfileRight::addProfileRights([$data['field']]);
            }
        }

        // Update session if it exists
        if (!isset($_SESSION['glpiactiveprofile']['id'])) {
            return;
        }

        $profileId = (int) $_SESSION['glpiactiveprofile']['id'];

        $iterator = $DB->request([
            'FROM'  => 'glpi_profilerights',
            'WHERE' => [
                'profiles_id' => $profileId,
                'name'        => ['LIKE', '%plugin_wikitsemanticschat%'],
            ],
        ]);

        foreach ($iterator as $row) {
            $_SESSION['glpiactiveprofile'][$row['name']] = $row['rights'];
        }
    }

    /**
     * Remove plugin rights from current session
     *
     * @since 2.0.0
     */
    public static function removeRightsFromSession(): void
    {
        foreach (self::getAllRights(true) as $right) {
            if (isset($_SESSION['glpiactiveprofile'][$right['field']])) {
                unset($_SESSION['glpiactiveprofile'][$right['field']]);
            }
        }
    }
}
