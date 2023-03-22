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
		}

		public function add_graph_box_widget() {
			wp_add_dashboard_widget(
				'graph_box_view',
				__( 'Graph box', 'graph-box' ),
				array( $this, 'render_graph_box_widget' )
			);
		}

		public function render_graph_box_widget() {
			echo '<strong>Attach graph here.</strong>';
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