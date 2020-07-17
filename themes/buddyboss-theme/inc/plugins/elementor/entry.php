<?php

namespace BBElementor;

use BBElementor\Widgets\Header_Bar;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Main BB Elementor Widgets Class
 *
 * Register new elementor widget.
 *
 * @since 1.0.0
 */
class bbElementorWidgets {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		$this->add_actions();
	}

	/**
	 * BB Categories
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function bb_elementor_widget_categories( $elements_manager ) {

		$elements_manager->add_category(
			'buddyboss-elements',
			array(
				'title' => __( 'BuddyBoss', 'buddyboss-theme' ),
				'icon'  => 'eicon-parallax',
			)
		);

	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function add_actions() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'bb_elementor_widget_categories' ) );

		add_action( 'elementor/widgets/widgets_registered', array( $this, 'bb_elementor_widgets_registered' ) );

		add_action(
			'elementor/frontend/after_register_scripts',
			function () {
				wp_register_script( 'elementor-bb-frontend', get_template_directory_uri() . '/inc/plugins/elementor/assets/js/frontend.js', array( 'jquery' ), false, true );
			}
		);

		add_action(
			'elementor/editor/after_enqueue_scripts',
			function () {
				wp_enqueue_script( 'elementor-bb-editor', get_template_directory_uri() . '/inc/plugins/elementor/assets/js/editor.js', array( 'jquery' ), false, true );
			}
		);
	}

	/**
	 * BB Widgets Registered
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function bb_elementor_widgets_registered() {
		$this->includes();
		$this->register_widget();
	}

	/**
	 * Includes
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function includes() {
		require __DIR__ . '/widgets/header-bar.php';
	}

	/**
	 * Register Widget
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function register_widget() {
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Header_Bar() );
	}
}

new bbElementorWidgets();
