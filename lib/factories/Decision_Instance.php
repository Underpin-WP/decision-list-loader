<?php
/**
 * Cron Job Factory
 *
 * @since   1.0.0
 * @package Underpin\Abstracts
 */


namespace Underpin_Decision_Lists\Factories;


use Underpin\Traits\Instance_Setter;
use Underpin_Cron_Jobs\Abstracts\Cron_Job;
use Underpin_Decision_Lists\Abstracts\Decision;
use function Underpin\underpin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Admin_Bar_Menu
 * Handles creating custom admin bar menus
 *
 * @since   1.0.0
 * @package Underpin\Abstracts
 */
class Decision_Instance extends Decision {
	use Instance_Setter;

	protected $valid_callback = '';

	protected $valid_actions_callback = '';

	/**
	 * constructor.
	 *
	 * @param array $args Overrides to default args in the Cron_Job object
	 */
	public function __construct( $args = [] ) {
		// Override default params.
		$this->set_values( $args );
	}

	public function is_valid( $params = [] ) {
		return $this->set_callable( $this->valid_callback, $params );
	}

	public function valid_actions( $params = [] ) {
		return $this->set_callable( $this->valid_actions_callback, $params );
	}

}