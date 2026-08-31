import { __ } from "@wordpress/i18n";
import { useBlockProps, RichText } from "@wordpress/block-editor";
import {
  TextControl,
  TextareaControl,
  ToggleControl,
} from "@wordpress/components";
import EditorBlockShell from "../../_shared/EditorBlockShell";

export default function Edit({ attributes, setAttributes, clientId }) {
  const {
    title,
    content,
    ctaText,
    ctaUrl,
    ctaTarget,
    quoteBody,
    quoteAttribution,
  } = attributes;
  const blockProps = useBlockProps({
    className: "container cb-chillibyte-2026-editor-block",
  });

  return (
    <EditorBlockShell
      blockProps={blockProps}
      clientId={clientId}
      title="CB Text Quote"
    >
      <TextareaControl
        label={__("Title", "cb-chillibyte-2026")}
        value={title}
        onChange={(value) => setAttributes({ title: value })}
        help={__(
          "Wrap a word in <strong> to bold it, or <em> to underline it.",
          "cb-chillibyte-2026",
        )}
      />
      <div className="cb-chillibyte-2026-editor-field">
        <label className="cb-chillibyte-2026-editor-field__label">
          {__("Content", "cb-chillibyte-2026")}
        </label>
        <RichText
          tagName="div"
          className="cb-chillibyte-2026-editor-field__control"
          aria-label={__("Content", "cb-chillibyte-2026")}
          placeholder={__("Content", "cb-chillibyte-2026")}
          value={content}
          onChange={(value) => setAttributes({ content: value })}
        />
      </div>
      <TextControl
        label={__("CTA Text", "cb-chillibyte-2026")}
        value={ctaText}
        onChange={(value) => setAttributes({ ctaText: value })}
      />
      <TextControl
        type="url"
        label={__("CTA URL", "cb-chillibyte-2026")}
        value={ctaUrl}
        onChange={(value) => setAttributes({ ctaUrl: value })}
      />
      <ToggleControl
        label={__("Open CTA in a new tab", "cb-chillibyte-2026")}
        checked={ctaTarget}
        onChange={(value) => setAttributes({ ctaTarget: value })}
      />
      <TextareaControl
        label={__("Quote Body", "cb-chillibyte-2026")}
        value={quoteBody}
        onChange={(value) => setAttributes({ quoteBody: value })}
      />
      <TextControl
        label={__("Quote Attribution", "cb-chillibyte-2026")}
        value={quoteAttribution}
        onChange={(value) => setAttributes({ quoteAttribution: value })}
      />
    </EditorBlockShell>
  );
}
