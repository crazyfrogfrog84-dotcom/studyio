# StuDyIo design system

## Theme behavior

The app uses semantic CSS custom properties in `resources/css/app.css`. The token groups cover background, surfaces, glass fills, borders, primary and muted text, accents and shadows. Light and dark values are selected with `data-theme` on the root HTML element. The initial theme follows `prefers-color-scheme`; the theme preference is remembered in local storage and a small head script applies it before the page paints. A visible navigation control uses inline sun and moon SVGs and has an accessible label. The preview control lets portfolio visitors choose a theme too.

Transitions are short, keyboard focus is visible, and reduced-motion preferences are respected. Glass surfaces retain a solid fallback and a visible border where backdrop filtering is unavailable. Small screens reduce blur to limit rendering cost.

## App shell

The home, account, portfolio form, template selection and management pages use frosted panels over a soft brand aurora. Panels share the spacing scale and readable contrast tokens. Account pages use a split composition with a form panel.

## Portfolio directions

- **Simple:** editorial serif and sans-serif type, an open single column and hairline dividers. Soft UI shadows are reserved for small controls and skill chips, with borders for definition.
- **Modern:** a slate base with teal accents, profile sidebar, mesh hero, frosted project surfaces and technology-logo skill chips. Sections remain clearly labeled.
- **Creative:** dark high-contrast surfaces with red accents, oversized display type, an offset portrait and numbered sections. Rounded clay-style controls and project surfaces use subtle interaction feedback.

All three hide empty sections and adapt to phone, tablet and desktop widths. No motion is required to understand or use the content.

## Visual review status

Live screenshots at phone, tablet and desktop widths in both themes are pending because local browser access is disabled in this workspace. The `docs/screenshots/` folder lists the required captures; no mock screenshots are presented as live evidence.
