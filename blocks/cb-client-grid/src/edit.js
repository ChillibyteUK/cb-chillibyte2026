import { __ } from "@wordpress/i18n";
import {
  useBlockProps,
  MediaUpload,
  MediaUploadCheck,
} from "@wordpress/block-editor";
import { Button } from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { store as coreStore } from "@wordpress/core-data";
import EditorBlockShell from "../../_shared/EditorBlockShell";

export default function Edit({ attributes, setAttributes, clientId }) {
  const { logos } = attributes;
  const blockProps = useBlockProps({
    className: "container cb-chillibyte-2026-editor-block",
  });

  const logosMedia = useSelect(
    (select) =>
      logos.length
        ? logos.map((id) => select(coreStore).getMedia(id)).filter(Boolean)
        : [],
    [logos],
  );

  function removeLogosImage(id) {
    setAttributes({ logos: logos.filter((imageId) => imageId !== id) });
  }

  return (
    <EditorBlockShell
      blockProps={blockProps}
      clientId={clientId}
      title="CB Client Grid"
    >
      <div className="cb-chillibyte-2026-editor-field">
        <label className="cb-chillibyte-2026-editor-field__label">
          {__("Logos", "cb-chillibyte-2026")}
        </label>
        {logosMedia.length > 0 && (
          <ul
            className="cb-chillibyte-2026-repeater-field__row"
            style={{
              display: "flex",
              flexWrap: "wrap",
              gap: "8px",
              padding: 0,
              margin: "0 0 8px",
              listStyle: "none",
            }}
          >
            {logosMedia.map((item) => (
              <li key={item.id} style={{ position: "relative" }}>
                <img
                  src={
                    item.media_details?.sizes?.thumbnail?.source_url ||
                    item.source_url
                  }
                  alt=""
                  style={{
                    width: "80px",
                    height: "80px",
                    objectFit: "cover",
                    display: "block",
                    background: "#fff",
                    border: "1px solid #ccc",
                  }}
                />
                <Button
                  size="small"
                  isDestructive
                  label={__("Remove", "cb-chillibyte-2026")}
                  onClick={() => removeLogosImage(item.id)}
                  style={{
                    position: "absolute",
                    top: 0,
                    right: 0,
                    minWidth: "20px",
                    height: "20px",
                    padding: 0,
                    background: "rgba(0,0,0,.6)",
                    color: "#fff",
                  }}
                >
                  &times;
                </Button>
              </li>
            ))}
          </ul>
        )}
        <MediaUploadCheck>
          <MediaUpload
            onSelect={(selected) =>
              setAttributes({ logos: selected.map((item) => item.id) })
            }
            allowedTypes={["image"]}
            multiple
            gallery
            value={logos}
            render={({ open }) => (
              <Button variant="secondary" onClick={open}>
                {logos.length
                  ? __("Edit Gallery", "cb-chillibyte-2026")
                  : __("Select Images", "cb-chillibyte-2026")}
              </Button>
            )}
          />
        </MediaUploadCheck>
      </div>
    </EditorBlockShell>
  );
}
