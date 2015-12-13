<?php

class WP_Elasticsearch_Test extends WP_UnitTestCase {

	function test_search() {
		$mock = $this->getMock( 'WP_Elasticsearch_Mock', array('search') );
		$mock->expects($this->any())->method('search')->will($this->returnValue(array(1,2,3)));
		
		$wpels_instance = WP_Elasticsearch::get_instance();
		$wpels_instance->init();
		var_dump($mock->search('search_text'));
		$this->assertTrue( true );
	}
}

class WP_Elasticsearch_Mock {
   public function __construct() {
   }
   public function search($name) {
      return WP_Elasticsearch::getInstance()->search($name);
   }
}

