--
-- -------------------------------------------------------------------------
-- Wikit Semantics Chat plugin for GLPI
-- Copyright (C) 2025 by the Wikit Development Team.
-- -------------------------------------------------------------------------
--
-- LICENSE
--
-- This file is part of Wikit Semantics Chat.
--
-- Wikit Semantics Chat is free software; you can redistribute it and/or modify
-- it under the terms of the Apache License, Version 2.0 (the "License");
-- you may not use this file except in compliance with the License.
-- You may obtain a copy of the License at
--
--     http://www.apache.org/licenses/LICENSE-2.0
--
-- Unless required by applicable law or agreed to in writing, software
-- distributed under the License is distributed on an "AS IS" BASIS,
-- WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
-- See the License for the specific language governing permissions and
-- limitations under the License.
-- -------------------------------------------------------------------------
-- @author    Wikit SAS
-- @copyright Copyright (C) 2025 Wikit SAS
-- @license   Apache License 2.0
-- @link      https://github.com/wikitai/GLPI-Semantics-Chat
-- -------------------------------------------------------------------------

DROP TABLE IF EXISTS `glpi_plugin_wikitsemanticschat_configs`;

CREATE TABLE `glpi_plugin_wikitsemanticschat_configs` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `organization_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `llm_app_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#000000',
    `chat_button_tooltip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `chat_button_icon_url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `chat_button_width` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '48',
    `chat_button_height` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '48',
    `position_right` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
    `position_left` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'auto',
    `position_bottom` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
    `date_creation` timestamp NULL DEFAULT NULL,
    `date_mod` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `date_creation` (`date_creation`),
    KEY `date_mod` (`date_mod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Insert default configuration row
INSERT INTO `glpi_plugin_wikitsemanticschat_configs` (`id`, `date_creation`, `date_mod`)
VALUES (1, NOW(), NOW());
