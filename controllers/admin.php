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
		}
		//Crate Facebook  Object
		$this->facebook = new Facebook(array(
		  'appId'  => $this->config->item('APP_ID'),
		  'secret' => $this->config->item('SECRET_ID'),
		  'cookie' => true,
		  'xfbml'  => true
		));

		//$user = null;
		//$user = $this->facebook->getUser();		
		
	}
	
	
	function index(){
		
		if($this->user_id != ""  ) {
			$data['photos_albums']= $this->fbgallery_model->getAllPhotosAndAlbums();
			$this->template
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

			$data['app_id']= $this->fbgallery_model->getSettings('app_id');
			$data['app_secret']= $this->fbgallery_model->getSettings('app_secret');			
			
			$this->template
			->build('admin/settings',$data);			

		}else {
			$this->template->build('admin/access_failed');
		}
	}	
	
	function import(){
		
		if($this->user_id != ""  ) {
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
					$data['albums'] = $this->facebook->api('me/albums');
				}
			} catch (FacebookApiException $e) {
				$data['facebook'] = false;
				$data['fb_user_profile'] = false;
				$data['albums'] = false;
			}
		
			$this->template
			->build('admin/import',$data);			

		}else {
			$this->template->build('admin/access_failed');
		}
	}

	function delete( $action_on,$id ){
		if( $this->user_id != "" ) {
			if($action_on == 'album' && $id != ''){
				$this->fbgallery_model->deleteAlbum($id);
			}
			
			if($action_on == 'photo' && $id != ''){

				$this->fbgallery_model->deletePhoto($id);
			}
			redirect(site_url('admin/' . $this->module));
		}else {
			$this->template->build('admin/access_failed');
		}
	}
	

	function import_fanpage($pageid=0){
		if($this->user_id != ""  ) {
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

		}else {
			$this->template->build('admin/access_failed');
		}		
	}

}