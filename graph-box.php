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
			add_action( 'wp_dashboard_setup', array ($this, 'add_graph_box_widget') );

			add_action( 'admin_enqueue_scripts', array ($this, 'graph_box_admin_scripts'), 10 );

   add_action( 'rest_api_init', array ($this, 'graph_box_rest') );
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

   // script variables
	  $rest_url = get_rest_url( null, 'graph-box/v1/data/' );

   $data = array ('rest_url' => $rest_url);

			wp_add_inline_script('graph-box', 'var graphBoxData = '. wp_json_encode( $data ));

	  wp_localize_script('graph-box', 'wpApiSettings', array(
		  'root' => esc_url_raw(rest_url()),
		  'nonce' => wp_create_nonce('wp_rest')
	  ));


		}

  public function graph_box_rest() {
    register_rest_route( 'graph-box/v1', '/data/', array(
     'methods' => 'GET',
     'callback' => array ($this, 'graph_box_data'),
     'permission_callback' => array ($this, 'validate_user'),
    ));
  }

  public function validate_user() {
   //return true;
	  $user = wp_get_current_user();
	  $allowed_roles = array('administrator');
	  if( array_intersect($allowed_roles, $user->roles ) ) {
	   return true;
   } else {
    return false;
   }

  }
  public function graph_box_data() {

   $data = array (
     array ('name' => 'Page A', 'uv' => 400, 'pv' => 2400, 'amt' => 2400),
     array ('name' => 'Page B', 'uv' => 800, 'pv' => 6400, 'amt' => 3500),
     array ('name' => 'Page V', 'uv' => 1400, 'pv' => 1500, 'amt' => 1500),
     array ('name' => 'Page E', 'uv' => 5000, 'pv' => 2300, 'amt' => 4800),
     array ('name' => 'Page Z', 'uv' => 400, 'pv' => 1800, 'amt' => 5600),
     array ('name' => 'Page W', 'uv' => 890, 'pv' => 1200, 'amt' => 9600),
     array ('name' => 'Page X', 'uv' => 4100, 'pv' => 4400, 'amt' => 8400)
   );


  if ( empty( $data ) ) {
	  return new \WP_Error( 'no_author', 'Invalid author', array( 'status' => 404 ) );
  }

  return $data;
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