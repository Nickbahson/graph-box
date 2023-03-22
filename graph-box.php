<?php

/**
 * Graph box
 *
 * Plugin Name:       Graph box
 * Plugin URI:        none-yet
 * Description:       React graph meta box filterable by date.
 * Version:           1.0.0
 * Author:            Nicholas Babu
 * Author URI:        https://profiles.wordpress.org/bahson/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.7
 * Tested up to: 6.1
 * Requires PHP:      8.0
 */

namespace Nick\GraphBox;

if (!defined('ABSPATH')) {
	exit();
}

define( 'GRAPH_BOX_PLUGIN_DIR', __DIR__ );

if (!class_exists('Graph_Box')) {
	class Graph_Box {
		public static $instance;

		public function __construct() {
			add_action('wp_dashboard_setup', array ($this, 'add_graph_box_widget'));

			add_action( 'admin_enqueue_scripts', array ($this, 'graph_box_admin_scripts'), 10 );
		}

		public function add_graph_box_widget() {
			wp_add_dashboard_widget(
				'graph_box_view',
				__( 'Graph Widget', 'graph-box' ),
				array( $this, 'render_graph_box_widget' )
			);
		}

		public function graph_box_admin_scripts(){
			$scripts = plugins_url('/', __FILE__ ). 'build/index.js';
			wp_register_script(
				'graph-box',
				$scripts,
				array('react', 'react-dom', 'wp-api', 'wp-components', 'wp-dom-ready', 'wp-element', 'wp-i18n'),
				1,
				false
			);

			wp_enqueue_script(
				'graph-box',
				$scripts,
				array('react', 'react-dom', 'wp-api', 'wp-components', 'wp-dom-ready', 'wp-element', 'wp-i18n'),
				1,
				true
			);

			wp_add_inline_script('graph-box', 'var graphBoxData = {}');


		}

		public function render_graph_box_widget() {
			?>
			<div id="graph-box-wrapper">

			</div>
			<?php
		}


		public static function run(){
			if (!isset(self::$instance)) {
				self::$instance = new Graph_Box();
			}
			return self::$instance;
		}
	}

	Graph_Box::run();
}