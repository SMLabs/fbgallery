<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fbgallery extends Public_Controller
{
	
	public function __construct()
	{
		parent::__construct();
		
		$this->template->append_metadata(css('fbgallery.css', $this->module ));
	}
	
	/**
	 * Index method
	 *
	 * @access public
	 * @return void
	 */
	public function index($aid=0){
		if($aid!=0){
			$data['photos']= array();
			$data['albums']= false;
		}else{
			$data['albums']= $this->fbgallery_model->getAlbums();
		}
		$this->template
		->build('main',$data);
	}
	
	/**
	 * Index method
	 *
	 * @access public
	 * @return void
	 */
	public function photos($aid=0){
		if($aid!=0){
			$data['photos']= $this->fbgallery_model->getPhotosByAlbum($aid);
			$data['album']= $this->fbgallery_model->getAlbums($aid);
		}else{
			redirect(site_url($this->module));
		}
		
		$this->template
			->append_metadata(css('prettyPhoto.css', $this->module ))
			->append_metadata(js('jquery.prettyPhoto.js', $this->module ))
			->append_metadata(js('init.js', $this->module ))
			->build('photos',$data);
		
	}

}