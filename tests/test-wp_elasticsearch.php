<?php

class WP_Elasticsearch_Test extends WP_UnitTestCase {
	
	private $client;

	/**
	 * Setup 
	 *
	 * @since 0.1
	 */
	public function setUp() {
		parent::setUp();
		
		$param = array();
		$param['endpoint'] = ES_HOST;
		$param['port']     = ES_PORT;
		$param['index']    = 'wordpress';
		$param['type']     = 'blog';
		add_option( 'wpels_settings', $param);
		$this->client = WP_Elasticsearch::get_instance();
	}

	/**
	 * Elasticsearch bulk test
	 *
	 * @since 0.1
	 */
	function test_es_bulk() {
		$this->factory->post->create( array( 
										'post_type' => 'post',
										'post_title' => 'panpanpan',
										'post_content' => 'everyday everywhere panpanpan' )
									);
		$this->factory->post->create( array(
										'post_type' => 'post',
										'post_title' => 'panpan',
										'post_content' => 'everyday everywhere panpan' ) 
									);
		$this->factory->post->create( array( 
										'post_type' => 'post',
										'post_title' => 'es test',
										'post_content' => 'everyday everywhere es test' )
									);
		$this->assertEquals( true, $this->client->_data_sync() );
	}
}

