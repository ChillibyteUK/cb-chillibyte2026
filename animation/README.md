# Grid reveal animation

A lightweight "pixel panel" reveal effect for hero images: a bitmap shape
(hand, heart, speech bubble, etc.) rendered as a grid of coloured SVG
cells that animate in — either as a smooth wave or a flickering
scramble that settles into place.

Built for low overhead: one `<rect>` per cell, no nested per-cell DOM,
no box-shadow, no JS timers driving the animation (it's all CSS
`@keyframes` + `animation-delay`).

## Folder structure

```
animation/
├── json/   the bitmaps - one 28x23 array of 0/1/2 per image
├── css/    reveal.css - all layout + animation styling
└── js/     reveal.js  - builds the SVG cells and plays the reveal
```

## Quick start (declarative - no JS call needed)

```html
<link rel="stylesheet" href="/animation/css/reveal.css">

<div class="grid-container">
  <svg class="grid" viewBox="0 0 560 460" preserveAspectRatio="xMaxYMid meet"
       data-grid-reveal="/animation/json/grid.json">
  </svg>
</div>

<script src="/animation/js/reveal.js"></script>
```

That's the whole integration. `reveal.js` scans the page for
`[data-grid-reveal]` elements on load and initialises each one
automatically. The `viewBox="0 0 560 460"` and `preserveAspectRatio`
attributes should be copied verbatim - every bitmap is standardised to
28x23 cells (20px/cell internally), so this is a fixed constant, not
something that needs to be computed per image.

If your theme inserts this markup dynamically (AJAX, SPA route
change, etc.), call `autoInit(container)` after inserting it - the
automatic scan only runs once, on page load.

## Overriding defaults per instance

Every option below is optional. Anything omitted falls back to the
defaults: **right-edge** stagger, step **50ms**, duration **750ms**,
flicker **on**, always-visible **on**.

```html
<svg class="grid" viewBox="0 0 560 460" preserveAspectRatio="xMaxYMid meet"
     data-grid-reveal="/animation/json/grid-about.json"
     data-mode="row"
     data-step="30"
     data-duration="1000"
     data-shuffle="false"
     data-always-visible="false">
</svg>
```

| attribute | values | meaning |
|---|---|---|
| `data-grid-reveal` | path to a JSON file | **required** - the bitmap to reveal |
| `data-mode` | `right-edge` \| `right-corner` \| `row` | direction the reveal wave sweeps from |
| `data-step` | number (ms) | delay between each cell/row in the wave |
| `data-duration` | number (ms) | how long each individual cell's animation takes |
| `data-shuffle` | `"false"` to disable | flicker through random colours before settling |
| `data-always-visible` | `"false"` to disable | cells are visible blank tiles from frame one, only colour animates (vs. fading/scaling in from nothing) |
| `data-min-steps` / `data-max-steps` | numbers | range for how many flicker steps a cell takes (only matters with shuffle on) |
| `data-jitter` | number (ms) | per-cell random timing offset, for a less mechanical wave |
| `data-cell-size` / `data-gap` | numbers | internal SVG coordinate units - rarely needs changing |

`right-edge`/`right-corner` skip animating the transparent padding
cells that pad narrower images out to 28 columns (nothing to reveal
there); `row` mode animates every cell, padding included, for a
busier full-panel effect.

## JS API (manual usage)

For anything the data-attribute API can't express - replaying on
demand, driving multiple grids from one script, etc. - use the
functions directly:

```js
import { initGridReveal, playReveal } from '/animation/js/reveal.js';
// or: <script src="/animation/js/reveal.js"> exposes these as globals

const svg = document.getElementById('grid');

// fetches the JSON, builds the cells, plays the reveal - returns a
// promise resolving to the cells array
initGridReveal(svg, { src: '/animation/json/grid.json' }).then((cells) => {
  // replay later with different settings, no re-fetch needed
  replayButton.addEventListener('click', () => {
    playReveal(svg, cells, { mode: 'row', shuffle: true });
  });
});
```

Lower-level pieces, if you need to split fetch/build/play apart:

- `buildReveal(svg, grid, { cellSize, gap })` → creates the `<rect>` cells from a bitmap array, returns the cells array
- `playReveal(svg, cells, options)` → (re)starts the animation on an already-built grid
- `buildShuffleVariants(options)` → generates the random flicker `@keyframes`, used internally by `playReveal` when `shuffle: true`

## Styling / theming

Everything's driven by CSS custom properties on `:root` in `reveal.css`:

```css
--colour-1: #6c7086;       /* "on" cell colour */
--colour-2: #2c2e40;       /* "shadow" cell colour */
--colour-blank: #1a1b21;   /* idle/empty cell colour */
--bg: #050506;             /* gap/seam colour between cells */
--dur: 700ms;               /* overridden per-instance via playReveal's `duration` */
--ease: cubic-bezier(.22, .7, .32, 1);
--hero-max-height: 700px;  /* the hero's height clamp: min(100vh, this) */
```

For a short/tall hero variant, override just the height clamp:

```css
.hero--short .grid-container { --hero-max-height: 420px; }
.hero--tall  .grid-container { --hero-max-height: 900px; }
```

The empty space to the left of a right-anchored shape (on wide
viewports) is filled by a cheap CSS gradient pattern on
`.grid-container` itself, not extra DOM - it's automatically sized to
match the real cells via `calc()` against `--grid-rows` (23), so it
never needs JS or a resize listener to stay in sync.

## Adding a new bitmap

Bitmaps are extracted from a reference PNG (grey grid = `1`, dark
shape = `2`, white/transparent = `0`) using `extract_grid.py` in the
parent directory:

```
python3 extract_grid.py <source.png> <cols> <rows> animation/json/<name>.json
```

- `<cols>`/`<rows>` are the PNG's *native* grid dimensions (varies per
  image, typically 26-28 cols x 23 rows) - the script pads the result
  out to the standard 28x23 automatically (padding columns on the
  left, since content stays right-aligned).
- A green+red legend swatch in the bottom-right corner (if present) is
  auto-detected and forced to `1` (grey) - it's a reference marker in
  the source art, not part of the shape.
- Re-run the extraction if the source PNG changes; the script always
  re-derives from pixels, there's no manual editing of the JSON expected.

## Why 28x23

Every bitmap shares one fixed grid size so the `viewBox` can be
hardcoded directly in HTML (`0 0 560 460`) instead of being set by JS
after a fetch resolves - that's what makes the reveal have zero
load-in layout jump. 23 rows because that's every source image's
native row count (no padding needed there); 28 columns because that's
the widest of the current source images (narrower ones get left-padded).
If a future image is wider than 28 columns, bump `STANDARD_COLS` in
`extract_grid.py`, the `--grid-rows`/viewBox width in `reveal.css`, and
the hardcoded `viewBox` attribute in your markup together.
