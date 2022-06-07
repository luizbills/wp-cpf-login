<?php

namespace CPF_Login;

use CPF_Login\Helpers as h;

final class Authenticator {
	public function __start () {
		add_filter( 'authenticate', [ $this, 'authenticate_by_document' ], 20, 3 );
	}

	public function authenticate_by_document ( $user, $username, $password ) {
		if ( \is_a( $user, 'WP_User' ) ) return $user;

		$document = preg_replace( '/[^0-9]/', '', $username );
		$invalid_document = false;
		$user = null;

		if ( 11 === strlen( $document ) && ! Validator::is_cpf( $document ) ) {
			$invalid_document = true;
		}
		elseif ( 14 === strlen( $document ) && ! Validator::is_cnpj( $document ) ) {
			$invalid_document = true;
		}

		if ( $invalid_document ) {
			return $this->get_invalid_document_error();
		}

		$user = $this->get_user_by_document( $username );

		if ( \is_wp_error( $user ) ) return $user;

		$password_hash = $user->user_pass;
		if ( ! \wp_check_password( $password, $password_hash, $user->ID ) ) {
			return $this->get_invalid_document_error();
		}

		return $user;
	}

	public function get_invalid_document_error () {
		return new \WP_Error(
			'invalid-cpf-cnpj',
			__( '<strong>Erro</strong>: Seu documento ou sua senha estão incorretos.', 'wp-cpf-login' )
		);
	}

	public function get_user_by_document ( $raw_document ) {
		$document_numbers = preg_replace( '/[^0-9]/', '', $raw_document );
		$type = 11 === strlen( $document_numbers ) ? 'cpf' : 'cnpj';
		$meta_key = apply_filters( 'wp_cpf_login_get_meta_key', "billing_$type", $type );

		if ( ! $meta_key ) return false;

		$mask = 'cpf' === $type ? 'XXX.XXX.XXX-XX' : 'XX.XXX.XXX/XXXX-XX';

		$query_args = [
			'meta_query' => [
				[
					'relation' => 'OR',
					[
						'key' => $meta_key,
						'value' => h::str_mask( $document_numbers, $mask ),
					],
					[
						'key' => $meta_key,
						'value' => $document_numbers,
					],
				]
			],
			'number' => 10,
			'fields' => 'ID'
		];
		$query = new \WP_User_Query( $query_args );
		$results = $query->get_results();

		if ( 0 === count( $results ) ) {
			return new \WP_Error(
				'invalid_username',
				sprintf(
					 __( '<strong>Error</strong>: The username <strong>%s</strong> is not registered on this site. If you are unsure of your username, try your email address instead.' ),
					$raw_document
				)
			);
		}

		if ( count( $results ) === 1 ) {
			return \get_user_by( 'id', $results[0] );
		}

		// mais de um usuário registrado com o mesmo CPF/CNPJ
		h::log( "Uma pessoa tentou fazer login usando o documento \"$document_numbers\", mas não conseguiu, pois existem mais de um usuário cadastrado com o mesmo documento. IDs dos usuários: " . implode( ', ', $results ) );

		return new \WP_Error(
			'duplicated-cpf-cnpj',
			__( '<strong>Erro</strong>: Detectamos que existe mais de um usuário cadastrado o seu documento. Por gentileza, use o seu endereço de e-mail ou entre em contato conosco.', 'wp-cpf-login' )
		);
	  }
}
