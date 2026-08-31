import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";
import {
  TextControl,
  TextareaControl,
  SelectControl,
  ToggleControl,
} from "@wordpress/components";
import EditorBlockShell from "../../_shared/EditorBlockShell";
// GENERATED at build time from animation/json/grid-*.json — see
// src/build/generate-hero-animations.js. Don't hand-maintain this list: an
// option with no matching bitmap 404s and silently renders nothing.
import { HERO_ANIMATIONS, DEFAULT_HERO_ANIMATION } from "./animations";

export default function Edit({ attributes, setAttributes, clientId }) {
  const {
    isHomepage,
    animation,
    heroTitle,
    subtitle,
    ctaText,
    ctaUrl,
    ctaTarget,
  } = attributes;
  const blockProps = useBlockProps({
    className: "container cb-chillibyte-2026-editor-block",
  });

  return (
    <EditorBlockShell
      blockProps={blockProps}
      clientId={clientId}
      title="CB Hero"
    >
      <div style={{ display: "flex", flexWrap: "wrap", gap: "12px" }}>
        <div style={{ flex: "50 1 0%" }}>
          <ToggleControl
            label={__("Is Homepage?", "cb-chillibyte-2026")}
            checked={isHomepage}
            onChange={(value) => setAttributes({ isHomepage: value })}
          />
        </div>
        <div style={{ flex: "50 1 0%" }}>
          <SelectControl
            label={__("Animation", "cb-chillibyte-2026")}
            value={animation}
            options={[
              { label: `— default (${DEFAULT_HERO_ANIMATION}) —`, value: "" },
              ...HERO_ANIMATIONS.map((slug) => ({ label: slug, value: slug })),
            ]}
            onChange={(value) => setAttributes({ animation: value })}
          />
        </div>
      </div>
      <TextareaControl
        label={__("Hero Title", "cb-chillibyte-2026")}
        value={heroTitle}
        onChange={(value) => setAttributes({ heroTitle: value })}
        help={__(
          "Wrap a word in <strong> to bold it, or <em> to underline it.",
          "cb-chillibyte-2026",
        )}
      />
      <TextControl
        label={__("Subtitle", "cb-chillibyte-2026")}
        value={subtitle}
        onChange={(value) => setAttributes({ subtitle: value })}
      />
      <TextControl
        label={__("CTA Text", "cb-chillibyte-2026")}
        value={ctaText}
        onChange={(value) => setAttributes({ ctaText: value })}
      />
      <TextControl
        type="url"
        label={__("CTA URL", "cb-chillibyte-2026")}
        value={ctaUrl}
        onChange={(value) => setAttributes({ ctaUrl: value })}
      />
      <ToggleControl
        label={__("Open CTA in a new tab", "cb-chillibyte-2026")}
        checked={ctaTarget}
        onChange={(value) => setAttributes({ ctaTarget: value })}
      />
    </EditorBlockShell>
  );
}
