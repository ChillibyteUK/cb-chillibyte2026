import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";
import {
  TextControl,
  TextareaControl,
  ToggleControl,
} from "@wordpress/components";
import EditorBlockShell from "../../_shared/EditorBlockShell";

export default function Edit({ attributes, setAttributes, clientId }) {
  const { title, showAddress, formShortcode } = attributes;
  const blockProps = useBlockProps({
    className: "container cb-chillibyte-2026-editor-block",
  });

  return (
    <EditorBlockShell
      blockProps={blockProps}
      clientId={clientId}
      title="CB Contact Form"
    >
      <TextareaControl
        label={__("Title", "cb-chillibyte-2026")}
        value={title}
        onChange={(value) => setAttributes({ title: value })}
        help={__(
          "Use <strong> and <em> to embolden and emphasise",
          "cb-chillibyte-2026",
        )}
      />
      <div style={{ display: "flex", flexWrap: "wrap", gap: "12px" }}>
        <div style={{ flex: "50 1 0%" }}>
          <ToggleControl
            label={__("Show Address", "cb-chillibyte-2026")}
            checked={showAddress}
            onChange={(value) => setAttributes({ showAddress: value })}
          />
        </div>
        <div style={{ flex: "50 1 0%" }}>
          <TextControl
            label={__("Form Shortcode", "cb-chillibyte-2026")}
            value={formShortcode}
            onChange={(value) => setAttributes({ formShortcode: value })}
          />
        </div>
      </div>
    </EditorBlockShell>
  );
}
