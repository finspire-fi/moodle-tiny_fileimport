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
}
