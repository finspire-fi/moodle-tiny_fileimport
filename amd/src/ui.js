// This file is part of Moodle - http://moodle.org/

/**
 * Tiny fileimport UI helpers.
 *
 * @module      tiny_fileimport/ui
 */

import Modal from 'core/modal';
import ModalEvents from 'core/modal_events';
import Notification from 'core/notification';
import {getString} from 'core/str';
import uploadFile from 'editor_tiny/uploader';
import {component} from './common';
import {getPermissions, getPickerTypeForFile} from './options';

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

    const uploadedFiles = [];

    for (const file of files) {
        try {
            const pickerType = getPickerTypeForFile(editor, file);
            const url = await uploadFile(editor, pickerType, file, file.name, () => {});
            uploadedFiles.push({name: file.name, url});
        } catch (error) {
            Notification.exception(error);
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
        if (event?.dataTransfer?.files?.length) {
            event.preventDefault();
        }
    });

    editor.on('drop', async(event) => {
        if (!event?.dataTransfer?.files?.length) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        await uploadAndInsert(editor, Array.from(event.dataTransfer.files));
    });
};

export const canUpload = (editor) => {
    const permissions = getPermissions(editor);
    return !!permissions.upload;
};
