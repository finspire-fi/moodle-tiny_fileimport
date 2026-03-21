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
- Modal-based upload UI with drag-and-drop and click-to-browse
- Direct editor drag-and-drop support
- Automatic link insertion (`<a href="...">filename</a>`)
- Smart per-file picker selection based on `accepted_types`
  - Supports common patterns such as `.pdf`, `*.pdf`, `application/*`, and `document`
- File type policy driven by admin settings
  - Default source: **Site administration > Server > File types** (`admin/tool/filetypes`)
  - Optional override list of allowed extensions
  - Optional allow-all mode (`*`)
- Optional override for Tiny default attachment handling during editor drag-and-drop
- Clean user-facing message for unsupported file types (instead of raw `invalidfiletype` exception output)
- Works with Moodle Tiny plugin architecture (`plugin_with_menuitems`, `plugin_with_configuration`)

---

## Requirements

- Moodle: **4.5 and later**
- PHP: Moodle-supported version for your Moodle release

---

## Configuration & Behavior

### Admin settings

Navigate to plugin settings and configure:

- `Allow all file types` (`tiny_fileimport/allowalltypes`)
- `Override default file attachment feature` (`tiny_fileimport/overridedefaultfileattachmentfeature`)
- `Allowed file extensions override` (`tiny_fileimport/allowedextensionsoverride`)

### File type policy precedence

File type restrictions are resolved in this order:

1. If `Allow all file types` is enabled, plugin uses `*` (no plugin-level extension restriction)
2. Else, if `Allowed file extensions override` is non-empty, plugin uses that list
3. Else, plugin uses all file extensions listed in `admin/tool/filetypes`

### Extension override format

- Comma, space, or newline separated extensions
- Examples: `pdf, docx, xlsx, zip` or one per line
- Dot prefix is optional (`pdf` and `.pdf` are both accepted)

### Editor drag-and-drop behavior

- If `Override default file attachment feature` is enabled:
  - direct editor drag-and-drop is handled by this plugin
- If `Override default file attachment feature` is disabled:
  - Tiny keeps native drag-and-drop handling for files it supports (for example images)
  - this plugin is used only when native editor upload flow does not handle the dropped file(s)

### Upload error behavior

- Unsupported file types show a clean, localized message: **File type not supported**
- Other upload failures continue to use Moodle exception handling

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
