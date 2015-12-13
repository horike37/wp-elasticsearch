<?php

class WP_Elasticsearch_Test extends WP_UnitTestCase {
	private $search_post_id;

	function test_search() {
		remove_filter( 'wpels_search', array( WP_Elasticsearch::get_instance(), 'search' ) );
		add_filter( 'wpels_search', array( $this, 'el_search_mock' ) );

		$this->search_post_id = $this->factory->post->create();
		$this->go_to( '/?s=searchText' );
		$this->assertQueryTrue( 'is_search' );
		while ( have_posts() ) {
			the_post();
			$this->assertEquals( $this->search_post_id,  get_the_ID() );
		}
	}

	function el_search_mock( $search_query ) {
		return array( $this->search_post_id );
	}
}

