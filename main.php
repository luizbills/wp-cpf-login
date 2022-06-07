<?php
/*
Plugin Name: WP CPF/CNPJ Login
Version: 1.0.0
Description: Permite os usuários usarem seu CPF/CNPJ para fazer login. Por padrão usará a user meta <code>billing_cpf</code> ou <code>billing_cnpj</code> do plugin Brazilian Market on WooCommerce.
Author: Luiz Bills
Author URI: https://github.com/luizbills
Update URI: false

Text Domain: wp-cpf-login
Domain Path: /languages

License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

defined( 'WPINC' ) || exit();

\load_plugin_textdomain( 'wp-cpf-login', false, \dirname( \plugin_basename( __FILE__ ) ) . '/languages/' );

try {
	$composer_autoload = __DIR__ . '/vendor/autoload.php';
	if ( ! file_exists( $composer_autoload ) ) {
		throw new \Exception ( $composer_autoload . ' does not exist' );
	}
	include_once $composer_autoload;
	\CPF_Login\Core\Main::start_plugin( __FILE__ );
} catch ( \Throwable $e ) {
	\add_action( 'admin_notices', function () use ( $e ) {
		list( $plugin_name ) = \get_file_data( __FILE__, [ 'plugin name' ] );
		$message = \sprintf(
			esc_html__( 'Error on plugin %s activation: %s', 'your_text_domain' ),
			"<strong>$plugin_name</strong>",
			'<br><code>' . \esc_html( $e->getMessage() ) . '</code>'
		);
		echo "<div class='notice notice-error'><p>$message</p></div>";
	} );
}
