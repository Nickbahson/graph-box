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

use WP_REST_Request;

if ( ! defined('ABSPATH' ) ) {
	exit();
}

define( 'GRAPH_BOX_PLUGIN_DIR', __DIR__ );

if ( ! class_exists('Graph_Box' ) ) {
	class Graph_Box {
		public static $instance;

		public function __construct() {

	  // On install or update
	  register_activation_hook( __FILE__, array( $this, 'graph_box_install_setup' ) );

	  register_deactivation_hook( __FILE__, array( $this, 'graph_box_uninstall_setup' ) );

			add_action( 'wp_dashboard_setup', array($this, 'graph_box_add_widget') );


   add_action( 'rest_api_init', array($this, 'graph_box_rest') );
		}

	 /**
   * Initial setup.
	  * @return void
	  */
  public function graph_box_install_setup() {
	  global $wpdb;
	  $table = $wpdb->prefix . "graph_box_entries";

	  $charset        = $wpdb->get_charset_collate();

	  $sql = "CREATE TABLE $table(
 		id mediumint NOT NULL AUTO_INCREMENT,
 		amount bigint NOT NULL,
 		sale_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
 		PRIMARY KEY (id)
 	    )$charset;";

	  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	  dbDelta( $sql );

   // Generate fake data:
      for ( $i = 0; $i < 300; $i++ ) {
       $date = date('Y-m-d H:i:s', strtotime( '+'.mt_rand( 2, 700 ).' days' ) );
       $amount = mt_rand( 10, 3000 );
       $data = array (
         'amount' => $amount,
         'sale_at' => $date
       );

	      $wpdb->insert( $table, $data );
      }

  }

	 /**
   * Clean up.
	  * @return void
	  */
  public function graph_box_uninstall_setup() {
	  global $wpdb;
	  $tables = array( $wpdb->prefix . "graph_box_entries" );

	  foreach ( $tables as $table ) {
		  $sql = "DROP TABLE IF EXISTS $table";
		  $wpdb->query( $sql );
	  }

  }

	 /**
   * Defines a dashboard widget.
	  * @return void
	  */
  public function graph_box_add_widget() {
			wp_add_dashboard_widget(
				'graph_box_view',
				__( 'Graph Box', 'graph-box' ),
				array( $this, 'graph_box_render_widget' )
			);
		}


	 /**
   * Defines a rest route.
	  * @return void
	  */
  public function graph_box_rest() {
    register_rest_route( 'graph-box/v1', '/data/', array(
     'methods' => 'GET',
     'callback' => array ( $this, 'graph_box_data' ),
     'permission_callback' => array ( $this, 'graph_box_validate_user' ),
    ) );
  }

  public function graph_box_validate_user() {
	  $user = wp_get_current_user();
	  $allowed_roles = array( 'administrator' );
	  if( array_intersect( $allowed_roles, $user->roles ) ) {
	   return true;
   } else {
    return false;
   }

  }

	 /**
   * Gets the data to be used by the graph, according to filters passed.
	  * @param WP_REST_Request $request
	  *
	  * @return array|object|\stdClass[]|\WP_Error
	  */
  public function graph_box_data( WP_REST_Request $request ) {

   if ( null == $request->get_param( 'days' ) ) {
	   return new \WP_Error( 'no_data', 'The range in days, must be included', array( 'status' => 400 ) );
   }

   $days = intval( $request->get_param( 'days' ) );

	  global $wpdb;

	  $prepared = array();

   $table = $wpdb->prefix . "graph_box_entries";

	  $data_sql = "SELECT DATE_FORMAT(sale_at, '%Y-%m-%d') as period_start_date, SUM(amount) as total_amount FROM {$table}";
	  $prepared[]  = $wpdb->prepare("GROUP BY FLOOR(DATEDIFF(sale_at, '2020-01-01') / %s)", $days);

	  $data_sql .= ' '.join( $prepared );

	  $data = $wpdb->get_results( $data_sql, OBJECT );


  if ( empty( $data ) ) {
	  return new \WP_Error( 'no_data', 'No data to show', array( 'status' => 404 ) );
  }

  return $data;
  }

	 /**
   * Outputs the markup to attach the react app to.
	  * @return void
	  */
  public function graph_box_render_widget() {

   // Include scripts related to this dom element/widget only when it's rendered.
	  $scripts = plugins_url( '/', __FILE__ ). 'build/index.js';
	  wp_enqueue_script(
		  'graph-box',
		  $scripts,
		  array( 'react', 'react-dom', 'wp-components', 'wp-dom-ready', 'wp-element', 'wp-i18n', ),
		  1,
		  true
	  );

	  // script variables
	  $rest_url = get_rest_url( null, 'graph-box/v1/data/' );

	  $data = array ( 'rest_url' => $rest_url );

	  wp_add_inline_script('graph-box', 'var graphBoxData = '. wp_json_encode( $data ) );

	  wp_localize_script('graph-box', 'wpApiSettings', array(
		  'root' => esc_url_raw(rest_url()),
		  'nonce' => wp_create_nonce( 'wp_rest' )
	  ) );

			?>
			<div id="graph-box-wrapper">

			</div>
			<?php
		}


	 /**
   * Runs a single instance of Graph_Box
	  * @return Graph_Box
	  */
		public static function graph_box_run() {
			if ( !isset( self::$instance ) ) {
				self::$instance = new Graph_Box();
			}
			return self::$instance;
		}
	}

	Graph_Box::graph_box_run();
}
