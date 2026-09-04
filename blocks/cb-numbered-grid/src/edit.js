import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";
import { TextareaControl } from "@wordpress/components";
import EditorBlockShell from "../../_shared/EditorBlockShell";
import RepeaterField from "../../_shared/RepeaterField";

/*
 * No `number` sub-field: render.php numbers rows from their position, so
 * reordering can't leave 03 sitting above 02. Bodies run long enough to want
 * a textarea, which is why this uses layout: 'column' rather than the
 * default side-by-side row.
 */
const itemFields = [
  { name: "title", label: __("Title", "cb-chillibyte-2026"), type: "text" },
  { name: "body", label: __("Body", "cb-chillibyte-2026"), type: "textarea" },
];

const itemEmptyRow = { title: "", body: "" };

export default function Edit({ attributes, setAttributes, clientId }) {
  const { title, intro, items } = attributes;
  const blockProps = useBlockProps({
    className: "container cb-chillibyte-2026-editor-block",
  });

  return (
    <EditorBlockShell
      blockProps={blockProps}
      clientId={clientId}
      title="CB Numbered Grid"
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
        fields={itemFields}
        emptyRow={itemEmptyRow}
        layout="column"
      />
    </EditorBlockShell>
  );
}
