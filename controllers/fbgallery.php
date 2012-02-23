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
	public function photos(	$aid = 0, $page=1 )
	{
		
		$data['albums']= $this->fbgallery_model->getAlbums();
		
		if($aid!=0){
			$photos_count = $this->fbgallery_model->countPhotosByAlbum($aid);
			$limit = 5;
			$offset = ($page-1) * $limit;
			$pages = ceil($photos_count/$limit);
			$pagination = $this->links($this->module . "/photos/" . $aid . "/",$page,$pages);
			
			$data['pagination'] = $pagination;
			
			$data['photos']= $this->fbgallery_model->getPhotosByAlbum($aid, $offset, $limit);
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
	
    function links($url, $page=1,$pages)
    {
	  
	  $plinks="";
	  $slinks="";
      
      // If we have more then one pages
      if (($pages) > 1)
      {
        // Assign the 'previous page' link into the array if we are not on the first page
        if ($page != 1) {
          $plinks = ' <a href="'.$url.($page - 1).'" class="normal_btn jaxnav-prev"><span>Prev Step</span></a> ';
        }

        // Assign the 'next page' if we are not on the last page
        if ($page < $pages) {
          $slinks = ' <a href="'.$url.($page + 1).'" class="normal_btn jaxnav-next"><span>Next Step</span></a> ';
        }
        
        // Push the array into a string using any some glue
        return   $slinks . '  ' . $plinks;
      }
      return '';
    }	
	
	
}