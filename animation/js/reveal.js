/**
 * Lightweight grid reveal: one SVG <rect> per cell (including empty
 * ones, so the whole panel can flicker/animate, not just the shape),
 * animated purely with CSS keyframes. No nested per-cell DOM, no
 * box-shadow, no JS timers driving the animation.
 */

const SVG_NS = 'http://www.w3.org/2000/svg';

// distance functions take (row, col, maxRow, maxCol) so right-anchored
// modes can measure from the far edge/corner without knowing grid size
// up front (the grid is right-aligned in its container, so the "leading"
// edge/corner for a reveal wave is the right one, not the left).
const STAGGER_MODES = {
  'right-edge': (row, col, maxRow, maxCol) => maxCol - col,
  'right-corner': (row, col, maxRow, maxCol) => Math.sqrt(row * row + (maxCol - col) * (maxCol - col)),
  'row': (row, col) => row,
};

const SHUFFLE_PALETTE = ['var(--colour-blank)', 'var(--colour-1)', 'var(--colour-2)'];
const VARIANTS_STYLE_ID = 'reveal-shuffle-variants';

function buildReveal(svg, grid, { cellSize = 20, gap = 2 } = {}) {
  const rows = grid.length;
  const cols = grid[0].length;
  const width = cols * cellSize;
  const height = rows * cellSize;

  svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
  svg.setAttribute('width', width);
  svg.setAttribute('height', height);
  // if min-width ever forces an aspect ratio the viewBox can't fill
  // exactly, keep the shape anchored right rather than centered
  svg.setAttribute('preserveAspectRatio', 'xMaxYMid meet');
  svg.innerHTML = '';

  const cells = [];
  for (let r = 0; r < rows; r++) {
    for (let c = 0; c < cols; c++) {
      const value = grid[r][c];
      const rect = document.createElementNS(SVG_NS, 'rect');
      rect.setAttribute('x', c * cellSize + gap / 2);
      rect.setAttribute('y', r * cellSize + gap / 2);
      rect.setAttribute('width', cellSize - gap);
      rect.setAttribute('height', cellSize - gap);
      rect.setAttribute('rx', 2);
      rect.setAttribute('class', `cell value-${value}`);
      svg.appendChild(rect);
      cells.push({ el: rect, row: r, col: c, value });
    }
  }
  return cells;
}

/**
 * Generates `count` distinct @keyframes rules, each with a random
 * number of flicker steps (between minSteps/maxSteps) at random
 * offsets and random colours, all ending on the cell's own --target.
 * Injects them as a <style> tag and returns the list of animation names.
 */
function buildShuffleVariants({ count = 12, minSteps = 5, maxSteps = 12, alwaysVisible = false } = {}) {
  const names = [];
  let css = '';
  const prefix = alwaysVisible ? 'reveal-shuffle-visible-v' : 'reveal-shuffle-v';

  for (let v = 0; v < count; v++) {
    const steps = minSteps + Math.floor(Math.random() * (maxSteps - minSteps + 1));
    const offsets = [];
    for (let i = 0; i < steps; i++) offsets.push(4 + Math.random() * 88);
    offsets.sort((a, b) => a - b);

    const name = `${prefix}${v}`;
    let prev = 'var(--colour-blank)';
    // "always visible" variants never touch opacity/transform - only fill changes,
    // so there's no appear/scale wave, just a colour flicker.
    let body = alwaysVisible
      ? `0% { opacity: 1; fill: var(--colour-blank); }\n`
      : `0% { opacity: 0; transform: scale(.5); fill: var(--colour-blank); }\n`;

    offsets.forEach((off, i) => {
      let colour;
      do { colour = SHUFFLE_PALETTE[Math.floor(Math.random() * SHUFFLE_PALETTE.length)]; }
      while (colour === prev);
      prev = colour;
      if (alwaysVisible) {
        body += `${off.toFixed(2)}% { opacity: 1; fill: ${colour}; }\n`;
      } else {
        const appear = i === 0 ? 'opacity: 1; transform: scale(1); ' : '';
        body += `${off.toFixed(2)}% { ${appear}fill: ${colour}; }\n`;
      }
    });

    body += alwaysVisible
      ? `100% { opacity: 1; fill: var(--target); }\n`
      : `100% { opacity: 1; transform: scale(1); fill: var(--target); }\n`;
    css += `@keyframes ${name} { ${body} }\n`;
    names.push(name);
  }

  let styleEl = document.getElementById(VARIANTS_STYLE_ID);
  if (!styleEl) {
    styleEl = document.createElement('style');
    styleEl.id = VARIANTS_STYLE_ID;
    document.head.appendChild(styleEl);
  }
  styleEl.textContent = css; // only one variant set is ever in play at a time
  return names;
}

function playReveal(svg, cells, {
  mode = 'right-edge',
  step = 30,
  duration = 700,
  shuffle = false,
  minSteps = 5,
  maxSteps = 12,
  jitter = 8,
  alwaysVisible = false,
} = {}) {
  const distanceFn = STAGGER_MODES[mode] || STAGGER_MODES['right-edge'];
  const maxRow = Math.max(...cells.map((c) => c.row));
  const maxCol = Math.max(...cells.map((c) => c.col));

  svg.style.setProperty('--dur', `${duration}ms`);
  svg.classList.toggle('always-visible', alwaysVisible);

  const variantNames = shuffle ? buildShuffleVariants({ minSteps, maxSteps, alwaysVisible }) : null;

  // right-anchored modes sweep in from the shape's side; the padding
  // columns standardising every grid to 28x23 are pure filler with
  // nothing to reveal, so skip animating them there for a cleaner wave
  const skipTransparent = mode === 'right-edge' || mode === 'right-corner';

  // restart every cell's animation from scratch
  cells.forEach(({ el }) => { el.style.animation = 'none'; });
  void svg.getBoundingClientRect(); // force reflow

  cells.forEach(({ el, row, col, value }) => {
    if (skipTransparent && value === 0) {
      // fill:transparent, not --colour-blank. Painting blanks in the blank
      // colour made every empty cell an svg rect sitting on top of the
      // container's tiled filler, so the two had to agree pixel for pixel -
      // and they can't: the rect has rx:2 corners and antialiased edges the
      // css tile doesn't. That's what showed as a colour difference and a
      // ragged seam down the leftmost column. Letting the tile show through
      // means a blank cell is drawn exactly once, by css.
      el.style.opacity = '1';
      el.style.fill = 'transparent';
      return; // leave animation as 'none' - stays static, no flicker/delay
    }
    el.style.removeProperty('opacity');
    el.style.removeProperty('fill');
    const delay = Math.max(0, distanceFn(row, col, maxRow, maxCol) * step + (Math.random() * 2 - 1) * jitter);
    el.style.setProperty('--delay', `${delay}ms`);
    el.style.removeProperty('animation'); // clear the 'none' override, fall back to .cell's rules
    el.style.animationName = variantNames
      ? variantNames[Math.floor(Math.random() * variantNames.length)]
      : ''; // '' restores the stylesheet's default `reveal` name
  });
}

/**
 * One-call setup for a hero: fetches the grid JSON, builds the cells,
 * and plays the reveal, all with the same right-edge/flicker/always-
 * visible defaults used across the site. Pass an already-in-the-DOM
 * <svg class="grid"> sitting inside a .grid-container (see reveal.css).
 *
 *   initGridReveal(document.getElementById('grid'), { src: 'grid.json' });
 *
 * Returns a promise resolving to the cells array, in case the caller
 * wants to call playReveal(svg, cells, {...}) again later (e.g. to
 * replay on a route change, or re-run with different settings).
 */
function initGridReveal(svg, {
  src,
  cellSize = 20,
  gap = 2,
  mode = 'right-edge',
  step = 50,
  duration = 750,
  shuffle = true,
  alwaysVisible = true,
  minSteps = 5,
  maxSteps = 12,
  jitter = 8,
} = {}) {
  return fetch(src)
    .then((r) => r.json())
    .then((grid) => {
      const cells = buildReveal(svg, grid, { cellSize, gap });
      playReveal(svg, cells, { mode, step, duration, shuffle, alwaysVisible, minSteps, maxSteps, jitter });
      return cells;
    });
}

/**
 * Declarative usage - no JS call needed from the theme at all. Put
 * data-grid-reveal="<path to json>" on the <svg class="grid"> itself,
 * plus any of the other initGridReveal() options as data attributes to
 * override just that instance's defaults:
 *
 *   <svg class="grid" viewBox="0 0 560 460" preserveAspectRatio="xMaxYMid meet"
 *        data-grid-reveal="/animation/json/grid.json"
 *        data-mode="row"
 *        data-duration="1000"
 *        data-shuffle="false">
 *   </svg>
 *
 * Every attribute but data-grid-reveal itself is optional - anything
 * omitted falls back to initGridReveal()'s defaults (right-edge, step
 * 50, duration 750, shuffle on, always-visible on).
 */
const DATA_OPTION_KEYS = {
  mode: (v) => v,
  step: Number,
  duration: Number,
  shuffle: (v) => v !== 'false',
  alwaysVisible: (v) => v !== 'false',
  minSteps: Number,
  maxSteps: Number,
  jitter: Number,
  cellSize: Number,
  gap: Number,
};

function optionsFromDataset(el) {
  const opts = { src: el.dataset.gridReveal };
  for (const [key, coerce] of Object.entries(DATA_OPTION_KEYS)) {
    if (el.dataset[key] !== undefined) opts[key] = coerce(el.dataset[key]);
  }
  return opts;
}

function autoInit(root = document) {
  root.querySelectorAll('[data-grid-reveal]:not([data-grid-reveal-done])').forEach((el) => {
    el.setAttribute('data-grid-reveal-done', '');
    initGridReveal(el, optionsFromDataset(el));
  });
}

if (typeof document !== 'undefined') {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => autoInit(), { once: true });
  } else {
    autoInit();
  }
}

if (typeof module !== 'undefined') {
  module.exports = { buildReveal, playReveal, buildShuffleVariants, initGridReveal, autoInit, STAGGER_MODES };
}
