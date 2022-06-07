<?php

use CPF_Login\Helpers as h;

defined( 'WPINC' ) || exit();

return [
	'php' => [
		'message' => function () {
			$req_version = h::config_get( 'REQUIRED_PHP_VERSION', false );
			return sprintf(
				__( "Update your %s version to $req_version or later.", 'wp-cpf-login' ),
				'<strong>PHP</strong>'
			);
		},
		'check' => function () {
			$req_version = h::config_get( 'REQUIRED_PHP_VERSION', false );
			$serv_version = \preg_replace( '/[^0-9\.]/', '', PHP_VERSION );
			return $req_version && $serv_version ? \version_compare( $serv_version, $req_version, '>=' ) : true;
		}
	],

	// 'woocommerce' => [
	// 	'message' => sprintf(
	// 		__( 'Install and activate the %s plugin.', 'wp-cpf-login' ),
	// 		'<strong>WooCommerce</strong>'
	// 	),
	// 	'check' => function () {
	// 		return \function_exists( 'WC' );
	// 	}
	// ],
];
