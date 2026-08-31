import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";
import EditorBlockShell from "../../_shared/EditorBlockShell";

export default function Edit({ attributes, setAttributes, clientId }) {
  const blockProps = useBlockProps({
    className: "container cb-chillibyte-2026-editor-block",
  });

  return (
    <EditorBlockShell
      blockProps={blockProps}
      clientId={clientId}
      classPrefix="cb-chillibyte-2026"
      textDomain="cb-chillibyte-2026"
      title="CB Sibling Nav"
    >
      <p>
        {__(
          "Outputs short links to sibling pages and only appears on child pages.",
          "cb-chillibyte-2026",
        )}
      </p>
    </EditorBlockShell>
  );
}
