// This file is part of Moodle - http://moodle.org/

/**
 * Tiny fileimport commands.
 *
 * @module      tiny_fileimport/commands
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
