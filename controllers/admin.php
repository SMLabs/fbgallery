<?php
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
	function __construct(){
	
		// Call the parent's constructor method
		parent::__construct();
		
		// load the module's config file
		$this->load->config('config');
		
		// load Language file
		$this->lang->load('fbgallery');
		
		// Get the user ID, if it exists
		$user = $this->ion_auth->get_user();
		
		if(!empty($user)){
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
	
	
	function index(){
		
		if($this->user_id != ""  ) {
			$data['photos_albums']= $this->fbgallery_model->getAllPhotosAndAlbums();
						
			$this->template
				->append_metadata(css('prettyPhoto.css', $this->module ))
				->append_metadata(js('jquery.prettyPhoto.js', $this->module ))
				->append_metadata(js('init.js', $this->module ))
				->build('admin/main',$data);

		}else {
			$this->template->build('admin/access_failed');
		}
	}
	
	function settings(){
		
		if($this->user_id != ""  ) {
			if($_SERVER['REQUEST_METHOD']=='POST'){
				$this->fbgallery_model->saveSettings('app_id',$_POST['app_id']);
				$this->fbgallery_model->saveSettings('app_secret',$_POST['app_secret']);
				redirect(site_url('admin/' . $this->module));
			}
			
			$data['facebook'] = $this->facebook;
			($this->facebook->getUser()) ? $data['fb_user_profile'] = $this->facebook->api('/me') : null;
			$data['app_id']= $this->fbgallery_model->getSettings('app_id');
			$data['app_secret']= $this->fbgallery_model->getSettings('app_secret');			
			
			$this->template->build('admin/settings',$data);			

		}else {
			$this->template->build('admin/access_failed');
		}
	}	
	
	function import(){
		
		// Connect
		if(!$this->facebook->getUser())redirect(site_url('admin/'.$this->module.'/connect'));
		
		$data['facebook'] = $this->facebook;
			
		try {
			if($_SERVER['REQUEST_METHOD']=='POST'){
				$album_info = $this->facebook->api($_POST['aid']);
				$photos_info = $this->facebook->api($_POST['aid'].'/photos');
				$this->fbgallery_model->saveAlbum($album_info, $photos_info['data']);
				redirect(site_url('admin/' . $this->module));
			}
			
			$data['fb_user_profile'] = $this->facebook->api('/me');
			$data['albums'] = $this->facebook->api('me/albums');

		} catch (FacebookApiException $e) {
			$data['fberror'] = $e;
			$data['fb_user_profile'] = false;
			$data['albums'] = false;
		}
	
		$this->template
			->build('admin/import',$data);
		
	}

	function delete( $action_on,$id ){
		if($action_on == 'album' && $id != '')
			$this->fbgallery_model->deleteAlbum($id);
		
		if($action_on == 'photo' && $id != '')
			$this->fbgallery_model->deletePhoto($id);
			
		redirect(site_url('admin/' . $this->module));
	}
	

	function import_fanpage($pageid=0){
	
		// Connect
		if(!$this->facebook->getUser())redirect(site_url('admin/'.$this->module.'/connect'));
		
		try {
			if($_SERVER['REQUEST_METHOD']=='POST'){
				$album_info = $this->facebook->api($_POST['aid']);
				$photos_info = $this->facebook->api($_POST['aid'].'/photos');
				$this->fbgallery_model->saveAlbum($album_info, $photos_info['data']);
				redirect(site_url('admin/' . $this->module));
			}
		
			$data['facebook'] = $this->facebook;
			if($this->facebook->getUser()){
				$data['fb_user_profile'] = $this->facebook->api('/me');
				if($pageid != 0){ 
					$data['albums'] = $this->facebook->api($pageid.'/albums');					
					
				}else{
					$data['accounts'] = $this->facebook->api('me/accounts');
				}
				
			}
		} catch (FacebookApiException $e) {
			$data['facebook'] = false;
			$data['fb_user_profile'] = false;
			$data['albums'] = false;
		}
	
		$this->template
			->build('admin/import_fanpage',$data);
	}
	
	function connect(){
		$this->template->build('admin/connect');
	}

}