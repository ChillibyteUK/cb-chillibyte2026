import { initNavToggle } from "./nav-toggle";
import { initNavDropdowns } from "./nav-dropdown";
import { initDialogs } from "./dialog";
import { initBlogCardFilters } from "./blog-card-filters";
import { initLenis } from "./lenis-init";
import { initReveal } from "./reveal";
import { initServiceDetails } from "./service-details";
import { initSingleProgress } from "./single-progress";
import { initSingleQuicklinks } from "./single-quicklinks";
import { initStatGrid } from "./stat-grid";

document.addEventListener("DOMContentLoaded", () => {
  initNavToggle();
  initNavDropdowns();
  initDialogs();
  initBlogCardFilters();
  initServiceDetails();
  initSingleProgress();
  initSingleQuicklinks();
  initStatGrid();
  initLenis();
  initReveal();
});
