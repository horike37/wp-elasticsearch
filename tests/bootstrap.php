<?php
$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}
require_once $_tests_dir . '/includes/functions.php';
function _manually_load_plugin() {
	
	$host = getenv( 'ES_HOST' );
	$host = preg_replace( '/(^https:\/\/|^http:\/\/)/is', '', $host );
	$port = getenv( 'ES_PORT' );
	if ( empty( $host ) ) {
		$host = 'localhost';
	}
	if ( empty( $port ) ) {
		$port = 9200;
	}
	define( 'ES_HOST', $host );
	define( 'ES_PORT', $port );
	
	require dirname( dirname( __FILE__ ) ) . '/wp-elasticsearch.php';
	
	$tries = 5;
	$sleep = 3;
	do {
		$response = wp_remote_get( esc_url(ES_HOST).':'. ES_PORT );
var_dump(esc_url(ES_HOST).':'. ES_PORT);
		if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
			// Looks good!
			break;
		} else {
			printf( "\nInvalid response from ES, sleeping %d seconds and trying again...\n", intval( $sleep ) );
			sleep( $sleep );
		}
	} while ( --$tries );
	if ( 200 != wp_remote_retrieve_response_code( $response ) ) {
		exit( 'Could not connect to Elasticsearch server.' );
	}
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );
require $_tests_dir . '/includes/bootstrap.php';