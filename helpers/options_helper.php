<?php if (!defined('BASEPATH')) exit('No direct script access allowed.');

function get_option($option_name){
	
	$CI = &get_instance();
	$result= $CI->fbgallery_model->getOption($option_name);
	
	$result = $result[0];
	//print_r($result);exit;
	if($option_name=='fb_facebook_session'){
	 	return unserialize($result->option_value);
	}
	return $result->option_value;
	
}
function update_option($option_name,$value){
	
	$CI = &get_instance();
	if($option_name=='fb_facebook_session'){
		
		$value=serialize($value);
	}
	return $CI->fbgallery_model->setOption($option_name,$value);
	
	
}