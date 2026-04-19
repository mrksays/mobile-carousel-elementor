<?php
/**
 * Plugin Name: Mobile Carousel for Elementor
 * Plugin URI:  https://github.com/your-repo/mobile-carousel-elementor
 * Description: Adds a smooth Swiper-powered mobile/tablet carousel to any Elementor Container. Configure slides per view, autoplay, arrows, and dots — all from the Elementor panel.
 * Version:     1.2.0
 * Author:      Muhammad Rameez Khalid
 * License:     GPL-2.0-or-later
 * Text Domain: mc-elementor
 */

defined( 'ABSPATH' ) || exit;

final class MC_Elementor_Plugin {

    private static ?self $instance = null;

    public static function instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'plugins_loaded', [ $this, 'init' ] );
    }

    public function init(): void {
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [ $this, 'notice_elementor_missing' ] );
            return;
        }
        add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'register_controls' ], 10, 2 );
        add_action( 'elementor/frontend/container/before_render',                   [ $this, 'before_render'    ] );
        add_action( 'wp_enqueue_scripts',                                            [ $this, 'enqueue_assets'  ] );
    }

    /* ---------------------------------------------------------------
     * Admin notice
     * ------------------------------------------------------------- */
    public function notice_elementor_missing(): void {
        echo '<div class="notice notice-warning"><p>'
           . esc_html__( 'Mobile Carousel for Elementor requires the Elementor plugin to be active.', 'mc-elementor' )
           . '</p></div>';
    }

    /* ---------------------------------------------------------------
     * Elementor controls
     * ------------------------------------------------------------- */
    public function register_controls( \Elementor\Element_Base $element ): void {

        $element->start_controls_section(
            'mc_section',
            [
                'label' => esc_html__( 'Mobile Carousel', 'mc-elementor' ),
                'tab'   => \Elementor\Controls_Manager::TAB_LAYOUT,
            ]
        );

        $element->add_control(
            'mc_enable',
            [
                'label'        => esc_html__( 'Enable Carousel', 'mc-elementor' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Yes', 'mc-elementor' ),
                'label_off'    => esc_html__( 'No', 'mc-elementor' ),
                'return_value' => 'yes',
            ]
        );

        // ── Breakpoint controls ──────────────────────────────────────
        $element->add_control(
            'mc_slides_heading',
            [
                'label'     => esc_html__( 'Slides Per View', 'mc-elementor' ),
                'type'      => \Elementor\Controls_Manager::HEADING,
                'condition' => [ 'mc_enable' => 'yes' ],
                'separator' => 'before',
            ]
        );

        $element->add_control(
            'mc_slides_mobile',
            [
                'label'     => esc_html__( 'Mobile (< 768 px)', 'mc-elementor' ),
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'default'   => 1,
                'min'       => 1,
                'max'       => 6,
                'condition' => [ 'mc_enable' => 'yes' ],
            ]
        );

        $element->add_control(
            'mc_slides_tablet',
            [
                'label'     => esc_html__( 'Tablet (768 – 1024 px)', 'mc-elementor' ),
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'default'   => 2,
                'min'       => 1,
                'max'       => 6,
                'condition' => [ 'mc_enable' => 'yes' ],
            ]
        );

        // ── Space between ────────────────────────────────────────────
        $element->add_control(
            'mc_space_between',
            [
                'label'     => esc_html__( 'Space Between Slides (px)', 'mc-elementor' ),
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'default'   => 15,
                'min'       => 0,
                'max'       => 100,
                'condition' => [ 'mc_enable' => 'yes' ],
                'separator' => 'before',
            ]
        );

        // ── Loop ─────────────────────────────────────────────────────
        $element->add_control(
            'mc_loop',
            [
                'label'        => esc_html__( 'Infinite Loop', 'mc-elementor' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'return_value' => 'yes',
                'condition'    => [ 'mc_enable' => 'yes' ],
            ]
        );

        // ── Autoplay ─────────────────────────────────────────────────
        $element->add_control(
            'mc_autoplay',
            [
                'label'        => esc_html__( 'Autoplay', 'mc-elementor' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'condition'    => [ 'mc_enable' => 'yes' ],
                'separator'    => 'before',
            ]
        );

        $element->add_control(
            'mc_speed',
            [
                'label'     => esc_html__( 'Autoplay Delay (ms)', 'mc-elementor' ),
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'default'   => 3000,
                'min'       => 500,
                'step'      => 100,
                'condition' => [
                    'mc_enable'   => 'yes',
                    'mc_autoplay' => 'yes',
                ],
            ]
        );

        $element->add_control(
            'mc_transition_speed',
            [
                'label'     => esc_html__( 'Transition Speed (ms)', 'mc-elementor' ),
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'default'   => 400,
                'min'       => 100,
                'max'       => 2000,
                'step'      => 50,
                'condition' => [ 'mc_enable' => 'yes' ],
            ]
        );

        // ── Navigation ───────────────────────────────────────────────
        $element->add_control(
            'mc_arrows',
            [
                'label'        => esc_html__( 'Arrow Navigation', 'mc-elementor' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'condition'    => [ 'mc_enable' => 'yes' ],
                'separator'    => 'before',
            ]
        );

        $element->add_control(
            'mc_dots',
            [
                'label'        => esc_html__( 'Dot Pagination', 'mc-elementor' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'condition'    => [ 'mc_enable' => 'yes' ],
            ]
        );

        $element->end_controls_section();
    }

    /* ---------------------------------------------------------------
     * Before render — attach data attribute
     * ------------------------------------------------------------- */
    public function before_render( \Elementor\Element_Base $element ): void {
        $s = $element->get_settings_for_display();

        if ( empty( $s['mc_enable'] ) || $s['mc_enable'] !== 'yes' ) {
            return;
        }

        $data = [
            'mobile'          => (int) ( $s['mc_slides_mobile']    ?? 1 ),
            'tablet'          => (int) ( $s['mc_slides_tablet']    ?? 2 ),
            'spaceBetween'    => (int) ( $s['mc_space_between']    ?? 15 ),
            'loop'            => ( ( $s['mc_loop']     ?? 'yes' ) === 'yes' ),
            'autoplay'        => ( ( $s['mc_autoplay'] ?? '' )    === 'yes' ),
            'speed'           => (int) ( $s['mc_speed']           ?? 3000 ),
            'transitionSpeed' => (int) ( $s['mc_transition_speed'] ?? 400 ),
            'arrows'          => ( ( $s['mc_arrows']   ?? '' )    === 'yes' ),
            'dots'            => ( ( $s['mc_dots']     ?? '' )    === 'yes' ),
        ];

        $element->add_render_attribute( '_wrapper', [
            'class'   => 'mc-carousel',
            'data-mc' => wp_json_encode( $data ),
        ] );
    }

    /* ---------------------------------------------------------------
     * Enqueue assets
     * ------------------------------------------------------------- */
    public function enqueue_assets(): void {
        // Swiper is bundled with Elementor — reuse it when available,
        // otherwise fall back to the CDN build.
        if ( ! wp_script_is( 'swiper', 'registered' ) ) {
            wp_register_script(
                'swiper',
                'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
                [],
                '11',
                true
            );
            wp_register_style(
                'swiper',
                'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
                [],
                '11'
            );
        }

        wp_enqueue_style(  'swiper' );
        wp_enqueue_script( 'swiper' );

        wp_enqueue_style(
            'mc-elementor-style',
            plugin_dir_url( __FILE__ ) . 'css/mobile-carousel.css',
            [ 'swiper' ],
            '1.2.0'
        );

        wp_enqueue_script(
            'mc-elementor-js',
            plugin_dir_url( __FILE__ ) . 'js/mobile-carousel.js',
            [ 'jquery', 'swiper' ],
            '1.2.0',
            true
        );
    }
}

MC_Elementor_Plugin::instance();
