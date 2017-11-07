<?php

if (!defined('WPINC')) {
	die;
}

/*
function wc_do_deprecated_action( $action, $args, $deprecated_in, $replacement ) {
	if ( has_action( $action ) ) {
		wc_deprecated_function( 'Action: ' . $action, $deprecated_in, $replacement );
		do_action_ref_array( $action, $args );
	}
}

function wc_deprecated_function( $function, $version, $replacement = null ) {
	// @codingStandardsIgnoreStart
	if ( is_ajax() ) {
		do_action( 'deprecated_function_run', $function, $replacement, $version );
		$log_string  = "The {$function} function is deprecated since version {$version}.";
		$log_string .= $replacement ? " Replace with {$replacement}." : '';
		error_log( $log_string );
	} else {
		_deprecated_function( $function, $version, $replacement );
	}
	// @codingStandardsIgnoreEnd
}

function wc_deprecated_argument( $argument, $version, $message = null ) {
	if ( is_ajax() ) {
		do_action( 'deprecated_argument_run', $argument, $message, $version );
		error_log( "The {$argument} argument is deprecated since version {$version}. {$message}" );
	} else {
		_deprecated_argument( $argument, $version, $message );
	}
}

function woocommerce_show_messages() {
	wc_deprecated_function( 'woocommerce_show_messages', '2.1', 'wc_print_notices' );
	wc_print_notices();
}
*/

?>