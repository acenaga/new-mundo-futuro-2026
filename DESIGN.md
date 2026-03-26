# Design System Strategy: The Galactic Editorial

## 1. Overview & Creative North Star
This design system is built upon the Creative North Star of **"The Galactic Editorial."** 

We are moving away from the "generic SaaS" look of flat cards and blue buttons. Instead, we are leaning into the expansive, high-contrast aesthetic of modern space exploration—think the precision of NASA technical documents blended with the luxurious typography of high-end digital journals.

The interface must feel like a sophisticated cockpit or a premium publication from the future. We achieve this through:
*   **Intentional Asymmetry:** Breaking the 12-column grid with offset headings and overlapping imagery.
*   **Luminous Depth:** Using the provided deep purples and blues to create a "void" where content floats with purpose.
*   **Technical Accents:** Utilizing the logo's hexagonal geometry and the vibrant yellow (`#f4bf27`) to draw the eye to critical conversion points like stars in a dark sky.

## 2. Colors
Our palette is a sophisticated navigation through a dark cosmic environment.

### The Palette Logic
*   **Primary (`#110090` / `#c1c1ff`):** Represents the deep structure of the "Mundo Futuro" universe. Use `primary_container` for deep section backgrounds and `primary` for high-visibility accents.
*   **Secondary (`#4c2e84` / `#d3bbff`):** Used for "Nebula" highlights—tonal shifts that add warmth to the deep blues.
*   **Tertiary Yellow (`#f4bf27` / `#342600`):** This is our "Sun." It is reserved strictly for primary CTAs, active states, and critical highlights. It must pop against the dark backgrounds.

### The "No-Line" Rule
Standard UI relies on lines to separate content. We do not. **Prohibit 1px solid borders for sectioning.** Boundaries must be defined solely through background color shifts.
*   *Example:* A `surface_container_low` section sitting directly on a `surface` background. The difference in tone provides all the structure required.

### Glass & Gradient Rule
To move beyond a "flat" feel, use **Glassmorphism** for floating elements (like navigation bars or hovering cards). Use semi-transparent `surface_bright` colors with a 20px-40px backdrop-blur. 
*   **Signature Texture:** Use a subtle linear gradient for hero backgrounds transitioning from `primary_container` (#110090) to `surface` (#12121d) at a 135-degree angle.

## 3. Typography
We use a high-contrast pairing to balance technical precision with editorial authority.

*   **Display & Headlines (Space Grotesk):** This is our "Technical Signature." Its wide apertures and geometric forms evoke futuristic engineering. Use `display-lg` (3.5rem) with tighter letter-spacing (-0.02em) for hero sections to create an authoritative, "big-screen" feel.
*   **Body & Titles (Inter):** This is our "Workhorse." Inter provides exceptional readability for complex tutorials and long-form publications.
*   **Hierarchy Strategy:** Use `label-md` in all-caps with increased letter-spacing (0.1em) for category tags. This mimics the labeling found on technical blueprints.

## 4. Elevation & Depth
Depth is not achieved through shadows alone, but through **Tonal Layering**.

*   **The Layering Principle:** Treat the UI as stacked sheets of frosted glass. 
    *   Base Layer: `surface` (#12121d)
    *   Secondary Layer: `surface_container_low` (#1b1b25)
    *   Interactive Layer: `surface_container_high` (#292934)
*   **Ambient Shadows:** When an element must "float" (e.g., a modal), use a shadow with a blur of 40px and a 10% opacity. The shadow color should be tinted with `on_primary` (#1c1497) rather than pure black to maintain the cosmic color profile.
*   **The "Ghost Border" Fallback:** If a container requires more definition, use a "Ghost Border": the `outline_variant` token at 15% opacity. Never use 100% opaque borders.

## 5. Components

### Buttons
*   **Primary (Action):** Filled with `tertiary` (#f4bf27). Text is `on_tertiary_container` (#b38900). This is our highest contrast element.
*   **Secondary (Contextual):** `surface_container_highest` background with a `primary` text. Use a `md` (0.375rem) roundedness scale.
*   **Tertiary (Ghost):** No background. Use `primary` text and an underline that only appears on hover.

### Cards & Lists
*   **Rule:** Forbid the use of divider lines.
*   **Style:** Cards should use `surface_container_low`. Use the spacing scale `8` (2.75rem) to separate content blocks. 
*   **Hex-Accent:** Reference the logo by applying a subtle hexagonal "clip-path" to the top-right corner of featured course cards.

### Input Fields
*   **Layout:** Background uses `surface_container_lowest`. 
*   **Focus State:** Instead of a thick border, use a 2px bottom-bar in `tertiary` (#f4bf27) and a subtle `tertiary_container` glow.

### Additional Signature Components
*   **Course Progress Orbit:** A circular or hexagonal progress indicator for tutorials that uses a gradient stroke from `secondary` to `tertiary`.
*   **Cosmic Breadcrumbs:** Use `label-sm` with `/` separators in 30% opacity `on_surface` color.

## 6. Do's and Don'ts

### Do
*   **Do** use massive amounts of whitespace. Use the `16` (5.5rem) and `20` (7rem) spacing tokens for section vertical padding.
*   **Do** overlap elements. Let a headline (`display-md`) slightly overlap an image or a `surface_container`.
*   **Do** use the `tertiary` yellow sparingly. It is a "laser," not a "floodlight."

### Don't
*   **Don't** use standard black or grey shadows. It kills the "space" vibrancy.
*   **Don't** use 1px solid borders to create "grids." Let the typography and background shifts do the work.
*   **Don't** use default Inter tracking for headlines. Always tighten it for a premium editorial look.
*   **Don't** use sharp corners. Use the `md` (0.375rem) or `lg` (0.5rem) roundedness scale to keep the tech feel "approachable" and "engineered."