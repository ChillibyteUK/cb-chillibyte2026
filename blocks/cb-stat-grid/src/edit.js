import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";
import { TextareaControl } from "@wordpress/components";
import EditorBlockShell from "../../_shared/EditorBlockShell";
import RepeaterField from "../../_shared/RepeaterField";

const statsFields = [
  { name: "stat", label: __("Stat", "cb-chillibyte-2026"), type: "number" },
  {
    name: "statSuffix",
    label: __("Stat suffix", "cb-chillibyte-2026"),
    type: "text",
  },
  {
    name: "description",
    label: __("Description", "cb-chillibyte-2026"),
    type: "text",
  },
];

const statsEmptyRow = { stat: "", statSuffix: "", description: "" };

export default function Edit({ attributes, setAttributes, clientId }) {
  const { title, stats } = attributes;
  const blockProps = useBlockProps({
    className: "container cb-chillibyte-2026-editor-block",
  });

  return (
    <EditorBlockShell
      blockProps={blockProps}
      clientId={clientId}
      title="CB Stat Grid"
    >
      <TextareaControl
        label={__("Title", "cb-chillibyte-2026")}
        value={title}
        onChange={(value) => setAttributes({ title: value })}
      />
      <RepeaterField
        label={__("Stats", "cb-chillibyte-2026")}
        value={stats}
        onChange={(value) => setAttributes({ stats: value })}
        fields={statsFields}
        emptyRow={statsEmptyRow}
      />
    </EditorBlockShell>
  );
}
