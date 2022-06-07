<?php

namespace CPF_Login;

use CPF_Login\Helpers as h;

defined( 'WPINC' ) || exit();

// register_activation_hook( h::config_get( 'FILE' ), function () {
// 	h::log( 'plugin activated' );
// } );

return [
	[ Authenticator::class, 10 ], // 10 is priority
];
