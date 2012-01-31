<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class fbgallery_model extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		$this->load->helper('cookie');
    }

###
	function getAlbums($album_id = NULL){
		
		if($album_id != NULL){
			
			$this->db->where('aid',$album_id);
		}
		return $this->db->get($this->db->dbprefix('fb_albums'))->result_array();
	}
	
	function getphotos($photo_id = NULL){
		
		if($photo_id != NULL){
			
			$this->db->where('pid',$photo_id);
		}
		return $this->db->get($this->db->dbprefix('fb_photos'))->result_array();
	}
	function getOption($option_name= NULL){
		
		if($option_name != NULL){
		
			$this->db->where('option_name',$option_name);
		}
		
		return $this->db->get($this->db->dbprefix('fb_options'))->result();
		
	}
	function setOption($option_name= NULL,$value){
		
		if($option_name != NULL){
			$data=array();
			$this->db->where('option_name',$option_name);
			$data['option_value']=$value;
			 $this->db->update($this->db->dbprefix('fb_options'),$data);
			 
			return true;
		}
		
		return false;
		
		}	
###	
	
	
}