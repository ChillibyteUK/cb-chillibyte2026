export function initServiceDetails() {
  const groups = document.querySelectorAll("[data-service-details-items]");

  if (!groups.length) {
    return;
  }

  const updateActiveItem = (items) => {
    const block = items[0]?.closest(".cb-service-details");
    const topOffset = block
      ? parseFloat(
          getComputedStyle(block).getPropertyValue(
            "--cb-service-details-top-offset",
          ),
        ) || 140
      : window.innerWidth >= 992
        ? 140
        : 96;
    let activeItem = items[0];
    let closestDistance = Number.POSITIVE_INFINITY;

    items.forEach((item) => {
      const distance = Math.abs(item.getBoundingClientRect().top - topOffset);

      if (distance < closestDistance) {
        closestDistance = distance;
        activeItem = item;
      }
    });

    items.forEach((item) => {
      item.classList.toggle("is-active", item === activeItem);
    });
  };

  groups.forEach((group) => {
    const items = Array.from(
      group.querySelectorAll("[data-service-details-item]"),
    );

    if (!items.length) {
      return;
    }

    const onUpdate = () => updateActiveItem(items);

    onUpdate();
    window.addEventListener("scroll", onUpdate, { passive: true });
    window.addEventListener("resize", onUpdate);
  });
}
