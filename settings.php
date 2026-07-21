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
    // Lock every setting except the license key itself until the license has been
    // validated successfully. This uses the same mechanism as forcing a setting via
    // config.php, so the field is both rendered disabled and pinned to its current
    // value even if a request bypassing the disabled control tried to change it.
    $licensevalidationdata = get_config('tiny_fileimport', 'license_validation_data');
    $licensevalidationerror = get_config('tiny_fileimport', 'license_validation_error');
    $licensevalid = false;
    if (empty($licensevalidationerror) && !empty($licensevalidationdata)) {
        $decodedlicensedata = json_decode($licensevalidationdata, true);
        $licensevalid = !empty($decodedlicensedata['valid']);
    }

    if (!$licensevalid) {
        foreach (['allowalltypes', 'overridedefaultfileattachmentfeature', 'allowedextensionsoverride'] as $lockedsetting) {
            $CFG->forced_plugin_settings['tiny_fileimport'][$lockedsetting] = get_config('tiny_fileimport', $lockedsetting);
        }
    }

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

    // Licensing settings.
    $marketplaceurl = 'https://marketplace.moodle.com/plugins/39';
    $settings->add(new admin_setting_heading(
        'tiny_fileimport/licensingheading',
        get_string('licensingheading', 'tiny_fileimport'),
        html_writer::div(get_string('license_purchase_info', 'tiny_fileimport', $marketplaceurl), 'alert alert-info')
    ));

    $licensekeysetting = new admin_setting_configtext(
        'tiny_fileimport/license_key',
        get_string('license_key', 'tiny_fileimport'),
        get_string('license_key_desc', 'tiny_fileimport'),
        '',
        PARAM_RAW_TRIMMED
    );
    // Validate immediately whenever the license key is changed and saved, rather
    // than waiting for the next scheduled run.
    $licensekeysetting->set_updatedcallback(static function () {
        core_php_time_limit::raise(60);
        ob_start();
        (new \tiny_fileimport\task\validate_license())->execute();
        ob_end_clean();
    });
    $settings->add($licensekeysetting);

    // License validation information (read-only).
    $validationdata = get_config('tiny_fileimport', 'license_validation_data');
    $lastchecked = get_config('tiny_fileimport', 'license_last_checked');
    $validationerror = get_config('tiny_fileimport', 'license_validation_error');

    $infotext = '';
    if ($validationerror) {
        $infotext = get_string('validation_error', 'tiny_fileimport') . ': ' . $validationerror;
    } else if ($validationdata) {
        $data = json_decode($validationdata, true);
        $status = $data['status'] ?? 'unknown';
        $valid = $data['valid'] ?? false;
        $expiresat = $data['expires_at'] ?? null;

        $infotext = get_string('license_status', 'tiny_fileimport') . ': ' . $status . "<br>";
        $yesno = $valid ? get_string('yes') : get_string('no');
        $infotext .= get_string('license_valid', 'tiny_fileimport') . ': ' . $yesno . "<br>";
        if ($expiresat) {
            $infotext .= get_string('license_expires', 'tiny_fileimport') . ': ' . date('Y-m-d', $expiresat) . "<br>";
        }
    }

    if ($lastchecked) {
        $infotext .= "<br>" . get_string('last_validated', 'tiny_fileimport') . ': ' . date('Y-m-d H:i:s', $lastchecked);
    }

    if (trim($infotext)) {
        $settings->add(new admin_setting_heading(
            'tiny_fileimport/validation_info',
            get_string('license_validation_info', 'tiny_fileimport'),
            $infotext
        ));
    }
}
