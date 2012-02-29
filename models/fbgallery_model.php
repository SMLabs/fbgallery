<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class fbgallery_model extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();

    }
	
	function fb_date_time_to_db_format($time)
	{
		 		
		$time = explode('T',$time);
		$t = $time[0];
		$time = explode('+',$time[1]);
		$t = $t.' '.$time[0];
		return $t;
	}

	function saveAlbum($album_info, $photos_info)
	{
		
		// Get Next Index (will be used for sorting and paging through albums)
		$query = $this->db->select('index')->order_by('index','DESC')->limit(1)->get('fbgallery_albums');		
		$index = ($query->num_rows() > 0 ) ? $query->row()->index+1 : 0;
				
		$this->db->set('id', $album_info['id'] );
		$this->db->set('cover_pid', $album_info['cover_photo'] ); 
		$this->db->set('owner', $album_info['from']['id'] ); 
		if(isset($album_info['name'])){$this->db->set('name', $album_info['name'] ); }
		$this->db->set('link', $album_info['link'] ); 
		$this->db->set('index', $index );
		$this->db->set('created',$this->fb_date_time_to_db_format($album_info['created_time']) ); 
		$this->db->set('modified', $this->fb_date_time_to_db_format($album_info['updated_time']) );
		$this->db->insert('fbgallery_albums'); 

		$index = 0;
				
		foreach($photos_info as $photo){
			$this->db->set('id', $photo['id'] );
			$this->db->set('aid', $album_info['id'] ); 
			$this->db->set('source', $photo['source']); 
			$this->db->set('picture', $photo['picture']); 
			if(isset($photo['name'])){$this->db->set('name', $photo['name'] );} 
			$this->db->set('link', $photo['link'] );
			$this->db->set('index', $index++);
			$this->db->set('created',$this->fb_date_time_to_db_format($photo['created_time'])  );
			$this->db->set('modified',$this->fb_date_time_to_db_format($photo['updated_time'])  ); 
			$this->db->insert('fbgallery_photos'); 
		}
		
		return true;
	}

	function getPhotosByAlbum($aid, $offset, $limit)
	{
		$this->db->select('*');
		$this->db->from('fbgallery_photos');
		$this->db->where('aid',$aid);
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		
		return $query->result();
	}

	function countPhotosByAlbum( $aid )
	{
		$this->db->where('aid',$aid);
		$this->db->from('fbgallery_photos');
		return  $this->db->count_all_results();
	}

	function get_albums()
	{
		$query = $this->db->select('fbgallery_albums.*,fbgallery_photos.picture,fbgallery_photos.source')
			->from('fbgallery_albums')
			->join('fbgallery_photos', 'fbgallery_photos.id = fbgallery_albums.cover_pid','left')
			->get();
			
		if($query->num_rows() > 0)return $query->result();
		return false;
	}
	
	function get_album($id)
	{
		$query = $this->db->select('fbgallery_albums.*,fbgallery_photos.picture,fbgallery_photos.source')
			->from('fbgallery_albums')
			->join('fbgallery_photos', 'fbgallery_photos.id = fbgallery_albums.cover_pid','left')
			->where('fbgallery_albums.id',$id)
			->get();
			
		if($query->num_rows() > 0) return $query->row();
		
		return false;
	}
	
	function get_album_photos($album_id,$active=null,$order_by="index")
	{
		$where = array('aid'=>$album_id);
		if($active!=null)$where['active'] = $active;
		$query = $this->db->get_where('fbgallery_photos',$where);
		
		if($query->num_rows()>0){
			return $query->result();
		}
		
		return false;
	}

	function delete_album( $aid )
	{
		$this->db
			->where('aid', $aid )
			->delete("fbgallery_photos");
		
		$this->db
			->where('id', $aid )
			->delete("fbgallery_albums");		
		
		return true;
	}

	function activate_photo($id,$active=1)
	{	
		$this->db
			->set('active',$active)
			->where('id', $id )
			->update("fbgallery_photos");
		
		return true;
	}

	function activate_album($id,$active=1)
	{	
		$this->db
			->set('active',$active)
			->where('id', $id )
			->update("fbgallery_albums");
		
		return true;
	}


	function getSettings($option_name)
	{
		$this->db->select('option_value');
		$this->db->from('fbgallery_options');
		$this->db->where('option_name', $option_name);
		$query = $this->db->get();
		
		$result = $query->result();	
		if($query->num_rows() > 0){
			return $result[0]->option_value;
		}else{
			return '';
		}
		
	}
	
	function saveSettings($option_name, $option_value)
	{
		$this->db->select('option_name');
		$this->db->from('fbgallery_options');
		$this->db->where('option_name', $option_name);
		$query = $this->db->get();
		
		$this->db->set('option_name', $option_name ); 
		$this->db->set('option_value', $option_value );	
			
		if($query->num_rows() > 0){
			$this->db->where('option_name', $option_name);
			$this->db->update('fbgallery_options'); 
		}else{
			$this->db->insert('fbgallery_options'); 
		}
		
		return true;	
	}
	
	function get_album_by_index($index)
	{
		$query = $this->db->select('*')->where('index',$index)->get('fbgallery_albums');
		return $query->num_rows() > 0 ? $query->row() : false;
	}
	
	function get_all_album_ids()
	{
		$query = $this->db->select('id')->get('fbgallery_albums');
		return $query->num_rows() > 0 ? $query->result() : (object)array();
	}
	
	function set_photo($id,$field,$value)
	{
		if($this->db->where('id',$id)->set($field,$value)->update('fbgallery_photos')) return true;
		return false;
	}
}