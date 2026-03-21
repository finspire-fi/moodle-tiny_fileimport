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
- Default allowed types come from the same source as **Site administration > Server > File types** (`admin/tool/filetypes`)
- Admin checkbox to allow `*` (all file types)
- Works with Moodle Tiny plugin architecture (`plugin_with_menuitems`, `plugin_with_configuration`)

---

## Requirements

- Moodle: **4.5 and later**
- PHP: Moodle-supported version for your Moodle release

---

## Plugin Location

Place the plugin in:

`lib/editor/tiny/plugins/fileimport`

Expected component name:

`tiny_fileimport`

## Configuration & Behavior

### Admin setting

Navigate to plugin settings and configure:

- `Allow all file types` (`tiny_fileimport/allowalltypes`)
- `Override default file attachment feature` (`tiny_fileimport/overridedefaultfileattachmentfeature`)
- `Allowed file extensions override` (`tiny_fileimport/allowedextensionsoverride`)

Behavior:

- If `Allow all file types` is enabled: plugin uses `*` (no extension restriction at plugin level)
- Else if `Allowed file extensions override` is non-empty: plugin uses that override list
- Else (default): plugin allows all file extensions currently listed in `admin/tool/filetypes`
- If `Override default file attachment feature` is enabled: direct editor drag-and-drop is always handled by this plugin
- If `Override default file attachment feature` is disabled: Tiny keeps its native drag-and-drop upload behavior for files it supports, such as images, and this plugin takes over only for files the native editor upload flow does not handle

Override format:

- Comma, space, or newline separated extensions
- Examples: `pdf, docx, xlsx, zip` or one per line
- Dot prefix is optional (`pdf` and `.pdf` are both accepted)

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

Before each upload, the plugin applies the configured accepted types to the selected picker so server-side upload validation receives the intended `accepted_types[]` values.

---

## Testing Checklist

- Insert menu shows **File import**
- Clicking **File import** opens modal
- Clicking dropzone opens file picker
- Drag & drop into modal uploads files
- Drag & drop directly into editor uploads files when override is enabled
- Dragged images still use Tiny's native image upload when override is disabled
- Uploaded links are inserted in content
- PDF upload succeeds (no `invalidfiletype`)
- Non-PDF types from `admin/tool/filetypes` (e.g. docx, xlsx, zip) upload successfully
- When `Allow all file types` is enabled, uncommon extensions are accepted

---

## Security Notes

- Upload processing uses Moodle’s repository upload endpoint and session validation.
- Allowed file types are governed by Moodle file picker configuration (`accepted_types`) and context options.
- Keep Moodle and plugin code updated.

---

## License

GNU GPL v3 or later (consistent with Moodle plugin conventions).
