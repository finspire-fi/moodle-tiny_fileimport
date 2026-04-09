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
 * Tiny fileimport commands.
 *
 * @module      tiny_fileimport/commands
 * @copyright   2026 Finspire <info@finspi.re>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getString} from 'core/str';
import {component, buttonName} from './common';
import {displayDialog, registerEditorDrop, canUpload} from './ui';

export const getSetup = async() => {
    const buttonText = await getString('buttontitle', component);

    return (editor) => {
        if (!canUpload(editor)) {
            return;
        }

        registerEditorDrop(editor);

        editor.ui.registry.addMenuItem(buttonName, {
            text: buttonText,
            icon: 'upload',
            onAction: () => {
                displayDialog(editor);
            },
        });
    };
};
