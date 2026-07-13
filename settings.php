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

/**
 * Admin settings for the Tiny file import plugin.
 *
 * @package    tiny_fileimport
 * @author     Mikko Haiku
 * @copyright  2026 Finspire <info@finspi.re>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin = 'tiny_fileimport';

$settings = new admin_settingpage('tiny_fileimport_settings', new lang_string('settings', $plugin));
if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configcheckbox(
        'tiny_fileimport/allowalltypes',
        get_string('allowalltypes', 'tiny_fileimport'),
        get_string('allowalltypes_desc', 'tiny_fileimport'),
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
        'tiny_fileimport/overridedefaultfileattachmentfeature',
        get_string('overridedefaultfileattachmentfeature', 'tiny_fileimport'),
        get_string('overridedefaultfileattachmentfeature_desc', 'tiny_fileimport'),
        0
    ));

    $settings->add(new admin_setting_configtextarea(
        'tiny_fileimport/allowedextensionsoverride',
        get_string('allowedextensionsoverride', 'tiny_fileimport'),
        get_string('allowedextensionsoverride_desc', 'tiny_fileimport'),
        '',
        PARAM_RAW_TRIMMED
    ));

    // Licensing settings
    $settings->add(new admin_setting_heading(
        'tiny_fileimport/licensingheading',
        get_string('licensingheading', 'tiny_fileimport'),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'tiny_fileimport/license_key',
        get_string('license_key', 'tiny_fileimport'),
        get_string('license_key_desc', 'tiny_fileimport'),
        '',
        PARAM_RAW_TRIMMED
    ));

    // License validation information (read-only)
    $validationData = get_config('tiny_fileimport', 'license_validation_data');
    $lastChecked = get_config('tiny_fileimport', 'license_last_checked');
    $validationError = get_config('tiny_fileimport', 'license_validation_error');

    $infoText = '';
    if ($validationError) {
        $infoText = get_string('validation_error', 'tiny_fileimport') . ': ' . $validationError;
    } elseif ($validationData) {
        $data = json_decode($validationData, true);
        $status = $data['status'] ?? 'unknown';
        $valid = $data['valid'] ?? false;
        $expiresAt = $data['expires_at'] ?? null;

        $infoText = get_string('license_status', 'tiny_fileimport') . ': ' . $status . "<br>";
        $infoText .= get_string('license_valid', 'tiny_fileimport') . ': ' . ($valid ? get_string('yes') : get_string('no')) . "<br>";
        if ($expiresAt) {
            $infoText .= get_string('license_expires', 'tiny_fileimport') . ': ' . date('Y-m-d', $expiresAt) . "<br>";
        }
    }

    if ($lastChecked) {
        $infoText .= get_string('last_validated', 'tiny_fileimport') . ': ' . date('Y-m-d H:i:s', $lastChecked);
    }

    if (trim($infoText)) {
        $settings->add(new admin_setting_heading(
            'tiny_fileimport/validation_info',
            get_string('license_validation_info', 'tiny_fileimport'),
            $infoText
        ));
    }
}
