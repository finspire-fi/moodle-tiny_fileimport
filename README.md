# Tiny File Import (`tiny_fileimport`)

A custom TinyMCE plugin for Moodle that adds a **File import** item to the **Insert** menu.

The plugin allows users to:
- Open a modal from the editor
- Drag & drop files (or click to browse)
- Upload files through Moodle repository API
- Insert uploaded file links into editor content
- Drag & drop files directly into the editor area

---

## Features

- Insert menu item: **File import**
- Modal-based file upload UI
- Direct editor drag-and-drop upload
- Automatic link insertion (`<a href="...">filename</a>`)
- Per-file picker type selection based on `accepted_types`
  - Supports common patterns like `.pdf`, `*.pdf`, `application/*`, and `document`
- Works with Moodle Tiny plugin architecture (`plugin_with_menuitems`, `plugin_with_configuration`)

---

## Requirements

- Moodle: **5.1.3+** (or compatible with current Tiny API used by this plugin)
- PHP: Moodle-supported version for your Moodle release
- Node.js + npm (for AMD build)
- Access to run Moodle CLI commands:
  - `admin/cli/upgrade.php`
  - `admin/cli/purge_caches.php`

---

## Plugin Location

Place the plugin in:

`lib/editor/tiny/plugins/fileimport`

Expected component name:

`tiny_fileimport`

---

## Installation

### 1) Copy plugin files

Copy the plugin directory into your Moodle codebase:

```bash
cp -R tiny_fileimport /path/to/moodle/lib/editor/tiny/plugins/fileimport
```

(Or use your normal deployment method.)

### 2) Build AMD assets

From Moodle root:

```bash
cd /path/to/moodle
npm install
```

Then build this plugin’s AMD modules:

```bash
cd /path/to/moodle/lib/editor/tiny/plugins/fileimport
npx grunt amd --gruntfile /path/to/moodle/Gruntfile.js --component=tiny_fileimport
```

### 3) Run Moodle upgrade

From Moodle root:

```bash
cd /path/to/moodle
echo "y" | php admin/cli/upgrade.php
```

### 4) Purge caches

```bash
php admin/cli/purge_caches.php
```

### 5) Browser refresh

Hard refresh your browser (Ctrl+Shift+R) before testing editor behavior.

---

## Development Workflow

When changing files under `amd/src`:

1. Rebuild AMD:

```bash
cd /path/to/moodle/lib/editor/tiny/plugins/fileimport
npx grunt amd --gruntfile /path/to/moodle/Gruntfile.js --component=tiny_fileimport
```

2. If PHP metadata changed (e.g., `classes/plugininfo.php`, `version.php`), bump plugin version in `version.php`.

3. Run upgrade and purge caches:

```bash
cd /path/to/moodle
echo "y" | php admin/cli/upgrade.php
php admin/cli/purge_caches.php
```

---

## Configuration & Behavior

### Menu registration

- JS menu item key: `fileimport`
- PHP available menu item: `tiny_fileimport/fileimport`

These identifiers must remain aligned for the menu entry to appear.

### Picker selection logic

The plugin chooses a file picker dynamically per file:

1. Try configured picker type first
2. If not suitable for that file, scan available pickers
3. Choose first picker whose `accepted_types` matches the file

Fallback order preference includes:
- `file`
- `link`
- `media`
- `image`

This avoids common `invalidfiletype` errors when uploading PDFs through non-document pickers.

---

## Testing Checklist

- Insert menu shows **File import**
- Clicking **File import** opens modal
- Clicking dropzone opens file picker
- Drag & drop into modal uploads files
- Drag & drop directly into editor uploads files
- Uploaded links are inserted in content
- PDF upload succeeds (no `invalidfiletype`)

---

## Troubleshooting

### 1) Menu item does not appear

Check:
- `classes/plugininfo.php` returns `tiny_fileimport/fileimport` in `get_available_menuitems()`
- JS registers `addMenuItem('fileimport', ...)`
- `amd/build/*.min.js` are rebuilt and valid AMD modules
- Caches were purged

### 2) Browser shows `Unexpected token 'export'`

Cause: build files contain ES module syntax instead of transpiled AMD.

Fix:
- Rebuild AMD with Grunt
- Purge caches

### 3) `invalidfiletype` when uploading PDF

Cause: selected picker doesn’t accept the file type.

Fix:
- Ensure latest plugin version is installed
- Rebuild + upgrade + purge caches
- Verify filepicker options on target form allow document uploads

### 4) Grunt fails with `Unable to find local grunt`

Run from Moodle root:

```bash
cd /path/to/moodle
npm install
```

Then rerun plugin build command with `--gruntfile` pointing to Moodle root.

---

## File Map

- `classes/plugininfo.php` — plugin registration and configuration
- `version.php` — plugin version metadata
- `amd/src/plugin.js` — Tiny plugin entrypoint
- `amd/src/commands.js` — menu item registration
- `amd/src/configuration.js` — menu injection
- `amd/src/options.js` — option and picker resolution
- `amd/src/ui.js` — modal + drag/drop + upload logic
- `amd/build/*.min.js` — compiled AMD output

---

## Security Notes

- Upload processing uses Moodle’s repository upload endpoint and session validation.
- Allowed file types are governed by Moodle file picker configuration (`accepted_types`) and context options.
- Keep Moodle and plugin code updated.

---

## License

GNU GPL v3 or later (consistent with Moodle plugin conventions).
