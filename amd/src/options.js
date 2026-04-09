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
 * Options helper for Tiny fileimport plugin.
 *
 * @module      tiny_fileimport/options
 * @copyright   2026 Finspire <info@finspi.re>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getPluginOptionName, getFilepickers} from 'editor_tiny/options';
import {pluginName} from './common';

const permissionsName = getPluginOptionName(pluginName, 'permissions');
const pickerTypeName = getPluginOptionName(pluginName, 'pickerType');
const acceptedTypesName = getPluginOptionName(pluginName, 'acceptedTypes');
const overrideDefaultFileAttachmentFeatureName = getPluginOptionName(pluginName, 'overrideDefaultFileAttachmentFeature');

export const register = (editor) => {
    const registerOption = editor.options.register;

    registerOption(permissionsName, {
        processor: 'object',
        "default": {
            upload: false,
        },
    });

    registerOption(pickerTypeName, {
        processor: 'string',
        "default": 'file',
    });

    registerOption(acceptedTypesName, {
        processor: 'array',
        "default": [],
    });

    registerOption(overrideDefaultFileAttachmentFeatureName, {
        processor: 'boolean',
        "default": false,
    });
};

export const getPermissions = (editor) => editor.options.get(permissionsName);

export const getPickerType = (editor) => {
    const configured = editor.options.get(pickerTypeName);
    const available = getFilepickers(editor);

    if (available && Object.prototype.hasOwnProperty.call(available, configured)) {
        return configured;
    }

    if (available && Object.prototype.hasOwnProperty.call(available, 'file')) {
        return 'file';
    }

    if (available && Object.prototype.hasOwnProperty.call(available, 'link')) {
        return 'link';
    }

    if (available && Object.prototype.hasOwnProperty.call(available, 'media')) {
        return 'media';
    }

    if (available && Object.prototype.hasOwnProperty.call(available, 'image')) {
        return 'image';
    }

    return configured;
};

const getPickerAcceptedTypes = (picker) => {
    if (!picker || typeof picker !== 'object') {
        return [];
    }

    const {accepted_types: acceptedTypes} = picker;
    if (Array.isArray(acceptedTypes)) {
        return acceptedTypes;
    }

    if (typeof acceptedTypes === 'string' && acceptedTypes.length) {
        return [acceptedTypes];
    }

    return [];
};

const fileMatchesAcceptedTypes = (file, acceptedTypes) => {
    if (!acceptedTypes.length) {
        return true;
    }

    const extension = file.name.includes('.') ? `.${file.name.split('.').pop().toLowerCase()}` : '';
    const mimeType = (file.type || '').toLowerCase();

    return acceptedTypes.some((type) => {
        const normalizedType = String(type).toLowerCase();

        if (normalizedType === '*') {
            return true;
        }

        if (normalizedType.startsWith('.')) {
            return extension === normalizedType;
        }

        if (normalizedType.startsWith('*.')) {
            return extension === normalizedType.slice(1);
        }

        if (normalizedType.endsWith('/*')) {
            return mimeType.startsWith(normalizedType.slice(0, -1));
        }

        if (normalizedType === 'document') {
            return mimeType.startsWith('application/') || extension === '.pdf';
        }

        if (normalizedType.includes('/')) {
            return mimeType === normalizedType;
        }

        return mimeType.startsWith(`${normalizedType}/`) || mimeType === normalizedType;
    });
};

export const getPickerTypeForFile = (editor, file) => {
    const preferredType = getPickerType(editor);
    const available = getFilepickers(editor);

    if (available && Object.prototype.hasOwnProperty.call(available, preferredType)) {
        const preferredPicker = available[preferredType];
        if (fileMatchesAcceptedTypes(file, getPickerAcceptedTypes(preferredPicker))) {
            return preferredType;
        }
    }

    if (available) {
        for (const [type, picker] of Object.entries(available)) {
            if (fileMatchesAcceptedTypes(file, getPickerAcceptedTypes(picker))) {
                return type;
            }
        }
    }

    return preferredType;
};

export const getAcceptedTypes = (editor) => editor.options.get(acceptedTypesName);

export const getOverrideDefaultFileAttachmentFeature = (editor) =>
    editor.options.get(overrideDefaultFileAttachmentFeatureName);
