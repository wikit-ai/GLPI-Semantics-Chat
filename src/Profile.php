<?php
/**
 * Wikit Semantics Chat - AI Answer Generation
 * Copyright (C) 2026 by the Wikit Development Team.
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
class Profile extends \Profile
{
   /** @var string $rightname Right name used for this class */
   public static $rightname = 'profile';

    /**
     * Get tab name for item
     *
     * @since 2.0.0
     *
     * @param CommonGLPI $item         Item instance
     * @param int        $withtemplate Template flag
     * @return string Tab name or empty string
     */
   public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0): string {
      if ($item->getType() === 'Profile'
           && $item->getField('interface') !== 'helpdesk'
       ) {
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
        $withtemplate = 0
    ): bool {
      if ($item->getType() === 'Profile') {
          $profile = new self();
          self::addDefaultProfileInfos(
              $item->getID(),
              ['plugin_wikitsemanticschat_config' => 0]
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
     * @return void
     */
   public static function createFirstAccess(int $profileId): void {
       self::addDefaultProfileInfos(
           $profileId,
           ['plugin_wikitsemanticschat_config' => READ + UPDATE],
           true
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
     * @return void
     */
   public static function addDefaultProfileInfos(
        int $profileId,
        array $rights,
        bool $dropExisting = false
    ): void {
       $dbu = new DbUtils();
       $profileRight = new ProfileRight();

      foreach ($rights as $right => $value) {
          $count = $dbu->countElementsInTable(
              'glpi_profilerights',
              [
                  'profiles_id' => $profileId,
                  'name'        => $right,
              ]
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
     * Show profile form for plugin rights management
     *
     * @since 2.0.0
     *
     * @param int  $profiles_id Profile ID
     * @param bool $openform    Whether to open form tag
     * @param bool $closeform   Whether to close form tag
     * @return void
     */
   public function showForm($profiles_id = 0, $openform = true, $closeform = true) {
       echo '<div class="firstbloc">';

       $canEdit = Session::haveRightsOr(self::$rightname, [CREATE, UPDATE, PURGE]);

      if ($canEdit && $openform) {
          $profile = new GlpiProfile();
          echo '<form method="post" action="' . $profile->getFormURL() . '">';
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
   }

    /**
     * Get all plugin rights
     *
     * @since 2.0.0
     *
     * @param bool $all Get all rights or only visible ones
     * @return array Array of rights definitions
     */
   public static function getAllRights(bool $all = false): array {
       return [
           [
               'rights' => [
                   READ   => __('Read'),
                   UPDATE => __('Update'),
               ],
               'label'  => __('Wikit Semantics Chat', 'wikitsemanticschat'),
               'field'  => 'plugin_wikitsemanticschat_config',
           ],
       ];
   }

    /**
     * Initialize profiles and migrate if necessary
     *
     * @since 2.0.0
     *
     * @return void
     */
   public static function initProfile(): void {
       global $DB;

       $dbu = new DbUtils();

       // Add new rights in glpi_profilerights table
      foreach (self::getAllRights(true) as $data) {
          $count = $dbu->countElementsInTable(
              'glpi_profilerights',
              ['name' => $data['field']]
          );

         if ($count === 0) {
            ProfileRight::addProfileRights([$data['field']]);
         }
      }

       // Update session if it exists
      if (!isset($_SESSION['glpiactiveprofile']['id'])) {
          return;
      }

       $profileId = (int)$_SESSION['glpiactiveprofile']['id'];

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
     *
     * @return void
     */
   public static function removeRightsFromSession(): void {
      foreach (self::getAllRights(true) as $right) {
         if (isset($_SESSION['glpiactiveprofile'][$right['field']])) {
            unset($_SESSION['glpiactiveprofile'][$right['field']]);
         }
      }
   }
}
