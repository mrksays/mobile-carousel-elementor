/**
 * Mobile Carousel for Elementor — mobile-carousel.js
 * Initialises Swiper on any .mc-carousel container (mobile & tablet only).
 * Works with Swiper v8 – v11.
 */
( function ( $ ) {
	'use strict';

	var BREAKPOINT = 1025; // px — above this width the carousel is inactive

	/* ----------------------------------------------------------------
	 * Core init
	 * -------------------------------------------------------------- */
	function initCarousels() {
		$( '.mc-carousel' ).each( function () {
			var $container = $( this );

			// Already initialised or viewport too wide
			if ( $container.hasClass( 'mc-ready' ) ) return;
			if ( window.innerWidth >= BREAKPOINT )         return;

			var settings = {};
			try {
				settings = JSON.parse( $container.attr( 'data-mc' ) || '{}' );
			} catch ( e ) {
				console.warn( '[mc-carousel] Bad data-mc JSON on', this, e );
				return;
			}

			// We need at least one direct child slide
			var $children = $container.children( '.e-con' );
			if ( ! $children.length ) return;

			// Mark as ready before DOM mutations so re-runs skip it
			$container.addClass( 'mc-ready swiper' );

			/* ── Build wrapper ──────────────────────────────────── */
			var $wrapper = $( '<div class="swiper-wrapper"></div>' );
			$children.each( function () {
				$( this ).addClass( 'swiper-slide' );
				$wrapper.append( this );
			} );
			$container.prepend( $wrapper );          // prepend so nav elements stay after

			/* ── Unique IDs for scoped selectors ────────────────── */
			// Swiper needs element references; we create unique suffixes so
			// multiple carousels on one page don't share selectors.
			var uid = 'mc-' + Math.random().toString( 36 ).slice( 2, 7 );

			/* ── Arrows ─────────────────────────────────────────── */
			var navigationCfg = false;
			if ( settings.arrows ) {
				var $next = $( '<button class="swiper-button-next mc-btn-next ' + uid + '-next" aria-label="Next slide"></button>' );
				var $prev = $( '<button class="swiper-button-prev mc-btn-prev ' + uid + '-prev" aria-label="Previous slide"></button>' );
				$container.append( $next ).append( $prev );
				navigationCfg = {
					nextEl: $next[ 0 ],
					prevEl: $prev[ 0 ],
				};
			}

			/* ── Dots ───────────────────────────────────────────── */
			var paginationCfg = false;
			if ( settings.dots ) {
				var $pagination = $( '<div class="swiper-pagination ' + uid + '-pagination"></div>' );
				$container.append( $pagination );
				paginationCfg = {
					el        : $pagination[ 0 ],
					clickable : true,
					dynamicBullets: false,
				};
			}

			/* ── Autoplay ───────────────────────────────────────── */
			var autoplayCfg = false;
			if ( settings.autoplay ) {
				autoplayCfg = {
					delay              : settings.speed || 3000,
					disableOnInteraction: false,
					pauseOnMouseEnter  : true,
				};
			}

			/* ── Swiper instance ────────────────────────────────── */
			new Swiper( $container[ 0 ], {
				slidesPerView   : settings.mobile || 1,
				spaceBetween    : settings.spaceBetween || 15,
				loop            : !! settings.loop,
				speed           : settings.transitionSpeed || 400,
				grabCursor      : true,
				a11y            : { enabled: true },
				autoplay        : autoplayCfg,
				navigation      : navigationCfg,
				pagination      : paginationCfg,
				breakpoints: {
					768: {
						slidesPerView: settings.tablet || 2,
						spaceBetween : settings.spaceBetween || 15,
					},
				},
				on: {
					// Re-check autoplay after user interaction
					slideChange: function () {
						if ( autoplayCfg && this.autoplay && ! this.autoplay.running ) {
							this.autoplay.start();
						}
					},
				},
			} );
		} );
	}

	/* ----------------------------------------------------------------
	 * Destroy carousels when viewport goes above breakpoint
	 * -------------------------------------------------------------- */
	function maybeDestroy() {
		if ( window.innerWidth >= BREAKPOINT ) {
			$( '.mc-carousel.mc-ready' ).each( function () {
				var $el = $( this );
				if ( $el[ 0 ].swiper ) {
					$el[ 0 ].swiper.destroy( true, true );
				}
				// Clean up classes so initCarousels can re-run on resize back
				$el.removeClass( 'mc-ready swiper' );
				// Unwrap slides — move them out of .swiper-wrapper back to container
				var $slides = $el.find( '.swiper-wrapper > .e-con' );
				$slides.removeClass( 'swiper-slide' );
				$el.find( '.swiper-wrapper' ).replaceWith( $slides );
				// Remove injected nav elements
				$el.find( '.swiper-button-next, .swiper-button-prev, .swiper-pagination' ).remove();
			} );
		}
	}

	/* ----------------------------------------------------------------
	 * Entry points
	 * -------------------------------------------------------------- */
	$( window ).on( 'load', initCarousels );

	// Resize handling — debounced
	var resizeTimer;
	$( window ).on( 'resize', function () {
		clearTimeout( resizeTimer );
		resizeTimer = setTimeout( function () {
			maybeDestroy();
			initCarousels();
		}, 250 );
	} );

	// Elementor editor live-preview support
	if ( window.elementorFrontend ) {
		$( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/container/default', function () {
				initCarousels();
			} );
		} );
	}

} )( jQuery );
