=== Mobile Carousel for Elementor ===
Contributors: Muhammad Rameez Khalid
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds a Swiper-powered mobile/tablet carousel to any Elementor Container — configured entirely from the Elementor panel.

== Description ==

Turn any Elementor Container into a responsive carousel on mobile and tablet with zero coding.

**Features**
* Activates only on mobile (< 768 px) and tablet (768 – 1024 px) — desktop layout is untouched.
* Slides per view — set independently for mobile and tablet.
* Space between slides control.
* Infinite loop toggle.
* Autoplay with configurable delay.
* Transition speed control.
* Arrow navigation buttons.
* Dot pagination.
* Smooth cubic-bezier transitions.
* Cleans up automatically when the viewport grows above 1024 px (e.g. on device rotation).
* Works in the Elementor editor live preview.

== Installation ==

1. Upload the `mobile-carousel-elementor` folder to `/wp-content/plugins/`.
2. Activate the plugin through **Plugins → Installed Plugins**.
3. Open any page in **Elementor**.
4. Select a **Container** that holds the items you want to carousel.
5. Go to **Layout → Mobile Carousel** and toggle **Enable Carousel**.
6. Configure slides, autoplay, arrows, and dots as needed.
7. Save and preview on a mobile/tablet viewport.

== Changelog ==

= 1.2.0 =
* Added Space Between, Loop, and Transition Speed controls.
* Arrows are now `<button>` elements for accessibility.
* Added resize/destroy logic so the carousel tears down correctly when rotating to desktop.
* Elementor editor live-preview hook added.
* Scoped arrow/pagination selectors via unique IDs — multiple carousels per page now work correctly.

= 1.0.0 =
* Initial release.
