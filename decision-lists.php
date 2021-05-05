<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Add this loader.
add_action( 'underpin/before_setup', function ( $file ) {
		require_once( plugin_dir_path( __FILE__ ) . 'lib/abstracts/Decision.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'lib/abstracts/registries/Decision_List.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'lib/loaders/Decision_Lists.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'lib/factories/Decision_Instance.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'lib/factories/Decision_List_Instance.php' );
		Underpin\underpin()->get( $file )->loaders()->add( 'decision_lists', [
			'registry' => 'Underpin_Decision_Lists\Loaders\Decision_Lists',
		] );
}, 3 );