import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";
import { TextControl, TextareaControl } from "@wordpress/components";
import EditorBlockShell from "../../_shared/EditorBlockShell";

export default function Edit({ attributes, setAttributes, clientId }) {
  const { title, brbShortcode } = attributes;
  const blockProps = useBlockProps({
    className: "container cb-chillibyte-2026-editor-block",
  });

  return (
    <EditorBlockShell
      blockProps={blockProps}
      clientId={clientId}
      title="CB Review Slider"
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
      <TextControl
        label={__("BRB Shortcode", "cb-chillibyte-2026")}
        value={brbShortcode}
        onChange={(value) => setAttributes({ brbShortcode: value })}
      />
    </EditorBlockShell>
  );
}
