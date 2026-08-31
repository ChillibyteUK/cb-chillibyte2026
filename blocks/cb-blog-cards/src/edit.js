import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";
import {
  TextControl,
  TextareaControl,
  ToggleControl,
} from "@wordpress/components";
import EditorBlockShell from "../../_shared/EditorBlockShell";

export default function Edit({ attributes, setAttributes, clientId }) {
  const { title, postCount, showFilters } = attributes;
  const blockProps = useBlockProps({
    className: "container cb-chillibyte-2026-editor-block",
  });

  return (
    <EditorBlockShell
      blockProps={blockProps}
      clientId={clientId}
      title="CB Blog Cards"
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
          <TextControl
            type="number"
            label={__("Post Count", "cb-chillibyte-2026")}
            value={postCount}
            onChange={(value) => setAttributes({ postCount: Number(value) })}
            help={__("Defaults to 3", "cb-chillibyte-2026")}
          />
        </div>
        <div style={{ flex: "50 1 0%" }}>
          <ToggleControl
            label={__("Show Filters", "cb-chillibyte-2026")}
            checked={showFilters}
            onChange={(value) => setAttributes({ showFilters: value })}
          />
        </div>
      </div>
    </EditorBlockShell>
  );
}
