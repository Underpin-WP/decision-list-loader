<?php

namespace Underpin_Decision_Lists\Factories;

use Underpin_Decision_Lists\Abstracts\Registries\Decision_List;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Decision_List_Instance extends Decision_List {

	private $items_to_register;

	public function __construct( $args = [] ) {
		$this->items_to_register = $args;
		parent::__construct();
	}

	protected function set_default_items() {
		foreach ( $this->items_to_register as $key => $item ) {
			$this->add( $key, $item );
		}
	}

}