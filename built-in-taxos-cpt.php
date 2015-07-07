<?php

/**
 *
 * @link              http://www.jonathandavidharris.co.uk/
 * @since             1.0.0
 * @package           Builtin_Taxos_CPT
 *
 * @wordpress-plugin
 * Plugin Name:       Built-in Taxonomies on CPT
 * Plugin URI:        https://github.com/spacedmonkey/built-in-taxos-cpt/
 * Description:       Add Built-in Taxonomies on Custom Post Types
 * Version:           1.0.0
 * Author:            Jonathan Harris
 * Author URI:        http://www.jonathandavidharris.co.uk/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/spacedmonkey/built-in-taxos-cpt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Builtin_Taxos_CPT
 */
class Builtin_Taxos_CPT {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0
	 * @author   Jonathan Harris <jonathan_harris@ipcmedia.com>
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 *
	 */
	private function __construct() {
		add_action( 'pre_get_posts', array( $this, 'action_pre_get_posts' ), 99 );
	}


	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * @param WP_Query $wp_query
	 *
	 * @return WP_Query
	 */
	public function action_pre_get_posts( WP_Query $wp_query ) {

		if ( is_admin() ) {
			return;
		}

		if ( ! empty( $wp_query->query_vars['suppress_filters'] ) ) {
			return;
		}

		# Further reading:
		# https://core.trac.wordpress.org/ticket/14589
		# https://core.trac.wordpress.org/ticket/19471
		if ( ! $wp_query->is_main_query() ) {
			return;
		}

		if ( $wp_query->is_tag() ) {
			$taxomony = 'post_tag';
		} else if ( $wp_query->is_category() ) {
			$taxomony = 'category';
		} else {
			return;
		}


		$post_type = $wp_query->get( 'post_type' );
		if ( empty( $post_type ) ) {
			$post_type = $this->post_type_support_taxomony( $taxomony );
		}
		$wp_query->set( 'post_type', $post_type );

		return $wp_query;
	}

	/**
	 * @param $taxonomy
	 *
	 * @return array
	 */
	public function post_type_support_taxomony( $taxonomy ) {
		$supported_post_types = array();

		$post_types = get_post_types( array( 'public' => true ) );
		foreach ( $post_types as $post_type ) {
			$taxonomy_names = get_object_taxonomies( $post_type );
			if ( in_array( $taxonomy, $taxonomy_names ) ) {
				$supported_post_types[] = $post_type;
			}
		}

		return $supported_post_types;
	}
}

/*
 * Create the object when plugins loaded
 */
add_action( 'plugins_loaded', array( 'Builtin_Taxos_CPT', 'get_instance' ), 99 );