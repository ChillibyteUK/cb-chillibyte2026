export function initSingleQuicklinks() {
  const sidebars = document.querySelectorAll("[data-single-quicklinks]");

  sidebars.forEach((sidebar) => {
    if (window.innerWidth < 992) {
      return;
    }

    const links = Array.from(
      sidebar.querySelectorAll("[data-single-quicklink]"),
    );

    if (!links.length) {
      return;
    }

    const targets = links
      .map((link) => {
        const id = link.getAttribute("href")?.replace(/^#/, "");

        if (!id) {
          return null;
        }

        return {
          link,
          target: document.getElementById(id),
        };
      })
      .filter((item) => item && item.target);

    if (!targets.length) {
      return;
    }

    const setActive = (activeLink) => {
      targets.forEach(({ link }) => {
        link.classList.toggle("is-active", link === activeLink);
      });
    };

    const updateActive = () => {
      const stickyParent = sidebar.closest(".single-post__sidebar-col");
      const topOffset = stickyParent
        ? parseFloat(getComputedStyle(stickyParent).top) || 160
        : 160;
      const activationOffset = topOffset + 120;
      let activeItem = targets[0];

      targets.forEach((item) => {
        if (item.target.getBoundingClientRect().top - activationOffset <= 0) {
          activeItem = item;
        }
      });

      setActive(activeItem.link);
    };

    targets.forEach(({ link, target }) => {
      link.addEventListener("click", () => {
        setActive(link);
        if (target) {
          history.replaceState(null, "", `#${target.id}`);
        }
      });
    });

    updateActive();
    window.addEventListener("scroll", updateActive, { passive: true });
    window.addEventListener("resize", updateActive);
  });
}
