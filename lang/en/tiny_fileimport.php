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
 * Strings for component 'tiny_fileimport', language 'en'.
 *
 * @package    tiny_fileimport
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'File import';
$string['buttontitle'] = 'File import';
$string['modaltitle'] = 'Add files';
$string['dropzonehint'] = 'Drag and drop files here, or click to choose files';
$string['allowalltypes'] = 'Allow all file types';
$string['allowalltypes_desc'] = 'If enabled, uploads are not restricted to the file types listed in Site administration > Server > File types. If disabled, all file types currently listed there are allowed by default.';
$string['overridedefaultfileattachmentfeature'] = 'Override default file attachment feature';
$string['overridedefaultfileattachmentfeature_desc'] = 'If enabled, this plugin handles drag-and-drop uploads in the editor instead of Tiny\'s default attachment handling. If disabled, the editor keeps its native upload handling for supported files, such as images, and this plugin is used only for files the default editor upload flow does not handle.';
$string['allowedextensionsoverride'] = 'Allowed file extensions override';
$string['allowedextensionsoverride_desc'] = 'Optional. Comma, space, or newline separated list of extensions to allow (for example: pdf, docx, xlsx, zip). If empty, the plugin uses the full list from Site administration > Server > File types. Ignored when "Allow all file types" is enabled.';
$string['filetypenotsupported'] = 'File type not supported';
$string['filetypenotsupported_desc'] = 'The file \"{$a}\" could not be uploaded because its file type is not supported by the current settings.';
$string['tiny/fileimport:use'] = 'Use Tiny file import';
$string['privacy:metadata'] = 'The Tiny file import plugin does not store any personal data.';
