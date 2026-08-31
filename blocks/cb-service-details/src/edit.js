import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";
import { TextareaControl } from "@wordpress/components";
import EditorBlockShell from "../../_shared/EditorBlockShell";
import RepeaterField from "../../_shared/RepeaterField";

const itemsFields = [
  { name: "title", label: __("Title", "cb-chillibyte-2026"), type: "text" },
  {
    name: "description",
    label: __("Description", "cb-chillibyte-2026"),
    type: "textarea",
  },
];

const itemsEmptyRow = { title: "", description: "" };

export default function Edit({ attributes, setAttributes, clientId }) {
  const { title, intro, items } = attributes;
  const blockProps = useBlockProps({
    className: "container cb-chillibyte-2026-editor-block",
  });

  return (
    <EditorBlockShell
      blockProps={blockProps}
      clientId={clientId}
      title="CB Service Details"
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
      <TextareaControl
        label={__("Intro", "cb-chillibyte-2026")}
        value={intro}
        onChange={(value) => setAttributes({ intro: value })}
      />
      <RepeaterField
        label={__("Items", "cb-chillibyte-2026")}
        value={items}
        onChange={(value) => setAttributes({ items: value })}
        fields={itemsFields}
        emptyRow={itemsEmptyRow}
        layout="column"
      />
    </EditorBlockShell>
  );
}
