import { __ } from "@wordpress/i18n";
import {
  MediaUpload,
  MediaUploadCheck,
  RichText,
} from "@wordpress/block-editor";
import {
  TextControl,
  TextareaControl,
  ToggleControl,
  Button,
  RadioControl,
} from "@wordpress/components";
import SiteUrlInput from "./SiteUrlInput";
import useRowMotion from "./useRowMotion";

/**
 * Generic repeater UI for a block attribute holding an array of row objects.
 * The block-editor equivalent of the `repeater` field type in
 * inc/options.php — same sub-field vocabulary (text/textarea/image), separate
 * implementation since one runs in wp-admin and the other inside the block
 * editor's React tree.
 *
 * Rows lay out inline by default (`layout: 'row'`): each sub-field takes an
 * equal-width slot, with compact move-up/move-down/remove icon buttons at
 * the row's end. Sub-field labels render once, as column headers above the
 * rows, rather than repeating per row — `hideLabelFromVision` keeps them
 * screen-reader accessible on each control without rendering visually
 * twice.
 *
 * `layout: 'column'` stacks each row's sub-fields vertically instead —
 * there's no shared column header in that layout (it wouldn't line up with
 * anything), so each sub-field's own label renders visibly above its
 * control instead of being screen-reader-only.
 *
 * @param {Object}   props
 * @param {string}   props.label    Field group label.
 * @param {Object[]} props.value    Current rows.
 * @param {Function} props.onChange ( rows ) => void
 * @param {Object[]} props.fields   [ { name, label, type: 'text'|'number'|'textarea'|'richtext'|'image'|'file'|'link'|'radio', help, mimeTypes, linkTarget, options } ]
 *                                  `linkTarget` (link fields only) adds an "open in new tab" toggle,
 *                                  The URL half of a `link` field is SiteUrlInput (type-ahead search of
 *                                  pages and posts, same-site URLs stored as root-relative paths).
 *                                  storing `{name}Target` on the row — same opt-in shape as the
 *                                  top-level `link` field type's `link_target` option. `options`
 *                                  (radio fields only) is `[ { label, value } ]`, mirroring the
 *                                  top-level `select`/`radio` field types' options shape.
 *                                  `richtext` stores an HTML string (a RichText field: bold/italic by default,
 *                                  override with `allowedFormats`). Legacy plain-text values with line breaks
 *                                  are shown with those breaks intact.
 * @param {Object}   props.emptyRow Shape of a freshly-added row, e.g. { stat: '', title: '' }.
 * @param {string}   [props.layout] 'row' (default) or 'column'.
 */
export default function RepeaterField({
  label,
  value,
  onChange,
  fields,
  emptyRow,
  layout = "row",
}) {
  const isColumn = "column" === layout;
  const rows = value || [];

  function updateRow(index, patch) {
    const next = rows.slice();
    next[index] = { ...next[index], ...patch };
    onChange(next);
  }

  // Stable row keys, slide-on-move and highlight — see useRowMotion.js.
  const { keys, setRowRef, movedKey, addRow, moveRow, removeRow } =
    useRowMotion(rows, onChange);

  function confirmRemoveRow(index) {
    // eslint-disable-next-line no-alert -- a plain confirm() is enough
    // friction for an irreversible remove; no undo exists for this field.
    if (!window.confirm(__("Remove this row?", "cb-chillibyte-2026"))) {
      return;
    }
    removeRow(index);
  }

  return (
    <div
      className={
        isColumn
          ? "cb-chillibyte-2026-repeater-field cb-chillibyte-2026-repeater-field--column"
          : "cb-chillibyte-2026-repeater-field"
      }
    >
      <label className="cb-chillibyte-2026-editor-field__label">{label}</label>
      {!isColumn && rows.length > 0 && (
        <div className="cb-chillibyte-2026-repeater-field__header">
          <span className="cb-chillibyte-2026-repeater-field__number-spacer" />
          {fields.map((field) => (
            <span
              key={field.name}
              className={
                "image" === field.type || "file" === field.type
                  ? "cb-chillibyte-2026-repeater-field__header-cell cb-chillibyte-2026-repeater-field__header-cell--image"
                  : "cb-chillibyte-2026-repeater-field__header-cell"
              }
            >
              {field.label}
            </span>
          ))}
          <span className="cb-chillibyte-2026-repeater-field__row-actions-spacer" />
        </div>
      )}
      <div className="cb-chillibyte-2026-repeater-field__rows">
        {rows.map((row, index) => (
          <div
            className={
              "cb-chillibyte-2026-repeater-field__row" +
              (keys[index] === movedKey ? " is-moved" : "")
            }
            key={keys[index]}
            ref={setRowRef(keys[index])}
          >
            <span className="cb-chillibyte-2026-repeater-field__number">
              {index + 1}
            </span>
            {fields.map((field) => {
              if ("image" === field.type) {
                return (
                  <MediaUploadCheck key={field.name}>
                    <MediaUpload
                      onSelect={(media) =>
                        updateRow(index, {
                          [field.name]: media.id,
                          [`${field.name}Url`]: media.url,
                        })
                      }
                      allowedTypes={["image"]}
                      value={row[field.name]}
                      render={({ open }) => (
                        <div className="cb-chillibyte-2026-repeater-field__image">
                          {row[`${field.name}Url`] && (
                            <img src={row[`${field.name}Url`]} alt="" />
                          )}
                          <Button
                            variant="secondary"
                            size="small"
                            onClick={open}
                          >
                            {row[field.name]
                              ? __("Replace", "cb-chillibyte-2026")
                              : __("Select", "cb-chillibyte-2026")}
                          </Button>
                        </div>
                      )}
                    />
                  </MediaUploadCheck>
                );
              }

              if ("link" === field.type) {
                return (
                  <div
                    className="cb-chillibyte-2026-repeater-field__link"
                    key={field.name}
                  >
                    <TextControl
                      label={__(`${field.label} Title`, "cb-chillibyte-2026")}
                      hideLabelFromVision={!isColumn}
                      value={row[`${field.name}Text`] || ""}
                      onChange={(v) =>
                        updateRow(index, { [`${field.name}Text`]: v })
                      }
                    />
                    <SiteUrlInput
                      label={__(`${field.label} URL`, "cb-chillibyte-2026")}
                      hideLabelFromVision={!isColumn}
                      value={row[field.name]}
                      onChange={(v) => updateRow(index, { [field.name]: v })}
                    />
                    {field.help && (
                      <p className="cb-chillibyte-2026-editor-field__help">
                        {field.help}
                      </p>
                    )}
                    {field.linkTarget && (
                      <ToggleControl
                        label={__(
                          `Open ${field.label} in a new tab`,
                          "cb-chillibyte-2026",
                        )}
                        checked={!!row[`${field.name}Target`]}
                        onChange={(v) =>
                          updateRow(index, { [`${field.name}Target`]: v })
                        }
                      />
                    )}
                  </div>
                );
              }

              if ("file" === field.type) {
                return (
                  <MediaUploadCheck key={field.name}>
                    <MediaUpload
                      onSelect={(media) =>
                        updateRow(index, {
                          [field.name]: media.id,
                          [`${field.name}Name`]:
                            media.filename || media.title || "",
                        })
                      }
                      allowedTypes={field.mimeTypes || []}
                      value={row[field.name]}
                      render={({ open }) => (
                        <div className="cb-chillibyte-2026-repeater-field__image">
                          {row[`${field.name}Name`] && (
                            <span className="cb-chillibyte-2026-repeater-field__file-name">
                              {row[`${field.name}Name`]}
                            </span>
                          )}
                          <Button
                            variant="secondary"
                            size="small"
                            onClick={open}
                          >
                            {row[field.name]
                              ? __("Replace", "cb-chillibyte-2026")
                              : __("Select", "cb-chillibyte-2026")}
                          </Button>
                        </div>
                      )}
                    />
                  </MediaUploadCheck>
                );
              }

              if ("richtext" === field.type) {
                return (
                  <div
                    className="cb-chillibyte-2026-repeater-field__richtext"
                    key={field.name}
                  >
                    <span
                      className={
                        isColumn
                          ? "cb-chillibyte-2026-editor-field__label"
                          : "screen-reader-text"
                      }
                    >
                      {field.label}
                    </span>
                    <RichText
                      tagName="div"
                      className="cb-chillibyte-2026-editor-field__control"
                      aria-label={field.label}
                      placeholder={field.label}
                      allowedFormats={
                        field.allowedFormats || ["core/bold", "core/italic"]
                      }
                      value={(row[field.name] || "").replace(/\r?\n/g, "<br>")}
                      onChange={(v) => updateRow(index, { [field.name]: v })}
                    />
                    {field.help && (
                      <p className="cb-chillibyte-2026-editor-field__help">
                        {field.help}
                      </p>
                    )}
                  </div>
                );
              }

              if ("textarea" === field.type) {
                return (
                  <TextareaControl
                    key={field.name}
                    label={field.label}
                    hideLabelFromVision={!isColumn}
                    value={row[field.name] || ""}
                    onChange={(v) => updateRow(index, { [field.name]: v })}
                    help={field.help}
                  />
                );
              }

              if ("radio" === field.type) {
                return (
                  <RadioControl
                    key={field.name}
                    label={field.label}
                    hideLabelFromVision={!isColumn}
                    selected={row[field.name] || ""}
                    options={field.options || []}
                    onChange={(v) => updateRow(index, { [field.name]: v })}
                  />
                );
              }

              if ("number" === field.type) {
                return (
                  <TextControl
                    key={field.name}
                    type="number"
                    label={field.label}
                    hideLabelFromVision={!isColumn}
                    value={row[field.name] ?? ""}
                    onChange={(v) =>
                      updateRow(index, {
                        [field.name]: "" === v ? "" : Number(v),
                      })
                    }
                    help={field.help}
                  />
                );
              }

              return (
                <TextControl
                  key={field.name}
                  label={field.label}
                  hideLabelFromVision={!isColumn}
                  value={row[field.name] || ""}
                  onChange={(v) => updateRow(index, { [field.name]: v })}
                  help={field.help}
                />
              );
            })}
            <div className="cb-chillibyte-2026-repeater-field__row-actions">
              <Button
                size="small"
                data-move="up"
                label={__("Move up", "cb-chillibyte-2026")}
                onClick={() => moveRow(index, -1)}
                disabled={0 === index}
              >
                &#9650;
              </Button>
              <Button
                size="small"
                data-move="down"
                label={__("Move down", "cb-chillibyte-2026")}
                onClick={() => moveRow(index, 1)}
                disabled={index === rows.length - 1}
              >
                &#9660;
              </Button>
              <Button
                size="small"
                isDestructive
                label={__("Remove", "cb-chillibyte-2026")}
                onClick={() => confirmRemoveRow(index)}
              >
                &times;
              </Button>
            </div>
          </div>
        ))}
      </div>
      <Button variant="primary" onClick={() => addRow(emptyRow)}>
        {__("Add row", "cb-chillibyte-2026")}
      </Button>
    </div>
  );
}
