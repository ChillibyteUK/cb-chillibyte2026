export function initBlogCardFilters() {
  const filterGroups = document.querySelectorAll("[data-blog-filter-group]");

  filterGroups.forEach((group) => {
    const block = group.closest(".cb-blog-cards");

    if (!block) {
      return;
    }

    const buttons = group.querySelectorAll("[data-blog-filter]");
    const cards = block.querySelectorAll("[data-blog-card]");

    if (!buttons.length || !cards.length) {
      return;
    }

    const setActiveFilter = (selectedFilter) => {
      buttons.forEach((button) => {
        const isActive = button.dataset.blogFilter === selectedFilter;

        button.classList.toggle("is-active", isActive);
        button.setAttribute("aria-pressed", isActive ? "true" : "false");
      });

      cards.forEach((card) => {
        const categories = (card.dataset.categories || "")
          .split(" ")
          .filter(Boolean);
        const shouldShow =
          selectedFilter === "all" || categories.includes(selectedFilter);

        card.hidden = !shouldShow;
      });
    };

    buttons.forEach((button) => {
      button.addEventListener("click", () => {
        setActiveFilter(button.dataset.blogFilter || "all");
      });
    });

    setActiveFilter("all");
  });
}
