<?php

namespace CPF_Login;

use CPF_Login\Helpers as h;

final class Validator {
	public static function is_cnpj ( $value ) {
		// extrai somente os números
		$cnpj = \preg_replace( '/[^0-9]/', '', (string) $value );
		// valida o tamanho
		if ( strlen( $cnpj ) !== 14 ) return false;
		// verifica se todos os digitos são iguais
		if ( preg_match( '/(\d)\1{13}/', $cnpj ) ) return false;
		// valida primeiro dígito verificador
		for ( $i = 0, $j = 5, $soma = 0; $i < 12; $i++ ) {
			$soma += $cnpj[ $i ] * $j;
			$j = ( $j === 2 ) ? 9 : $j - 1;
		}
		$resto = $soma % 11;
		if ( intval( $cnpj[12] ) !== ( $resto < 2 ? 0 : 11 - $resto ) ) return false;
		// valida segundo dígito verificador
		for ( $i = 0, $j = 6, $soma = 0; $i < 13; $i++ ) {
			$soma += $cnpj[ $i ] * $j;
			$j = ( $j === 2 ) ? 9 : $j - 1;
		}
		$resto = $soma % 11;
		return intval( $cnpj[13] ) === ( $resto < 2 ? 0 : 11 - $resto );
	}

	public static function is_cpf ( $value ) {
		// extrai somente os números
		$cpf = preg_replace( '/[^0-9]/is', '', (string) $value );
		// valida o tamanho
		if ( strlen( $cpf ) !== 11 ) return false;
		// verifica se todos os digitos são iguais
		if ( preg_match( '/(\d)\1{10}/', $cpf ) ) return false;
		// valida o cpf
		for ( $t = 9; $t < 11; $t++ ) {
			for ( $d = 0, $c = 0; $c < $t; $c++ ) {
				$d += $cpf[ $c ] * ( ( $t + 1) - $c );
			}
			$d = ( ( 10 * $d ) % 11 ) % 10;
			if ( intval( $cpf[ $c ] ) !== $d ) return false;
		}
		return true;
	}
}
