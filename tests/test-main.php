<?php

class MainTest extends WP_UnitTestCase {

	protected $main_class = null;

	function setUp() {
		parent::setUp();
		$this->main_class = Builtin_Taxos_CPT::get_instance();

		$args = array(
			'public'     => true,
			'label'      => 'Books',
			'taxonomies' => array( 'post_tag' )
		);
		register_post_type( 'book', $args );

		$args = array(
			'public' => true,
			'label'  => 'Paper'
		);
		register_post_type( 'paper', $args );

		$args = array(
			'public'     => false,
			'label'      => 'Pen',
			'taxonomies' => array( 'post_tag' )
		);
		register_post_type( 'pen', $args );
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_post_exists() {
		$this->assertContains( 'post', $this->main_class->post_type_support_taxomony( 'post_tag' ) );
	}

	function test_post_doesnt_exists() {
		$this->assertNotContains( 'post', $this->main_class->post_type_support_taxomony( 'wibble' ) );
		$this->assertEmpty( $this->main_class->post_type_support_taxomony( 'wibble' ) );
	}

	function test_book_exists() {
		$this->assertContains( 'book', $this->main_class->post_type_support_taxomony( 'post_tag' ) );
	}

	function test_rock_not_got_tags() {
		$this->assertNotContains( 'rock', $this->main_class->post_type_support_taxomony( 'post_tag' ) );
		$this->assertNotContains( 'rock', $this->main_class->post_type_support_taxomony( 'wibble' ) );
	}

	function test_paper_not_got_tags() {
		$this->assertNotContains( 'paper', $this->main_class->post_type_support_taxomony( 'post_tag' ) );
		$this->assertNotContains( 'paper', $this->main_class->post_type_support_taxomony( 'wibble' ) );
	}

	function test_pen_not_public() {
		$this->assertNotContains( 'pen', $this->main_class->post_type_support_taxomony( 'post_tag' ) );
	}
}

