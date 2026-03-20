// This file is part of Moodle - http://moodle.org/

/**
 * Tiny fileimport configuration.
 *
 * @module      tiny_fileimport/configuration
 */

import {addMenubarItem} from 'editor_tiny/utils';
import {buttonName} from './common';

export const configure = (instanceConfig) => {
    return {
        menu: addMenubarItem(instanceConfig.menu, 'insert', buttonName),
    };
};
