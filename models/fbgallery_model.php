<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class fbgallery_model extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();

    }
	
	function fb_date_time_to_db_format($time){
		 		
		$time = explode('T',$time);
		$t = $time[0];
		$time = explode('+',$time[1]);
		$t = $t.' '.$time[0];
		return $t;
	}

	function saveAlbum($album_info, $photos_info){
			
		$this->db->set('id', $album_info['id'] ); 
		$this->db->set('cover_pid', $album_info['cover_photo'] ); 
		$this->db->set('owner', $album_info['from']['id'] ); 
		if(isset($album_info['name'])){$this->db->set('name', $album_info['name'] ); }
		$this->db->set('link', $album_info['link'] ); 
		$this->db->set('created',$this->fb_date_time_to_db_format($album_info['created_time']) ); 
		$this->db->set('modified', $this->fb_date_time_to_db_format($album_info['updated_time']) );
		$this->db->insert('fb_albums'); 

		foreach($photos_info as $photo){
			$this->db->set('id', $photo['id'] ); 
			$this->db->set('aid', $album_info['id'] ); 
			$this->db->set('source', $photo['source']); 
			$this->db->set('picture', $photo['picture']); 
			if(isset($photo['name'])){$this->db->set('name', $photo['name'] );} 
			$this->db->set('link', $photo['link'] ); 
			$this->db->set('created',$this->fb_date_time_to_db_format($photo['created_time'])  ); 
			$this->db->set('modified',$this->fb_date_time_to_db_format($photo['updated_time'])  ); 
			$this->db->insert('fb_photos'); 
		}
		
		return true;		
	}

	function getAlbums($aid = NULL){
		if($aid != NULL){
			$this->db->where('fb_albums.id',$aid);
		}
		
		$this->db->select('fb_albums.*,fb_photos.picture,fb_photos.source');
		$this->db->from('fb_albums');
		$this->db->join('fb_photos', 'fb_photos.id = fb_albums.cover_pid');
		$query = $this->db->get();
		
		return $query->result();
	}

	function getPhotosByAlbum($aid){
		$this->db->select('*');
		$this->db->from('fb_photos');
		$this->db->where('aid',$aid);
		$query = $this->db->get();
		
		return $query->result();
	}

	function getAllPhotosAndAlbums(){

		$this->db->select('*');
		$this->db->from('fb_albums');
		$this->db->where('active',1);
		$query = $this->db->get();
		
		$fb_albums = $query->result();
		$data = array();
		foreach($fb_albums as $album){

			$this->db->select('*');
			$this->db->from('fb_photos');
			$this->db->where('aid', $album->id);
			$query = $this->db->get();
			
			$fb_photos = $query->result();			
			foreach($fb_photos as $photo){
				$data [$album->id]['album_id'] = $album->id;
				$data [$album->id]['album_name'] = $album->name;
				$data [$album->id]['album_link'] = $album->link;
				$data [$album->id][$photo->id]['photo_id'] = $photo->id;
				$data [$album->id][$photo->id]['photo_name'] = $photo->name;
				$data [$album->id][$photo->id]['photo_link'] = $photo->link;
				$data [$album->id][$photo->id]['photo_picture'] = $photo->picture;
				$data [$album->id][$photo->id]['photo_source'] = $photo->source;
			}
		}
		
		return $data;
	}

	function deleteAlbum( $aid )
	{
		$this->db->where('aid', $aid );
		$this->db->delete("fb_photos");

		$this->db->where('id', $aid );
		$this->db->delete("fb_albums");		
		return true;
	}

	function deletePhoto( $pid )
	{
		$this->db->where('id', $pid );
		$this->db->delete("fb_photos");
		
		return true;
	}


	function getSettings($option_name){
		$this->db->select('option_value');
		$this->db->from('fb_options');
		$this->db->where('option_name', $option_name);
		$query = $this->db->get();
		
		$result = $query->result();	
		if($query->num_rows() > 0){
			return $result[0]->option_value;
		}else{
			return '';
		}
		
	}
	
	function saveSettings($option_name, $option_value){
		
		
		$this->db->select('option_value');
		$this->db->from('fb_options');
		$this->db->where('option_name', $option_name);
		$query = $this->db->get();
		$result = $query->result();		

		$this->db->set('option_name', $option_name ); 
		$this->db->set('option_value', $option_value );
		
		if($query->num_rows() > 0){
			$this->db->update('fb_options'); 
		}else{
			$this->db->insert('fb_options'); 
		}
		
		return true;	
	}	
}