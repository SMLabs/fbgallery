<?php if (!defined('BASEPATH')) exit('No direct script access allowed.');


function WsStringFormat( $str, $stater=0, $max=30 )
{
	if(strlen($str) > $max) 
		return substr($str, $stater, $max) . '...'; 
	else 
		return $str; 
}

function CheckHTTP_InURL( $string )
{
	$string = (substr(ltrim($string), 0, 7) != 'http://' ? 'http://' : '') . $string;
	
	return $string;

}