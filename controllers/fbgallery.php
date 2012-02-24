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
			$pagination = $this->links( $this->module . "/photos/" . $aid . "/",$page,$pages);
			
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
	
	function thumb(){
		
		$this->load->helper($this->module.'/thumb');
		
		// *** Set some defaults
		$source = strtolower(getGETValue('src', ''));
		$width = getGETValue('w', 250);
		$height = getGETValue('h', 167);
		$option = getGETValue('type', 'crop');
		$sharpen = getGETValue('sharpen', false);
		$quality = getGETValue('q', 100);
		$actions = getGETValue('actions', '');
		
		/*
		*  Author:     Jarrod Oberto
		*  File name:  Image Tools with BMP support 
		*  Purpose:    Create images on the fly!
		*  Requires:   
		*	Version:    1.0
		*  
		*  Modificatoin history
		*  Date      Initials  Ver	 Description
		*  May 11	  JCO       1.0	 * First build 
		*							
		*
		*/
		
		// *** Define some paths/settings
		define('DOCUMENT_ROOT',	$_SERVER['DOCUMENT_ROOT']);
		define('CURRENT_PATH', dirname(__FILE__));
		define('CACHE_PATH', preg_replace('/controllers/','',CURRENT_PATH) . 'cache');
		define('USE_CACHE', true);
		define('AUTO_CONVERT', true);
		define('ALLOW_ACTIONS', true);	
		
		// *** For security we limit the methods that are allowed to be called
		$allowedActionsArray = array('greyScale', 'blackAndWhite', 'sepia', 'negative',
							         'rotate', 'addWatermark', 'addText', 'addBorder',
							         'addReflection', 'roundCorners', 'addShadow',
									 'addCaptionBox', 'greyScaleDramatic', 'vintage',
									 'greyScaleEnhanced');
		
		
		// *** Get the filename
		$imageNameInfoArray = pathinfo($source);
		$imageName = $imageNameInfoArray['filename'];
		$extension = $imageNameInfoArray['extension'];
		
		// *** Set image path 
		$imagePath = $source;
			
		// *** Make sure an image has been passed in and the image exists
		if ($source != '') {
		
			$actionsArray = splitParams($actions);
			
			// *** get action codes for filenameHash
			if (ALLOW_ACTIONS) {
				$cacheCodes = getCacheCodes($actionsArray);
			}
				
			// *** if using shadow or round corners save as png
			if (AUTO_CONVERT && strstr($cacheCodes,'.10') ||  strstr($cacheCodes,'.9')) {
				$extension = 'png';
			}
			
			// *** Filename hash.
			$filenameHash = md5($imageName . '-' . $width . 'x' . $height . $cacheCodes);
			$filenameHashExt = $filenameHash . '.' . $extension;
		
			// *** check cache
			if (file_exists(CACHE_PATH . '/' .  $filenameHashExt) && USE_CACHE) {
			
				// *** Output image from cache
				outputImage($filenameHash, $extension, CACHE_PATH);	
			
			} else {
				
				// *** Should already exist. If not, attempt to create
				createCacheDir(CACHE_PATH);
		
				// *** Open image
				$this->load->library($this->module.'/imagelib',$source);
		
				// *** Resize
				$this->imagelib -> resizeImage($width, $height, $option, $sharpen);
		
				if (ALLOW_ACTIONS) {
					callActions($actionsArray, $this->imagelib, $allowedActionsArray);
				}
				
				// *** Filename to save cached image
				$saveAs = CACHE_PATH . '/' . $filenameHash . '.' . $extension;
		
				// *** Save to cache
				if (USE_CACHE) {
					$this->imagelib -> saveImage($saveAs, $quality);
				}
					
				// *** Output to browser
				$this->imagelib -> displayImage($extension);
			
			}
		}
	}
	
}