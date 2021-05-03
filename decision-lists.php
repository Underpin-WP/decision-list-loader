<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Add this loader.
add_action( 'underpin/before_setup', function ( $class ) {
	if ( 'Underpin\Underpin' === $class ) {
		require_once( plugin_dir_path( __FILE__ ) . 'lib/abstracts/Decision.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'lib/abstracts/registries/Decision_List.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'lib/loaders/Decision_Lists.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'lib/factories/Decision_Instance.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'lib/factories/Decision_List_Instance.php' );
		Underpin\underpin()->loaders()->add( 'decision_lists', [ 'instance' => 'Underpin_Decision_Lists\Abstracts\Registries\Decision_List' ] );
	}
}, 4 );