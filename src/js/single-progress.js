export function initSingleProgress() {
  const bar = document.querySelector("[data-single-progress]");
  const contentCol = document.querySelector(".single-post__content-col");

  if (!bar || !contentCol) {
    return;
  }

  const body = bar.closest(".single-post__body");
  const progressWrap = bar.closest(".single-post__progress");

  if (!body || !progressWrap) {
    return;
  }

  const update = () => {
    const headerHeight =
      parseFloat(
        getComputedStyle(document.documentElement).getPropertyValue(
          "--header-height",
        ),
      ) || 0;
    const adminBarHeight =
      parseFloat(
        getComputedStyle(document.documentElement).getPropertyValue(
          "--admin-bar-height",
        ),
      ) || 0;
    const stickyTop = headerHeight + adminBarHeight + progressWrap.offsetHeight;

    const rect = contentCol.getBoundingClientRect();
    const contentHeight = contentCol.offsetHeight;
    const viewportHeight = window.innerHeight;

    const totalScrollable = contentHeight - viewportHeight + stickyTop;

    if (totalScrollable <= 0) {
      bar.style.transform = "scaleX(1)";
      return;
    }

    const scrolled = stickyTop - rect.top;
    const progress = Math.min(Math.max(scrolled / totalScrollable, 0), 1);

    bar.style.transform = `scaleX(${progress})`;
  };

  update();
  window.addEventListener("scroll", update, { passive: true });
  window.addEventListener("resize", update);
}
