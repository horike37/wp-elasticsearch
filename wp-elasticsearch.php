<?php
/**
 * Plugin Name: WP Elasticsearch
 * Version: 0.1
 * Description: WordPress search replace Elasticsearch
 * Author: horike,amimotoami
 * Text Domain: wp-elasticsearch
 * Domain Path: /languages
 **/

require_once 'admin/option.php';
require_once 'vendor/autoload.php';

use Elastica\Client;
use Elastica\Query;
use Elastica\Query\QueryString;
use Elastica\Type\Mapping;
use Elastica\Bulk;

class WP_Elasticsearch {
	private static $instance;
	private function __construct() {}

	/**
	 * Return a singleton instance of the current class
	 *
	 * @since 0.1
	 * @return object
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			$c = __CLASS__;
			self::$instance = new $c();
		}
		return self::$instance;
	}

	/**
	 * Initialize.
	 *
	 * @since 0.1
	 */
	public function init() {
		add_filter( 'admin_init',   array( $this, 'data_sync' ) );
		add_filter( 'posts_search', array( $this, 'posts_search' ), 9999, 2);
		add_filter( 'wpels_search', array( $this, 'search' ) );
		add_action( 'save_post',    array( $this, 'save_post' ), 10, 2 );
	}

	
	/**
	 * posts_search filter hook.
	 *
	 * @param $search, $wp_query
	 * @return String
	 * @since 0.1
	 */
	public function posts_search( $search, $wp_query ) {

		global $wpdb;
		$options = get_option( 'wpels_settings' );

		if ( !is_admin() && $wp_query->is_search && $options !== false ) {
			$search_query = get_search_query();
			$post_ids = apply_filters( 'wpels_search', $search_query );
	
			if ( !empty( $post_ids ) && is_array( $post_ids ) ) {
				$search = 'AND '.$wpdb->posts.'.ID IN (';
				$search .= implode(',',$post_ids);
				$search .= ')';
			}
		}
		return $search;
	}

	/**
	 * search query to Elasticsearch.
	 *
	 * @param $search_query
	 * @return true or WP_Error object
	 * @since 0.1
	 */
	public function search( $search_query ) {
		try {
			$options = get_option( 'wpels_settings' );
			$client = $this->_create_client( $options );
			if ( ! $client ) {
				throw new Exception( 'Couldn\'t make Elasticsearch Client. Parameter is not enough.' );
			}

			$type = $client->getIndex( $options['index'] )->getType( $options['type'] );
			$qs = new QueryString();
			$qs->setQuery( $search_query );
			$query_es = Query::create( $qs );
			$resultSet = $type->search( $query_es );

			$post_ids = array();
			foreach ( $resultSet as $r ) {
				$post_ids[] = $r->getID();
			}

			return $post_ids;
		} catch (Exception $e) {
			$err = new WP_Error( 'Elasticsearch Search Error', $e->getMessage() );
			return $err;
		}
	}

	/**
	 * admin_init action. mapping to Elasticsearch
	 *
	 * @since 0.1
	 */
	public function data_sync() {
		if ( isset( $_POST['wp_elasticsearch_datasync'] ) && wp_verify_nonce( $_POST['wp_elasticsearch_datasync'], 'data_sync' ) ) {
			$ret = $this->_data_sync();
			if ( is_wp_error( $ret ) ) {
				$message = array_shift( $ret->get_error_messages( 'Elasticsearch Mapping Error' ) );
				add_settings_error( 'settings_elasticsearch', 'settings_elasticsearch', $message, 'error' );
			} else {
				add_settings_error( 'settings_elasticsearch', 'settings_elasticsearch', 'Success Data Sync to Elasticsearch', 'updated' );
			}
		}
	}
	
	/**
	 * save_post action. Sync Elasticsearch.
	 *
	 * @param $post_id, $post
	 * @since 0.1
	 */
	public function save_post( $post_id, $post ) {
		if ( !empty($_POST) && $post->post_type === 'post' ) {
			$ret = $this->_data_sync();
			if ( is_wp_error( $ret ) ) {
				$message = array_shift( $ret->get_error_messages( 'Elasticsearch Mapping Error' ) );
				wp_die($message);
			}
		}
	}

	/**
	 * admin_init action. mapping to Elasticsearch
	 *
	 * @return true or WP_Error object
	 * @since 0.1
	 */
	public function _data_sync() {
		try {
			$options = get_option( 'wpels_settings' );
			$client = $this->_create_client( $options );
			if ( ! $client ) {
				throw new Exception( 'Couldn\'t make Elasticsearch Client. Parameter is not enough.' );
			}

			$options = get_option( 'wpels_settings' );
			$index = $client->getIndex( $options['index'] );
			$index->create( array(), true );
			$type = $index->getType( $options['type'] );

			$mapping = array(
							'post_title' => array(
												'type' => 'string',
												'analyzer' => 'kuromoji',
											),
							'post_content' => array(
												'type' => 'string',
												'analyzer' => 'kuromoji',
											),
						);
			if ( ! empty( $options['custom_fields'] ) ) {
				$custom_fields = explode( "\n", $options['custom_fields'] );
				$custom_fields = array_map( 'trim', $custom_fields );
				$custom_fields = array_filter( $custom_fields, 'strlen' );

				foreach ( $custom_fields as $field ) {
					$mapping[ $field ] = array(
										'type' => 'string',
										'analyzer' => 'kuromoji',
										);
				}
			}

			$type->setMapping( $mapping );
			$my_posts = get_posts( array( 'posts_per_page' => -1 ) );
			$docs = array();
			foreach ( $my_posts as $p ) {
				$d = array(
					'post_title' => (string) $p->post_title,
					'post_content' => (string) strip_tags( $p->post_content ),
				);
				if ( ! empty( $options['custom_fields'] ) ) {
					foreach ( $custom_fields as $field ) {
						$d[ $field ] = (string) strip_tags( get_post_meta( $p->ID, $field, true ) );
					}
				}
				$docs[] = $type->createDocument( (int) $p->ID, $d );
			}
			$bulk = new Bulk( $client );
			$bulk->setType( $type );
			$bulk->addDocuments( $docs );
			$bulk->send();

			return true;
		} catch (Exception $e) {
			$err = new WP_Error( 'Elasticsearch Mapping Error', $e->getMessage() );
			return $err;
		}
	}

	/**
	 * Create connection to Elasticsearch
	 *
	 * @param $options
	 * @return Client client object
	 * @since 0.1
	 */
	private function _create_client( $options ) {
		if ( empty( $options['endpoint'] ) || empty( $options['port'] ) || empty( $options['index'] ) || empty( $options['type'] ) ) {
			return false;
		}

		$es_options = array(
			'host' => $options['endpoint'],
			'port' => $options['port'],
		);
		if ( isset($options['aws_auth']) && $options['aws_auth'] === 'true' ) {
			$es_options['persistent'] = false;
			$es_options['transport'] = 'AwsAuthV4';

			if ( !empty( $options['access_key'] ) && !empty( $options['secret_key'] ) ) {
				$es_options['aws_access_key_id'] = $options['access_key'];
				$es_options['aws_secret_access_key'] = $options['secret_key'];
			}
			if ( !empty( $options['region'] ) ) {
				$es_options['aws_region'] = $options['region'];
			}
		}
		$client = new \Elastica\Client( $es_options );
		return $client;
	}
}
WP_Elasticsearch::get_instance()->init();
