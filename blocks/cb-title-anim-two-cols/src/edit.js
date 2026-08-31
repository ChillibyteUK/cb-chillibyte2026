import { __ } from "@wordpress/i18n";
import { useBlockProps, RichText } from "@wordpress/block-editor";
import { TextareaControl, SelectControl } from "@wordpress/components";
import EditorBlockShell from "../../_shared/EditorBlockShell";

export default function Edit({ attributes, setAttributes, clientId }) {
  const { title, leftText, rightText, animation } = attributes;
  const blockProps = useBlockProps({
    className: "container cb-chillibyte-2026-editor-block",
  });

  return (
    <EditorBlockShell
      blockProps={blockProps}
      clientId={clientId}
      title="CB Title, Anim, Two Cols"
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
      <SelectControl
        label={__("Animation", "cb-chillibyte-2026")}
        value={animation}
        options={[
          { label: "", value: "" },
          { label: "Plane", value: "plane" },
        ]}
        onChange={(value) => setAttributes({ animation: value })}
        help={__(
          "Add more by dropping {slug}.svg + {slug}.css into animation/hover/{slug}/, then adding a matching option here.",
          "cb-chillibyte-2026",
        )}
      />
      <div className="cb-chillibyte-2026-editor-field">
        <label className="cb-chillibyte-2026-editor-field__label">
          {__("Left Text", "cb-chillibyte-2026")}
        </label>
        <RichText
          tagName="div"
          className="cb-chillibyte-2026-editor-field__control"
          aria-label={__("Left Text", "cb-chillibyte-2026")}
          placeholder={__("Left Text", "cb-chillibyte-2026")}
          value={leftText}
          onChange={(value) => setAttributes({ leftText: value })}
        />
        <p className="cb-chillibyte-2026-editor-field__help">
          {__("rendered larger than right text", "cb-chillibyte-2026")}
        </p>
      </div>
      <div className="cb-chillibyte-2026-editor-field">
        <label className="cb-chillibyte-2026-editor-field__label">
          {__("Right Text", "cb-chillibyte-2026")}
        </label>
        <RichText
          tagName="div"
          className="cb-chillibyte-2026-editor-field__control"
          aria-label={__("Right Text", "cb-chillibyte-2026")}
          placeholder={__("Right Text", "cb-chillibyte-2026")}
          value={rightText}
          onChange={(value) => setAttributes({ rightText: value })}
        />
      </div>
    </EditorBlockShell>
  );
}
