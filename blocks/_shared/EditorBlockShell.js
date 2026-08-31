import { __ } from "@wordpress/i18n";
import { store as blockEditorStore } from "@wordpress/block-editor";
import { useInstanceId } from "@wordpress/compose";
import { useSelect } from "@wordpress/data";
import { useEffect, useMemo, useState } from "@wordpress/element";

export default function EditorBlockShell({
  blockProps,
  clientId,
  classPrefix = "cb-chillibyte-2026",
  textDomain = "cb-chillibyte-2026",
  storageNamespace = "block",
  title,
  children,
  defaultOpen = true,
}) {
  const [isOpen, setIsOpen] = useState(defaultOpen);
  const instanceId = useInstanceId(EditorBlockShell);
  const contentId = `${classPrefix}-editor-block-content-${instanceId}`;
  const blockPath = useSelect(
    (select) => {
      if (!clientId) {
        return "";
      }

      const { getBlockIndex, getBlockRootClientId } = select(blockEditorStore);
      const path = [];
      let currentId = clientId;

      while (currentId) {
        const parentId = getBlockRootClientId(currentId) || "";
        path.unshift(String(getBlockIndex(currentId, parentId)));
        currentId = parentId || null;
      }

      return path.join(".");
    },
    [clientId],
  );
  const storageKey = useMemo(() => {
    if (!clientId || !blockPath || typeof window === "undefined") {
      return "";
    }

    return [
      classPrefix,
      "editor-block-state",
      storageNamespace,
      window.location.pathname,
      window.location.search,
      blockPath,
    ].join(":");
  }, [blockPath, classPrefix, clientId, storageNamespace]);

  useEffect(() => {
    if (!storageKey || typeof window === "undefined") {
      return;
    }

    const storedValue = window.localStorage.getItem(storageKey);

    if ("closed" === storedValue) {
      setIsOpen(false);
    } else if ("open" === storedValue) {
      setIsOpen(true);
    }
  }, [storageKey]);

  useEffect(() => {
    if (!storageKey || typeof window === "undefined") {
      return;
    }

    window.localStorage.setItem(storageKey, isOpen ? "open" : "closed");
  }, [isOpen, storageKey]);

  return (
    <div {...blockProps}>
      <div className={`${classPrefix}-editor-block__title`}>
        <span>{title}</span>
        <button
          type="button"
          className={`${classPrefix}-editor-block__toggle`}
          onClick={() => setIsOpen((open) => !open)}
          aria-expanded={isOpen}
          aria-controls={contentId}
          aria-label={
            isOpen
              ? __("Hide block fields", textDomain)
              : __("Show block fields", textDomain)
          }
        >
          <span aria-hidden="true">{isOpen ? "−" : "+"}</span>
        </button>
      </div>
      <div
        id={contentId}
        className={`${classPrefix}-editor-block__content`}
        hidden={!isOpen}
      >
        {children}
      </div>
    </div>
  );
}
