<?php if (!defined('BASEPATH')) exit('No direct script access allowed.');


function WsEncrypt( $str )
{
	$CI =& get_instance();
	
	$CI->encrypt->set_cipher(MCRYPT_BLOWFISH);
	$CI->encrypt->set_mode( MCRYPT_MODE_CBC);		
	
	return str_replace( array('+','/','='),array('-','_','~'), $CI->encrypt->encode( $str ) );		
}

function WsDecrypt( $encrypted_str )
{
	$CI =& get_instance();
	
	$CI->encrypt->set_cipher(MCRYPT_BLOWFISH);
	$CI->encrypt->set_mode( MCRYPT_MODE_CBC);		
	
	return $CI->encrypt->decode( str_replace(array('-','_','~'),array('+','/','='),$encrypted_str ) ) ;
}
