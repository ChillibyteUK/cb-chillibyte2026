export function initStatGrid() {
  const statValues = document.querySelectorAll("[data-stat-target]");

  if (!statValues.length) {
    return;
  }

  const animateValue = (element) => {
    const target = Number(element.dataset.statTarget || 0);

    if (!Number.isFinite(target)) {
      element.textContent = "0";
      return;
    }

    const duration = 1400;
    const start = performance.now();

    const step = (now) => {
      const progress = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      element.textContent = Math.round(target * eased).toLocaleString();

      if (progress < 1) {
        window.requestAnimationFrame(step);
      }
    };

    window.requestAnimationFrame(step);
  };

  if (!("IntersectionObserver" in window)) {
    statValues.forEach(animateValue);
    return;
  }

  const observer = new IntersectionObserver(
    (entries, currentObserver) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return;
        }

        animateValue(entry.target);
        currentObserver.unobserve(entry.target);
      });
    },
    { threshold: 0.35 },
  );

  statValues.forEach((element) => {
    observer.observe(element);
  });
}
