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
  const { awards } = attributes;
  const blockProps = useBlockProps({
    className: "container cb-chillibyte-2026-editor-block",
  });

  const awardsMedia = useSelect(
    (select) =>
      awards.length
        ? awards.map((id) => select(coreStore).getMedia(id)).filter(Boolean)
        : [],
    [awards],
  );

  function removeAwardsImage(id) {
    setAttributes({ awards: awards.filter((imageId) => imageId !== id) });
  }

  return (
    <EditorBlockShell
      blockProps={blockProps}
      clientId={clientId}
      title="CB Awards Strip"
    >
      <div className="cb-chillibyte-2026-editor-field">
        <label className="cb-chillibyte-2026-editor-field__label">
          {__("Awards", "cb-chillibyte-2026")}
        </label>
        {awardsMedia.length > 0 && (
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
            {awardsMedia.map((item) => (
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
                  onClick={() => removeAwardsImage(item.id)}
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
        {awards.length === 0 && (
          <p className="cb-chillibyte-2026-editor-field__help">
            {__(
              "No images selected — the site-wide Awards Gallery from Site-Wide Settings will be shown. Select images here to override it for this block only.",
              "cb-chillibyte-2026",
            )}
          </p>
        )}
        <MediaUploadCheck>
          <MediaUpload
            onSelect={(selected) =>
              setAttributes({ awards: selected.map((item) => item.id) })
            }
            allowedTypes={["image"]}
            multiple
            gallery
            value={awards}
            render={({ open }) => (
              <Button variant="secondary" onClick={open}>
                {awards.length
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
