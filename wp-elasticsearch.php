<?php
/*
Plugin Name: WP Elasticsearch
Version: 0.1
Description: PLUGIN DESCRIPTION HERE
Author: YOUR NAME HERE
Author URI: YOUR SITE HERE
Plugin URI: PLUGIN SITE HERE
Text Domain: wp-elasticsearch
Domain Path: /languages
*/

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

    public static function get_instance() {
        if( !isset( self::$instance ) ) {
            $c = __CLASS__;
            self::$instance = new $c();
        }
        return self::$instance;
    }

    public function init() {
        add_filter( 'admin_init', array( $this, 'data_sync' ) );
        add_action( 'pre_get_posts', array( $this, 'search' ) );
    }

	public function search( $query ) {
		if ( $query->is_search() && $query->is_main_query() ) {
			$options = get_option( 'wpels_settings' );
			$client = $this->_create_client( $options );
			if ( !$client ) {
				return;
			}
			$type = $client->getIndex($options['index'])->getType($options['type']);
			$qs = new QueryString();
			$qs->setQuery(get_search_query());
			$query_es = Query::create($qs);
			$resultSet = $type->search($query_es);
		
			$post_ids = array();
			foreach ( $resultSet as $r ) {
				$post_ids[] = $r->getID();
			}
			$query->set('post__in', $post_ids);
			$query->set('s', '');
			$query->set('post_type', 'post');
		}	
	}

	public function data_sync() {
		if ( isset( $_POST['wpElasticsearchDatasync'] ) 
				&& wp_verify_nonce( $_POST['wpElasticsearchDatasync'], 'data_sync' ) ) {
			$this->_data_sync();
		}
	}
	
	private function _data_sync() {
		$options = get_option( 'wpels_settings' );
		$client = $this->_create_client( $options );
		if ( !$client ) {
			return;
		}
		
		$options = get_option( 'wpels_settings' );
		$index = $client->getIndex($options['index']);
		$index->create(array(), true);
		$type = $index->getType($options['type']);
		
		$mapping = array(
						'post_title' => array(
                                          'type' => 'string',
                                          'analyzer' => 'kuromoji',
                                          ),
						'post_content' => array(
                                           'type' => 'string',
                                           'analyzer' => 'kuromoji',
                                          )
                        );
		if ( !empty($options['custom_fields']) ) {
			$custom_fields = explode( "\n", $options['custom_fields'] );
			$custom_fields = array_map( 'trim', $custom_fields );
			$custom_fields = array_filter( $custom_fields, 'strlen' );
			
			foreach ( $custom_fields as $field ) {
				$mapping[$field] = array(
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
            	'post_title' => (string)$p->post_title,
            	'post_content' => (string)strip_tags( $p->post_content )
			);
			if ( !empty($options['custom_fields']) ) {
				foreach ( $custom_fields as $field ) {
					$d[$field] = (string)strip_tags( get_post_meta( $p->ID, $field, true ) );
				}
			}
			$docs[] = $type->createDocument( (int)$p->ID, $d );
		}
		$bulk = new Bulk( $client );
		$bulk->setType( $type );
		$bulk->addDocuments( $docs );
		$bulk->send();
	}
	
	private function _create_client( $options ) {
		
		if ( empty($options['endpoint']) || empty($options['port']) || empty($options['index']) || empty($options['type']) ) {
			return false;
		}

		$client = new \Elastica\Client( array(
											'host' => $options['endpoint'],
											'port' => $options['port']
										));
        return $client;
	}
}
$wpels_instance = WP_Elasticsearch::get_instance();
$wpels_instance->init(); 