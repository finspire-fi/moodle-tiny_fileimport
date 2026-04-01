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
 * @author     Mikko Haiku
 * @copyright  2026 Finspire <info@finspi.re>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugininfo extends plugin implements plugin_with_configuration, plugin_with_menuitems {
    #[\Override]
    public static function is_enabled(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): bool {
        return true;
    }

    /**
     * Return TinyMCE menu items provided by this plugin.
     *
     * @return array
     */
    public static function get_available_menuitems(): array {
        return [
            'tiny_fileimport/fileimport',
        ];
    }

    /**
     * Return plugin configuration for the current editor context.
     *
     * @param context $context
     * @param array $options
     * @param array $fpoptions
     * @param editor|null $editor
     * @return array
     */
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

        $allowalltypes = (bool) get_config('tiny_fileimport', 'allowalltypes');
        $overrideextensions = self::get_configured_allowed_extensions_override();
        $acceptedtypes = ['*'];

        if (!$allowalltypes) {
            if (!empty($overrideextensions)) {
                $acceptedtypes = $overrideextensions;
            } else {
                $acceptedtypes = self::get_all_filetypes_from_admin_list();
            }
        }

        return [
            'permissions' => [
                'upload' => true,
            ],
            'pickerType' => $pickertype,
            'acceptedTypes' => $acceptedtypes,
            'overrideDefaultFileAttachmentFeature' => (bool) get_config(
                'tiny_fileimport',
                'overridedefaultfileattachmentfeature'
            ),
        ];
    }

    /**
     * Get all configured file extensions from the same source used by admin/tool/filetypes.
     *
     * @return array
     */
    protected static function get_all_filetypes_from_admin_list(): array {
        if (!function_exists('get_mimetypes_array')) {
            return ['*'];
        }

        $extensions = array_keys(get_mimetypes_array());
        $extensions = array_filter($extensions, static function (string $extension): bool {
            return (bool) preg_match('/^[a-z0-9]+$/i', $extension);
        });

        return array_values(array_map(static function (string $extension): string {
            return '.' . strtolower($extension);
        }, $extensions));
    }

    /**
     * Get configured allowed extension override from admin setting.
     *
     * @return array
     */
    protected static function get_configured_allowed_extensions_override(): array {
        $raw = (string) get_config('tiny_fileimport', 'allowedextensionsoverride');
        if ($raw === '') {
            return [];
        }

        $tokens = preg_split('/[\s,;]+/', $raw) ?: [];
        $tokens = array_filter($tokens, static function (string $value): bool {
            return $value !== '';
        });

        $extensions = array_map(static function (string $value): string {
            $normalized = ltrim(strtolower(trim($value)), '.');
            return '.' . $normalized;
        }, $tokens);

        $extensions = array_filter($extensions, static function (string $extension): bool {
            return (bool) preg_match('/^\.[a-z0-9]+$/', $extension);
        });

        return array_values(array_unique($extensions));
    }
}
