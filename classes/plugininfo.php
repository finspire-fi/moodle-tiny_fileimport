<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace tiny_fileimport;

use context;
use editor_tiny\editor;
use editor_tiny\plugin;
use editor_tiny\plugin_with_configuration;
use editor_tiny\plugin_with_menuitems;

/**
 * Tiny fileimport plugin.
 *
 * @package    tiny_fileimport
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugininfo extends plugin implements plugin_with_menuitems, plugin_with_configuration {

    #[\Override]
    public static function is_enabled(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): bool {
        return true;
    }

    public static function get_available_menuitems(): array {
        return [
            'tiny_fileimport/fileimport',
        ];
    }

    public static function get_plugin_configuration_for_context(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): array {
        $pickertype = 'file';
        if (!array_key_exists($pickertype, $fpoptions)) {
            if (array_key_exists('link', $fpoptions)) {
                $pickertype = 'link';
            } else if (array_key_exists('media', $fpoptions)) {
                $pickertype = 'media';
            } else if (array_key_exists('image', $fpoptions)) {
                $pickertype = 'image';
            } else if (!empty($fpoptions)) {
                $pickertype = array_key_first($fpoptions);
            }
        }

        return [
            'permissions' => [
                'upload' => true,
            ],
            'pickerType' => $pickertype,
        ];
    }
}
