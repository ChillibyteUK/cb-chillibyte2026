/*!
 * cb-chillibyte-2026 v1.0.0 (https://github.com/ChillibyteUK/cb-chillibyte-2026)
 * Copyright 2026 Chillibyte - DS
 * Licensed under GPL-3.0
 */
(function () {
	'use strict';

	/**
	 * Mobile nav toggle. Wires any button with aria-controls pointing at a
	 * .navbar-collapse to show/hide it and keep aria-expanded in sync — this is
	 * the entire replacement for Bootstrap's Collapse component for this use case.
	 */
	function initNavToggle() {
	  document.querySelectorAll('.navbar-toggler[aria-controls]').forEach(toggler => {
	    const target = document.getElementById(toggler.getAttribute('aria-controls'));
	    if (!target) return;
	    toggler.addEventListener('click', () => {
	      const isOpen = target.classList.toggle('is-open');
	      toggler.setAttribute('aria-expanded', String(isOpen));
	    });

	    // Close after choosing a link — expected mobile nav behaviour.
	    target.querySelectorAll('a').forEach(link => {
	      link.addEventListener('click', () => {
	        target.classList.remove('is-open');
	        toggler.setAttribute('aria-expanded', 'false');
	      });
	    });
	  });
	}

	/**
	 * Click-to-open nav dropdowns. Each dropdown-toggle button shows/hides its
	 * linked .dropdown-menu and keeps aria-expanded in sync. Clicking elsewhere,
	 * or pressing Escape, closes whatever is open — this is the entire
	 * replacement for hover-based submenus.
	 */
	function initNavDropdowns() {
	  const toggles = document.querySelectorAll('.dropdown-toggle[aria-controls]');
	  function close(toggle) {
	    const menu = document.getElementById(toggle.getAttribute('aria-controls'));
	    if (!menu) return;
	    menu.classList.remove('is-open');
	    toggle.setAttribute('aria-expanded', 'false');
	  }
	  function closeAllExcept(except) {
	    toggles.forEach(toggle => {
	      if (toggle !== except) close(toggle);
	    });
	  }
	  toggles.forEach(toggle => {
	    const menu = document.getElementById(toggle.getAttribute('aria-controls'));
	    if (!menu) return;
	    toggle.addEventListener('click', event => {
	      event.stopPropagation();
	      const isOpen = menu.classList.toggle('is-open');
	      toggle.setAttribute('aria-expanded', String(isOpen));
	      closeAllExcept(toggle);
	    });
	  });
	  document.addEventListener('click', event => {
	    if (event.target.closest('.dropdown-menu')) return;
	    closeAllExcept();
	  });
	  document.addEventListener('keydown', event => {
	    if (event.key !== 'Escape') return;
	    const openToggle = Array.from(toggles).find(toggle => toggle.getAttribute('aria-expanded') === 'true');
	    closeAllExcept();
	    if (openToggle) openToggle.focus();
	  });
	}

	/**
	 * Native <dialog> wiring — replaces Bootstrap's Modal component entirely.
	 * showModal()/close() do the heavy lifting (focus trap, Escape-to-close,
	 * ::backdrop); this just connects trigger/close buttons to a target dialog.
	 *
	 * Markup:
	 *   <button data-dialog-target="my-dialog">Open</button>
	 *   <dialog id="my-dialog">
	 *     <button data-dialog-close>Close</button>
	 *     ...
	 *   </dialog>
	 */
	function initDialogs() {
	  document.querySelectorAll('[data-dialog-target]').forEach(trigger => {
	    const dialog = document.getElementById(trigger.getAttribute('data-dialog-target'));
	    if (!(dialog instanceof HTMLDialogElement)) return;
	    trigger.addEventListener('click', () => dialog.showModal());
	    dialog.querySelectorAll('[data-dialog-close]').forEach(closeBtn => {
	      closeBtn.addEventListener('click', () => dialog.close());
	    });

	    // Click on the backdrop (the dialog element itself, outside its content) closes it.
	    dialog.addEventListener('click', event => {
	      if (event.target === dialog) dialog.close();
	    });
	  });
	}

	function initBlogCardFilters() {
	  const filterGroups = document.querySelectorAll("[data-blog-filter-group]");
	  filterGroups.forEach(group => {
	    const block = group.closest(".cb-blog-cards");
	    if (!block) {
	      return;
	    }
	    const buttons = group.querySelectorAll("[data-blog-filter]");
	    const cards = block.querySelectorAll("[data-blog-card]");
	    if (!buttons.length || !cards.length) {
	      return;
	    }
	    const setActiveFilter = selectedFilter => {
	      buttons.forEach(button => {
	        const isActive = button.dataset.blogFilter === selectedFilter;
	        button.classList.toggle("is-active", isActive);
	        button.setAttribute("aria-pressed", isActive ? "true" : "false");
	      });
	      cards.forEach(card => {
	        const categories = (card.dataset.categories || "").split(" ").filter(Boolean);
	        const shouldShow = selectedFilter === "all" || categories.includes(selectedFilter);
	        card.hidden = !shouldShow;
	      });
	    };
	    buttons.forEach(button => {
	      button.addEventListener("click", () => {
	        setActiveFilter(button.dataset.blogFilter || "all");
	      });
	    });
	    setActiveFilter("all");
	  });
	}

	/**
	 * Smooth scroll via Lenis (https://lenis.darkroom.engineering/), loaded as a
	 * global from the CDN build in inc/enqueue.php — 'lenis' is a script
	 * dependency of theme.min.js so window.Lenis is guaranteed to exist here.
	 */
	function initLenis() {
	  if (typeof Lenis === 'undefined') return;
	  const lenis = new Lenis();
	  function raf(time) {
	    lenis.raf(time);
	    requestAnimationFrame(raf);
	  }
	  requestAnimationFrame(raf);
	}

	/**
	 * [data-reveal] — this theme's lightweight, hand-rolled equivalent of AOS.
	 * See src/css/animate.css for the CSS half of this convention.
	 *
	 * Three globals, all exposed on window (not just ES exports) specifically
	 * so per-block view.js files can call them — those are separate, unbundled
	 * scripts outside this rollup build and can't import from it:
	 *
	 * - window.cbReveal(el, delaySeconds) — animates one element in. Also
	 *   auto-runs window.cbScribble(el) the instant its own fade-in completes,
	 *   so any revealed element containing an <em> gets the RoughNotation
	 *   underline for free, correctly timed, no extra wiring needed.
	 * - window.cbScribble(container) — RoughNotation-underlines every <em>
	 *   inside `container`. Standalone too, e.g. cb-hero calls this directly
	 *   on its own h1 once the typewriter finishes, since that element isn't
	 *   part of the [data-reveal] system at all.
	 * - window.cbRevealSequence(container) — staggers every [data-reveal]
	 *   child of `container` in DOM order via window.cbReveal.
	 * - window.cbRevealRows(container) — observes each [data-reveal] child
	 *   separately and staggers only across each visual row. See below.
	 *
	 * Default behaviour: any [data-reveal-container] auto-sequences its
	 * [data-reveal] children the moment it scrolls into view. A standalone
	 * [data-reveal] with no [data-reveal-container] ancestor auto-reveals
	 * itself the same way.
	 *
	 * Grids taller than the viewport want [data-reveal-rows] instead.
	 * [data-reveal-container] triggers on the *container*, so a long card grid
	 * animates every item the instant its top edge appears — the cascade runs
	 * off-screen and everything below the fold is already faded in by the time
	 * you scroll to it. [data-reveal-rows] observes each item on its own, so a
	 * row animates as you reach it, and staggers by the item's position within
	 * its own row so the cascade still reads left-to-right. Both honour
	 * data-reveal-stagger; the two are alternatives, don't put both on one
	 * element. blocks/cb-team-cards is the worked example (20 cards, ~2,700px
	 * tall four-up and ~11,000px stacked one-up).
	 *
	 * Opt-out: add data-reveal-manual to a [data-reveal-container] (or a
	 * standalone [data-reveal]) and this file leaves it alone — some other
	 * script decides when to trigger it instead, by calling
	 * window.cbRevealSequence()/window.cbReveal() directly. cb-hero uses this
	 * for its subtitle/CTA, gated on its typewriter finishing rather than on
	 * scroll position.
	 */

	window.cbScribble = function (container) {
	  if (typeof RoughNotation === 'undefined') {
	    /*
	     * Warn rather than return quietly. rough-notation is a global
	     * dependency of this bundle (see inc/enqueue.php), so reaching here
	     * means it failed to load — CDN blocked, offline, or the dependency
	     * dropped from the enqueue. Silent was how this went unnoticed on
	     * every page but two: a missing underline is indistinguishable from
	     * a heading that simply has no <em> in it.
	     */
	    if (window.console && console.warn) {
	      console.warn('cbScribble: RoughNotation is not loaded — .cb-scribble-text underlines will not render.');
	    }
	    return;
	  }
	  container.querySelectorAll('em').forEach(function (em) {
	    RoughNotation.annotate(em, {
	      type: 'underline',
	      color: '#FF4848',
	      strokeWidth: 5,
	      iterations: 3,
	      padding: 4,
	      animationDuration: 700,
	      multiline: true
	    }).show();
	  });
	};
	window.cbReveal = function (el, delaySeconds) {
	  if (typeof gsap === 'undefined') {
	    el.style.opacity = '1';
	    el.style.transform = 'none';
	    window.cbScribble(el);
	    return;
	  }
	  gsap.to(el, {
	    opacity: 1,
	    /*
	     * Every transform component the CSS half might start an element
	     * from, reset to its resting value — see [data-reveal-from] in
	     * src/css/animate.css. Each is a no-op for elements that weren't
	     * offset that way, so one tween serves every variant; animating y
	     * alone would leave a horizontally offset or scaled element
	     * stranded at its start state, faded in but never arriving.
	     */
	    x: 0,
	    y: 0,
	    scale: 1,
	    duration: 0.6,
	    ease: 'power2.out',
	    delay: delaySeconds || 0,
	    onComplete: function () {
	      window.cbScribble(el);
	    }
	  });
	};
	window.cbRevealSequence = function (container, stagger) {
	  var gap = stagger || parseFloat(container.dataset.revealStagger) || 0.15;
	  container.querySelectorAll('[data-reveal]').forEach(function (el, i) {
	    window.cbReveal(el, i * gap);
	  });
	};
	window.cbRevealRows = function (container, stagger) {
	  var gap = stagger || parseFloat(container.dataset.revealStagger) || 0.08;
	  var items = Array.prototype.slice.call(container.querySelectorAll('[data-reveal]'));
	  items.forEach(function (el) {
	    cbObserveOnce(el, function () {
	      /*
	       * Stagger by position within the item's own visual row, worked out
	       * from layout rather than from DOM index, so one implementation
	       * covers every breakpoint: whatever the grid resolves to (4-up,
	       * 3-up, 1-up) is what this reads.
	       *
	       * offsetTop/offsetLeft, not getBoundingClientRect, on purpose —
	       * these are pre-transform layout values, so an item part-way
	       * through its own zoom/slide tween still reports the position it
	       * occupies in the grid. Rect-based grouping would drift as soon as
	       * neighbours started animating.
	       */
	      var column = items.filter(function (other) {
	        return other.offsetTop === el.offsetTop && other.offsetLeft < el.offsetLeft;
	      }).length;
	      window.cbReveal(el, column * gap);
	    });
	  });
	};
	function cbObserveOnce(el, callback) {
	  if (typeof IntersectionObserver === 'undefined') {
	    callback();
	    return;
	  }
	  /*
	   * threshold is a fraction of the *target*, not of the viewport — so a
	   * ratio-based trigger is unreachable for any target taller than
	   * viewport / ratio, and the callback simply never runs. The old
	   * { threshold: 0.2 } silently did this to the About page's team grid:
	   * that grid is its own [data-reveal-container], and stacked one-up at
	   * mobile it stands 11,275px tall, so 20% of it is 2,255px — impossible to
	   * show at once in an 812px viewport (peak ratio 0.072). All 20 cards
	   * stayed at opacity 0 permanently. It only worked on desktop because
	   * four-up makes the same grid 2,665px, where 20% fits on screen.
	   *
	   * rootMargin sidesteps the whole problem: shrinking the root's bottom
	   * edge by 15% means "fire once the target's top edge has risen into the
	   * top 85% of the viewport", which is a statement about position rather
	   * than proportion and so doesn't care how tall the target is. Timing for
	   * normal-sized elements is close to what the 0.2 ratio used to give.
	   */
	  var observer = new IntersectionObserver(function (entries, obs) {
	    entries.forEach(function (entry) {
	      if (!entry.isIntersecting) return;
	      callback();
	      obs.unobserve(entry.target);
	    });
	  }, {
	    threshold: 0,
	    rootMargin: '0px 0px -15% 0px'
	  });
	  observer.observe(el);
	}
	function initReveal() {
	  if (typeof gsap === 'undefined' || !('IntersectionObserver' in window)) {
	    document.querySelectorAll('[data-reveal]').forEach(function (el) {
	      el.style.opacity = '1';
	      el.style.transform = 'none';
	    });
	    return;
	  }
	  document.querySelectorAll('[data-reveal-container]:not([data-reveal-manual])').forEach(function (container) {
	    cbObserveOnce(container, function () {
	      window.cbRevealSequence(container);
	    });
	  });
	  document.querySelectorAll('[data-reveal-rows]:not([data-reveal-manual])').forEach(function (container) {
	    window.cbRevealRows(container);
	  });
	  document.querySelectorAll('[data-reveal]:not([data-reveal-manual])').forEach(function (el) {
	    if (el.closest('[data-reveal-container], [data-reveal-rows]')) return;
	    cbObserveOnce(el, function () {
	      window.cbReveal(el);
	    });
	  });
	}

	function initServiceDetails() {
	  const groups = document.querySelectorAll("[data-service-details-items]");
	  if (!groups.length) {
	    return;
	  }
	  const updateActiveItem = items => {
	    const block = items[0]?.closest(".cb-service-details");
	    const topOffset = block ? parseFloat(getComputedStyle(block).getPropertyValue("--cb-service-details-top-offset")) || 140 : window.innerWidth >= 992 ? 140 : 96;
	    let activeItem = items[0];
	    let closestDistance = Number.POSITIVE_INFINITY;
	    items.forEach(item => {
	      const distance = Math.abs(item.getBoundingClientRect().top - topOffset);
	      if (distance < closestDistance) {
	        closestDistance = distance;
	        activeItem = item;
	      }
	    });
	    items.forEach(item => {
	      item.classList.toggle("is-active", item === activeItem);
	    });
	  };
	  groups.forEach(group => {
	    const items = Array.from(group.querySelectorAll("[data-service-details-item]"));
	    if (!items.length) {
	      return;
	    }
	    const onUpdate = () => updateActiveItem(items);
	    onUpdate();
	    window.addEventListener("scroll", onUpdate, {
	      passive: true
	    });
	    window.addEventListener("resize", onUpdate);
	  });
	}

	function initSingleProgress() {
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
	    const headerHeight = parseFloat(getComputedStyle(document.documentElement).getPropertyValue("--header-height")) || 0;
	    const adminBarHeight = parseFloat(getComputedStyle(document.documentElement).getPropertyValue("--admin-bar-height")) || 0;
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
	  window.addEventListener("scroll", update, {
	    passive: true
	  });
	  window.addEventListener("resize", update);
	}

	function initSingleQuicklinks() {
	  const sidebars = document.querySelectorAll("[data-single-quicklinks]");
	  sidebars.forEach(sidebar => {
	    if (window.innerWidth < 992) {
	      return;
	    }
	    const links = Array.from(sidebar.querySelectorAll("[data-single-quicklink]"));
	    if (!links.length) {
	      return;
	    }
	    const targets = links.map(link => {
	      const id = link.getAttribute("href")?.replace(/^#/, "");
	      if (!id) {
	        return null;
	      }
	      return {
	        link,
	        target: document.getElementById(id)
	      };
	    }).filter(item => item && item.target);
	    if (!targets.length) {
	      return;
	    }
	    const setActive = activeLink => {
	      targets.forEach(({
	        link
	      }) => {
	        link.classList.toggle("is-active", link === activeLink);
	      });
	    };
	    const updateActive = () => {
	      const stickyParent = sidebar.closest(".single-post__sidebar-col");
	      const topOffset = stickyParent ? parseFloat(getComputedStyle(stickyParent).top) || 160 : 160;
	      const activationOffset = topOffset + 120;
	      let activeItem = targets[0];
	      targets.forEach(item => {
	        if (item.target.getBoundingClientRect().top - activationOffset <= 0) {
	          activeItem = item;
	        }
	      });
	      setActive(activeItem.link);
	    };
	    targets.forEach(({
	      link,
	      target
	    }) => {
	      link.addEventListener("click", () => {
	        setActive(link);
	        if (target) {
	          history.replaceState(null, "", `#${target.id}`);
	        }
	      });
	    });
	    updateActive();
	    window.addEventListener("scroll", updateActive, {
	      passive: true
	    });
	    window.addEventListener("resize", updateActive);
	  });
	}

	function initStatGrid() {
	  const statValues = document.querySelectorAll("[data-stat-target]");
	  if (!statValues.length) {
	    return;
	  }
	  const animateValue = element => {
	    const target = Number(element.dataset.statTarget || 0);
	    if (!Number.isFinite(target)) {
	      element.textContent = "0";
	      return;
	    }
	    const duration = 1400;
	    const start = performance.now();
	    const step = now => {
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
	  const observer = new IntersectionObserver((entries, currentObserver) => {
	    entries.forEach(entry => {
	      if (!entry.isIntersecting) {
	        return;
	      }
	      animateValue(entry.target);
	      currentObserver.unobserve(entry.target);
	    });
	  }, {
	    threshold: 0.35
	  });
	  statValues.forEach(element => {
	    observer.observe(element);
	  });
	}

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

})();
//# sourceMappingURL=theme.js.map
