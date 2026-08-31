import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";
import { TextareaControl } from "@wordpress/components";
import EditorBlockShell from "../../_shared/EditorBlockShell";

export default function Edit({ attributes, setAttributes, clientId }) {
  const { title, intro } = attributes;
  const blockProps = useBlockProps({
    className: "container cb-chillibyte-2026-editor-block",
  });

  return (
    <EditorBlockShell
      blockProps={blockProps}
      clientId={clientId}
      classPrefix="cb-chillibyte-2026"
      textDomain="cb-chillibyte-2026"
      title="CB Team Cards"
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
      <p>
        {__(
          "Cards are pulled automatically from the People post type using the featured image, title, and Role meta field.",
          "cb-chillibyte-2026",
        )}
      </p>
    </EditorBlockShell>
  );
}
