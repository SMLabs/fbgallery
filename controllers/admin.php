<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends Admin_Controller {

	private $user_id = "";

	var $facebook;
	var $access_token;	
	/**
	 * The current active section
	 * @access protected
	 * @var string
	 */
	protected $section = 'fbgallery';
	
	/**
	 * Constructor method
	 *
	 * @return void
	 */
	function __construct()
	{
	
		// Call the parent's constructor method
		parent::__construct();
		
		// load the module's config file
		$this->load->config('config');
		
		// load Language file
		$this->lang->load('fbgallery');
		
		// Get the user ID, if it exists
		$user = $this->ion_auth->get_user();
		
		if(!empty($user))
		{
			$this->user_id = $user->id;	
			$this->config->set_item('user_id', $this->user_id );
		}else{
			$this->template->build('admin/access_failed');
		}
		
		//Get settings app settings from DB
		$this->config->set_item('app_id', $this->fbgallery_model->getSettings('app_id') );
		$this->config->set_item('app_secret', $this->fbgallery_model->getSettings('app_secret') );
		
		//Crate Facebook  Object
		$this->facebook = new Facebook(array(
		  'appId'  => $this->config->item('app_id'),
		  'secret' => $this->config->item('app_secret'),
		  'cookie' => true,
		  'xfbml'  => true
		));
		
		// are we ready to Rock?
		if(($this->config->item('app_id') == '' || $this->config->item('app_secret') == '') && $this->uri->segment('3') != 'settings')
			redirect(site_url('admin/'.$this->module.'/settings'));
		
		$this->template
			->append_metadata(css('admin.css', $this->module ));
	}
	
	
	function index()
	{
		$albums = $this->fbgallery_model->get_albums();
		
		if($albums)
		{
			foreach($albums as $index=>$album) $albums[$index]->photos = (array)$this->fbgallery_model->get_album_photos($album->id);
		}
		else
		{
			$albums = array();
		}
		
		$data['albums'] = $albums;
		
		$this->template
			->append_metadata( js('jquery-ui-1.8.18.custom.min.js',$this->module))
			->append_metadata(css('prettyPhoto.css', $this->module))
			->append_metadata(js('jquery.prettyPhoto.js', $this->module))
			->append_metadata(js('init.js', $this->module ))
			->build('admin/main',$data);
	}
	
	function order_photos()
	{
		$order = $this->input->post('order');
		foreach($order as $index=>$id){
			echo $index;
			if(!$this->fbgallery_model->set_photo($id,'index',$index)){
				echo 'Update Failed at index='.$index.', id='.$id;
			};
		}
		
	}
	
	function settings()
	{
		
		if($_SERVER['REQUEST_METHOD']=='POST')
		{
			$this->fbgallery_model->saveSettings('app_id',$_POST['app_id']);
			$this->fbgallery_model->saveSettings('app_secret',$_POST['app_secret']);
			redirect(site_url('admin/' . $this->module));
		}
		
		$data['facebook'] = $this->facebook;
		($this->facebook->getUser()) ? $data['fb_user_profile'] = $this->facebook->api('/me') : null;
		$data['app_id']= $this->fbgallery_model->getSettings('app_id');
		$data['app_secret']= $this->fbgallery_model->getSettings('app_secret');
		
		$this->template->build('admin/settings',$data);
	}	
	
	function import()
	{
		
		// Connect
		if(!$this->facebook->getUser())redirect(site_url('admin/'.$this->module.'/connect'));
		
		$data['facebook'] = $this->facebook;
			
		try 
		{
			if($_SERVER['REQUEST_METHOD']=='POST')
			{
				$album_info = $this->facebook->api($_POST['aid']);
				$photos_info = $this->facebook->api($_POST['aid'].'/photos/?limit=9999');// limit set to 1000 to get all photos				
				$this->fbgallery_model->saveAlbum($album_info, $photos_info['data']);
				redirect($_SERVER['HTTP_REFERER']);
			}
			
			$data['fb_user_profile'] = $this->facebook->api('/me');
			$data['albums'] = $this->facebook->api('me/albums');
			
			$albums = $this->fbgallery_model->get_all_album_ids();
			$data['imported'] = array();
			foreach($albums as $index => $album) $data['imported'][] = $album->id;
			
		}
		catch (FacebookApiException $e)
		{
			$data['exception'] = $e;
			$this->template
				->build('admin/facebookapiexception',$data);
		}
	
		$this->template
			->build('admin/import',$data);
		
	}
	

	function import_fanpage($pageid=0)
	{
	
		// Connect
		if(!$this->facebook->getUser())redirect(site_url('admin/'.$this->module.'/connect'));
		
		$data['facebook'] = $this->facebook;
		
		try 
		{
			if($_SERVER['REQUEST_METHOD']=='POST')
			{
				$album_info = $this->facebook->api($_POST['aid']);
				$photos_info = $this->facebook->api($_POST['aid'].'/photos/?limit=9999');
				$this->fbgallery_model->saveAlbum($album_info, $photos_info['data']);
				redirect($_SERVER['HTTP_REFERER']);
			}
			if($this->facebook->getUser())
			{
				$data['fb_user_profile'] = $this->facebook->api('/me');
				
				if($pageid != 0)
				// Choose an Album from the Fanpage
				{ 
					
					$data['albums'] = $this->facebook->api($pageid.'/albums');			
					$albums = $this->fbgallery_model->get_all_album_ids();
					$data['imported'] = array();
					foreach($albums as $index => $album) $data['imported'][] = $album->id;					
				}
				else
				// Choose a Fanpage
				{
					$data['accounts'] = $this->facebook->api('me/accounts');
					//print_r($data['accounts']['data']);exit;
				}
				
			}
		} 
		catch (FacebookApiException $e)
		{
			$data['exception'] = $e;
			$this->template
				->build('admin/facebookapiexception',$data);
		}
	
		$this->template
			->build('admin/import_fanpage',$data);
	}
	
	function delete($scope,$id)
	{
		if($id != '' && $scope!=''){
			switch($scope){
				case 'album':
					$this->fbgallery_model->delete_album($id);
					break;
				case 'photo':
					$this->fbgallery_model->delete_photo($id,0);
					break;
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
		redirect(site_url($this->module));
	}
	
	// Album Activation/Deactivation
	
	function activate($scope,$id)
	{
		if($id!='' && $scope!='')
		{
			switch($scope){
				case 'album':
					$this->fbgallery_model->activate_album($id,1);
					break;
				case 'photo':
					$this->fbgallery_model->activate_photo($id,1);
					break;
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
		redirect(site_url($this->module));
	}
	
	function deactivate($scope,$id)
	{
		if($id!='' && $scope!='')
		{
			switch($scope){
				case 'album':
					$this->fbgallery_model->activate_album($id,0);
					break;
				case 'photo':
					$this->fbgallery_model->activate_photo($id,0);
					break;
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
		redirect(site_url($this->module));
	}
	
	function connect()
	{
		$this->template->build('admin/connect');
	}

}