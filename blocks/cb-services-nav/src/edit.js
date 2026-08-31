import { __ } from "@wordpress/i18n";
import {
  useBlockProps,
  RichText,
  MediaUpload,
  MediaUploadCheck,
} from "@wordpress/block-editor";
import { TextControl, Button } from "@wordpress/components";
import EditorBlockShell from "../../_shared/EditorBlockShell";

export default function Edit({ attributes, setAttributes, clientId }) {
  const { title, intro, imageId, imageUrl, imageAlt } = attributes;
  const blockProps = useBlockProps({
    className: "container cb-chillibyte-2026-editor-block",
  });

  function onSelectImage(media) {
    setAttributes({
      imageId: media.id,
      imageUrl: media.url,
      imageAlt: media.alt || "",
    });
  }

  function removeImage() {
    setAttributes({
      imageId: 0,
      imageUrl: "",
      imageAlt: "",
    });
  }

  return (
    <EditorBlockShell
      blockProps={blockProps}
      clientId={clientId}
      title="CB Services Nav"
    >
      <TextControl
        label={__("Title", "cb-chillibyte-2026")}
        value={title}
        onChange={(value) => setAttributes({ title: value })}
        help={__(
          "Use <strong> and <em> to embolden and emphasise",
          "cb-chillibyte-2026",
        )}
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
      <div className="cb-chillibyte-2026-editor-field">
        <label className="cb-chillibyte-2026-editor-field__label">
          {__("Intro Image", "cb-chillibyte-2026")}
        </label>
        {imageUrl ? (
          <img
            src={imageUrl}
            alt={imageAlt}
            style={{
              display: "block",
              maxWidth: "12rem",
              height: "auto",
              marginBottom: "0.75rem",
            }}
          />
        ) : null}
        <MediaUploadCheck>
          <MediaUpload
            onSelect={onSelectImage}
            allowedTypes={["image"]}
            multiple={false}
            value={imageId}
            render={({ open }) => (
              <Button variant="secondary" onClick={open}>
                {imageUrl
                  ? __("Replace Image", "cb-chillibyte-2026")
                  : __("Select Image", "cb-chillibyte-2026")}
              </Button>
            )}
          />
        </MediaUploadCheck>
        {imageUrl ? (
          <Button variant="link" isDestructive onClick={removeImage}>
            {__("Remove Image", "cb-chillibyte-2026")}
          </Button>
        ) : null}
      </div>
    </EditorBlockShell>
  );
}
