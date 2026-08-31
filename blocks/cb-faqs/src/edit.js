import { __ } from "@wordpress/i18n";
import { useBlockProps, RichText } from "@wordpress/block-editor";
import { TextControl } from "@wordpress/components";
import EditorBlockShell from "../../_shared/EditorBlockShell";
import RepeaterField from "../../_shared/RepeaterField";

const FIELDS = [
  {
    name: "question",
    label: __("Question", "cb-chillibyte-2026"),
    type: "textarea",
  },
  {
    name: "answer",
    label: __("Answer", "cb-chillibyte-2026"),
    type: "textarea",
  },
];

const EMPTY_ROW = { question: "", answer: "" };

export default function Edit({ attributes, setAttributes, clientId }) {
  const { title, intro, faqs } = attributes;
  const blockProps = useBlockProps({
    className: "container cb-chillibyte-2026-editor-block",
  });

  return (
    <EditorBlockShell
      blockProps={blockProps}
      clientId={clientId}
      title="CB FAQs"
    >
      <TextControl
        label={__("Title", "cb-chillibyte-2026")}
        value={title}
        onChange={(value) => setAttributes({ title: value })}
      />
      <div className="cb-chillibyte-2026-editor-field">
        <label className="cb-chillibyte-2026-editor-field__label">
          {__("Intro", "cb-chillibyte-2026")}
        </label>
        <RichText
          tagName="div"
          className="cb-chillibyte-2026-editor-field__control"
          aria-label={__("Intro", "cb-chillibyte-2026")}
          placeholder={__("Intro", "cb-chillibyte-2026")}
          value={intro}
          onChange={(value) => setAttributes({ intro: value })}
        />
      </div>
      <RepeaterField
        label={__("FAQs", "cb-chillibyte-2026")}
        value={faqs}
        onChange={(value) => setAttributes({ faqs: value })}
        fields={FIELDS}
        emptyRow={EMPTY_ROW}
      />
    </EditorBlockShell>
  );
}
