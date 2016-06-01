<?php

class WP_Elasticsearch_Test extends WP_UnitTestCase {
	
	/**
	 * Target post_id
	 *
	 */
	private $search_post_id;

	/**
	 * Search test. 
	 *
	 * @since 0.1
	 */
	function test_search() {
		/*remove_filter( 'wpels_search', array( WP_Elasticsearch::get_instance(), 'search' ) );
		add_filter( 'wpels_search', array( $this, 'el_search_mock' ) );

		$this->search_post_id = $this->factory->post->create();
		$this->go_to( '/?s=searchText' );
		$this->assertQueryTrue( 'is_search' );
		while ( have_posts() ) {
			the_post();
			$this->assertEquals( $this->search_post_id,  get_the_ID() );
		}*/
	}

	
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
		$_POST['wpels_settings']["endpoint"] = ES_HOST;
		$this->assertEquals( true, $this->client->_data_sync() );
	}
}

