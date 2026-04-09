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
 * Tiny fileimport UI helpers.
 *
 * @module      tiny_fileimport/ui
 * @copyright   2026 Finspire <info@finspi.re>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Modal from 'core/modal';
import ModalEvents from 'core/modal_events';
import Notification from 'core/notification';
import {getString} from 'core/str';
import uploadFile from 'editor_tiny/uploader';
import {component} from './common';
import {
    getPermissions,
    getPickerTypeForFile,
    getAcceptedTypes,
    getOverrideDefaultFileAttachmentFeature,
} from './options';

const FILEPICKERS_OPTION_NAME = 'moodle:filepickers';

const NATIVE_IMAGE_TYPES_OPTION_NAME = 'images_file_types';

const matchesNativeImageTypes = (editor, file) => {
    const nativeImageTypes = String(editor.options.get(NATIVE_IMAGE_TYPES_OPTION_NAME) || '')
        .split(',')
        .map((value) => value.trim().toLowerCase())
        .filter((value) => value !== '');

    if (!nativeImageTypes.length) {
        return true;
    }

    const extension = file.name.includes('.') ? file.name.split('.').pop().toLowerCase() : '';
    return nativeImageTypes.includes(extension);
};

const canUseNativeEditorUpload = (editor, file) => {
    if (!editor.options.get('automatic_uploads')) {
        return false;
    }

    if (!String(file.type || '').toLowerCase().startsWith('image/')) {
        return false;
    }

    return matchesNativeImageTypes(editor, file);
};

const shouldUsePluginDropHandling = (editor, files) => {
    if (getOverrideDefaultFileAttachmentFeature(editor)) {
        return true;
    }

    return files.some((file) => !canUseNativeEditorUpload(editor, file));
};

const escapeHtml = (value) => {
    const div = document.createElement('div');
    div.textContent = value;
    return div.innerHTML;
};

const insertLinks = (editor, uploadedFiles) => {
    const html = uploadedFiles
        .map(({name, url}) => `<p><a href="${escapeHtml(url)}">${escapeHtml(name)}</a></p>`)
        .join('');

    if (html) {
        editor.insertContent(html);
    }
};

const uploadAndInsert = async(editor, files) => {
    if (!files || !files.length) {
        return;
    }

    const configuredAcceptedTypes = getAcceptedTypes(editor);
    const uploadedFiles = [];

    for (const file of files) {
        try {
            const pickerType = getPickerTypeForFile(editor, file);
            const filepickers = editor.options.get(FILEPICKERS_OPTION_NAME) || {};

            if (filepickers[pickerType] && configuredAcceptedTypes?.length) {
                filepickers[pickerType].accepted_types = configuredAcceptedTypes;
                editor.options.set(FILEPICKERS_OPTION_NAME, filepickers);
            }

            const url = await uploadFile(editor, pickerType, file, file.name, () => {});
            uploadedFiles.push({name: file.name, url});
        } catch (error) {
            // Check if error is due to unsupported file type
            if (error.errorcode === 'invalidfiletype' || error.errorcode === '191') {
                const message = await getString('filetypenotsupported_desc', component, escapeHtml(file.name));
                Notification.alert(
                    await getString('filetypenotsupported', component),
                    message
                );
            } else {
                Notification.exception(error);
            }
        }
    }

    insertLinks(editor, uploadedFiles);
};

export const displayDialog = async(editor) => {
    const title = await getString('modaltitle', component);
    const hint = await getString('dropzonehint', component);

    const modal = await Modal.create({
        title,
        body: `<div class="tiny_fileimport_dropzone"
                style="border:2px dashed #bbb;padding:28px;text-align:center;cursor:pointer;">${escapeHtml(hint)}</div>
            <input type="file" class="tiny_fileimport_input" multiple style="display:none;"/>`,
        removeOnClose: true,
        show: true,
    });

    const root = modal.getRoot()[0];
    const dropzone = root.querySelector('.tiny_fileimport_dropzone');
    const input = root.querySelector('.tiny_fileimport_input');

    dropzone.addEventListener('click', () => input.click());

    input.addEventListener('change', async() => {
        await uploadAndInsert(editor, Array.from(input.files || []));
        modal.hide();
    });

    dropzone.addEventListener('dragover', (event) => {
        event.preventDefault();
    });

    dropzone.addEventListener('drop', async(event) => {
        event.preventDefault();
        await uploadAndInsert(editor, Array.from(event.dataTransfer?.files || []));
        modal.hide();
    });

    root.addEventListener(ModalEvents.hidden, () => {
        modal.destroy();
    });
};

export const registerEditorDrop = (editor) => {
    editor.on('dragover', (event) => {
        const files = Array.from(event?.dataTransfer?.files || []);

        if (files.length && shouldUsePluginDropHandling(editor, files)) {
            event.preventDefault();
        }
    });

    editor.on('drop', async(event) => {
        const files = Array.from(event?.dataTransfer?.files || []);

        if (!files.length || !shouldUsePluginDropHandling(editor, files)) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        await uploadAndInsert(editor, files);
    });
};

export const canUpload = (editor) => {
    const permissions = getPermissions(editor);
    return !!permissions.upload;
};
