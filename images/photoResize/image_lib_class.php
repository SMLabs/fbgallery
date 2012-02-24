<?php
   # ========================================================================#
   #
   #  Author:    Jarrod Oberto
   #  Version:	 1.2
   #  Date:      10-05-11
   #  Purpose:   Provide tools for image manipulation using GD
   #  Param In:  See functions.
   #  Param Out: Produces a resized image
   #  Requires : Requires PHP GD library.
   #  Usage Example:
   #                     include("classes/resize_class.php");
   #                     $resizeObj = new resize('images/cars/large/a.jpg');
   #                     $resizeObj -> resizeImage(150, 100, 0);
   #                     $resizeObj -> saveImage('images/cars/large/b.jpg', 100);
   #
   #				- See end of doc for more examples -
   #
   #  Supported file types include: jpg, png, gif, bmp, psd (read) 
   #
   #
   #
   #	The following functions are taken from phpThumb() [available from
   #    http://phpthumb.sourceforge.net], and are used with written permission 
   #	from James Heinrich.
   #		- GD2BMPstring
   #    	- GetPixelColor 
   #    	- LittleEndian2String
   #
   #	The following functions are from Marc Hibbins and are used with written
   #	permission (are also under the Attribution-ShareAlike 
   #	[http://creativecommons.org/licenses/by-sa/3.0/] license.
   #		-
   #	
   #	PhpPsdReader is used with written permission from Tim de Koning. 
   #	[http://www.kingsquare.nl/phppsdreader]
   #
   #
   #
   #  Modificatoin history
   #  Date      Initials  Ver	Description
   #  10-05-11	J.C.O	  1.0	Code canyon
   #  01-06-11  J.C.O	  1.1   * Added reflections	
   #							* Added Rounded corners
   #							* You can now use PNG interlacing
   #							* Added shadow
   #							* Added caption box
   #							* Added vintage filter
   #							* Added dynamic image resizing (resize on the fly)
   #							* minor bug fixes
   #  05-06-11  J.C.O	  1.1.1 * Fixed undefined variables	
   #  17-06-11  J.C.O	  1.2   * Added image_batch_class.php class
   #							* Minor bug fixes		
   #
   #  Known issues & Limitations:
   # -------------------------------
   #  Not so much an issue, the image is destroyed on the deconstruct rather than
   #  when we have finished with it. The reason for this is that we don't know
   #  when we're finished with it as you can both save the image and display
   #  it directly to the screen (imagedestroy($this->imageResized))
   #
   #  Opening BMP files is slow. A test with 884 bmp files processed in a loop
   #  takes forever - over 5 min. This test inlcuded opening the file, then
   #  getting and displaying its width and height.
   #
   #  $forceStretch:
   # -------------------------------
   #  On by default.
   #  $forceStretch can be disabled by calling method setForceStretch with false
   #  parameter. If disabled, if an images original size is smaller than the size
   #  specified by the user, the original size will be used. This is useful when
   #  dealing with small images.
   # 
   #  If enabled, images smaller than the size specified will be stretched to 
   #  that size.
   #
   #
   #   
   #   	
   #  FEATURES:
   #
   #		* BMP SUPPORT (read & write)
   #		* PSD (photoshop) support (read)
   #		* GIF, PNG
   #		* TRANSPARENCY SUPPORT (png, gif)
   #		* RESIZE IMAGES
   #			- Apply sharpening (jpg) (requires PHP >= 5.1.0)
   #			- Resize modes:
   #				- exact size
   #				- resize by width (auto height)
   #				- resize by height (auto width)
   #				- auto (automatically determine the best of the above modes to use)
   #		* APPLY FILTERS
   #			- Convert to grey scale		
   #			- Convert to black and white		
   #			- Convert to sepia	
   #			- Convert to negative	
   #		* ROTATE IMAGES
   #		* EXTRACT EXIF DATA (requires exif module)
   #		* ADD WATERMARK
   #			- Specify exact x, y placement
   #			- Or, specify using one of the 9 pre-defined placements such as "tl" 
   #				(for top left), "m" (for middle), "br" (for bottom right)
   #				- also specify padding from edge amount (optional).
   #			- Set opacity of watermark (png).
   #		* ADD TEXT (inc. custom font support)
   #		* ADD CAPTION BOX
   #				
   #
   # ========================================================================#


class imageLib
{

    private $fileName;
    private $image;
    protected $imageResized;
    private $widthOriginal;			# Always be the original width
    private $heightOriginal;
    private $width;					# Current width (width after resize)
    private $height;
    private $imageSize;
	private $fileExtension;

	private $debug = true;
	private $errorArray = array();

	private $forceStretch = true;
	
	private $psdReaderPath;

	private $pngInterlace;

## --------------------------------------------------------

    function __construct($fileName)
    # Author:     Jarrod Oberto
    # Purpose:    Constructor
    # Param in:   $fileName: File name and path.
    # Param out:  n/a
    # Reference:
    # Notes:
    #
    {
		if (!$this->testGDInstalled()) { if ($this->debug) { die('The GD Library is not installed.'); }else{ die(); }};

		$this->initialise();
		
        // *** Save the image file name. Only store this incase you want to display it
        $this->fileName = $fileName;
		$this->fileExtension = strtolower(strrchr($fileName, '.'));
		
        // *** Open up the file
        $this->image = $this->openImage($fileName);

		
		// *** Assign here so we don't modify the original
		$this->imageResized = $this->image;		
		
        // *** If file is an image
        if ($this->testIsImage($this->image))
        {
            // *** Get width and height
            $this->width  = imagesx($this->image);
            $this->widthOriginal = imagesx($this->image);
            $this->height = imagesy($this->image);
            $this->heightOriginal = imagesy($this->image);

		  
		    /* 	Added 15-09-08
		     *	Get the filesize using this build in method.
		     *	Stores an array of size
		     *	
		     *	$this->imageSize[1] = width
		     *	$this->imageSize[2] = height
		     *	$this->imageSize[3] = width x height
		     *		     		     
		     */
            $this->imageSize = getimagesize($this->fileName);
			
        } else {
			$this->errorArray[] = 'File is not an image';
		}
    }
	
## --------------------------------------------------------
	
	private function initialise () {
		
		// *** Set if png should be interlaced or not. 
		$this->pngInterlace = false;
	}

	
/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*- 
	Resize	
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/	

	
    public function resizeImage($newWidth, $newHeight, $option = 0, $sharpen = false)
    # Author:     Jarrod Oberto
    # Purpose:    Resizes the image
    # Param in:   $newWidth:
    #             $newHeight:
    #             $option:	   0 / exact = defined size;
    #                          1 / portrait = keep aspect set height;
    #                          2 / landscape = keep aspect set width;
    #                          3 / auto = auto;
	#                          4 / crop= resize and crop;
	#			  $sharpen:	   true: sharpen (jpg only);
	#			   			   false: don't sharpen	
    # Param out:  n/a
    # Reference:
    # Notes:      To clarify the $option input: 
    #               0 = The exact height and width dimensions you set. 
    #               1 = Whatever height is passed in will be the height that
    #                   is set. The width will be calculated and set automatically 
    #                   to a the value that keeps the original aspect ratio. 
    #               2 = The same but based on the width. We try make the image the
	#                  biggest size we can while stil fitting inside the box size
    #               3 = Depending whether the image is landscape or portrait, this
    #                   will automatically determine whether to resize via 
    #                   dimension 1,2 or 0
	#               4 = Will resize and then crop the image for best fit
	#
	#				forceStretch can be applied to options 1,2,3 and 4
    #
    {
		// *** If string, make lowercase
		$option = $this->prepOption($option);

		// *** Make sure the file passed in is valid
		if (!$this->image) { if ($this->debug) { die('file ' . $this->getFileName() .' is missing or invalid'); }else{ die(); }};

		// *** Get optimal width and height - based on $option
		$dimensionsArray = $this->getDimensions($newWidth, $newHeight, $option);

		$optimalWidth  = $dimensionsArray['optimalWidth'];
		$optimalHeight = $dimensionsArray['optimalHeight'];

		// *** Resample - create image canvas of x, y size
		$this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
		imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);


		// *** If '4', then crop too
		if ($option == 4 || $option == 'crop') {

			if (($optimalWidth >= $newWidth && $optimalHeight >= $newHeight)) { 
				$this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight);
			}
		}

    }

## --------------------------------------------------------

    private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight)
    # Author:     Jarrod Oberto
    # Purpose:    Crops the image
    # Param in:   $newWidth:
    #             $newHeight:
    # Param out:  n/a
    # Reference:
    # Notes:	  This crops the image from the center of the image
    #
    {

		// *** Find center - this will be used for the crop
		$cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );
		$cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );

		$crop = $this->imageResized;
		//imagedestroy($this->imageResized);

		// *** Now crop from center to exact requested size
		$this->imageResized = imagecreatetruecolor($newWidth , $newHeight);
		imagecopyresampled($this->imageResized, $crop , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);
		
		// *** Set new width and height to our variables
		$this->width = $newWidth;
		$this->height = $newHeight;
  
    }

## --------------------------------------------------------

	private function getDimensions($newWidth, $newHeight, $option)
    # Author:     Jarrod Oberto
    # Purpose:	  Get new image dimensions based on user specificaions
    # Param in:   $newWidth:
    #             $newHeight:
    # Param out:  Array of new width and height values
    # Reference:
    # Notes:	  If $option = 3 then this function is call recursivly
	#
	#			  To clarify the $option input:
    #               0 = The exact height and width dimensions you set.
    #               1 = Whatever height is passed in will be the height that
    #                   is set. The width will be calculated and set automatically
    #                   to a the value that keeps the original aspect ratio.
    #               2 = The same but based on the width.
    #               3 = Depending whether the image is landscape or portrait, this
    #                   will automatically determine whether to resize via
    #                   dimension 1,2 or 0.
	#               4 = Resize the image as much as possible, then crop the
	#					remainder.
	{

        switch (strval($option))
        {
			case '4':
			case 'crop':
                $dimensionsArray = $this->getOptimalCrop($newWidth, $newHeight);
				$optimalWidth = $dimensionsArray['optimalWidth'];
				$optimalHeight = $dimensionsArray['optimalHeight'];
                break;
        }

		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
	}

## --------------------------------------------------------

    private function getOptimalCrop($newWidth, $newHeight)
	# Author:     Jarrod Oberto
    # Purpose:    Get optimal crop dimensions
    # Param in:   width and height as requested by user (fig 3)
    # Param out:  Array of optimal width and height (fig 2)
    # Reference:
    # Notes:      The optimal width and height return are not the same as the
	#			  same as the width and height passed in. For example:
	#
	#
	#		|-----------------|  	  |------------|       |-------|
	#		|	     	      |   =>  |**|      |**|   =>  |       |
	#		|		          | 	  |**|	    |**|       |       |
    #		|		   		  |       |------------|       |-------|
	#		|-----------------|
	#		     original                optimal             crop
	#              size                   size               size
	#  Fig          1                      2                  3
	#
	#		    300 x 250		        150 x 125          150 x 100
	#
 	#    The optimal size is the smallest size (that is closest to the crop size)
	#    while retaining proportion/ratio.
	#
	#	 The crop size is the optimal size that has been cropped on one axis to
	#	 make the image the exact size specified by the user.
	#
	#								* represent cropped area
	#
	{

		// *** If forcing is off...
		if (!$this->forceStretch) {

			// *** ...check if actual size is less than target size
			if ($this->width < $newWidth && $this->height < $newHeight) {
				return array('optimalWidth' => $this->width, 'optimalHeight' => $this->height);
			}
		}

		$heightRatio = $this->height / $newHeight;
		$widthRatio  = $this->width /  $newWidth;

		if ($heightRatio < $widthRatio) {
			$optimalRatio = $heightRatio;
		} else {
			$optimalRatio = $widthRatio;
		}

		$optimalHeight = $this->height / $optimalRatio;
		$optimalWidth  = $this->width  / $optimalRatio;

		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
	}

## --------------------------------------------------------
	
	private function prepOption($option)
    # Author:     Jarrod Oberto
    # Purpose:    Change the passed in option to lowercase
    # Param in:   (str/int) $option: eg. 'exact', 'crop'. 0, 4
    # Param out:  lowercase string
    # Reference:
    # Notes:      
    # 
	{
		if (is_string($option)) {
			return strtolower($option);
		}
			
		return $option;
	}


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*- 
	Draw border 	
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/	
	
	public function addBorder($thickness = 1, $rgbArray = array(255, 255, 255)) 
    # Author:     Jarrod Oberto
    # Purpose:    Add a border to the image
    # Param in:   
    # Param out:  
    # Reference:
    # Notes:	  This border is added to the INSIDE of the image
    #			
	{ 
		if ($this->imageResized) {
			
			$rgbArray = $this->formatColor($rgbArray);
			$r = $rgbArray['r'];
			$g = $rgbArray['g'];
			$b = $rgbArray['b'];
			
			
			$x1 = 0; 
			$y1 = 0; 
			$x2 = ImageSX($this->imageResized) - 1; 
			$y2 = ImageSY($this->imageResized) - 1; 

			$rgbArray = ImageColorAllocate($this->imageResized, $r, $g, $b); 
			
			
			for($i = 0; $i < $thickness; $i++) { 
				ImageRectangle($this->imageResized, $x1++, $y1++, $x2--, $y2--, $rgbArray); 
			} 
		}
	}	
	
/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*- 
	Shadow	
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/		

	public function addShadow($shadowAngle=45, $blur=15, $bgColor='transparent')
	#
	#	Author:		Jarrod Oberto (Adapted from Pascal Naidon)
	#	Ref:		http://www.les-stooges.org/pascal/webdesign/vignettes/index.php?la=en
	#	Purpose:	Add a drop shadow to your image
	#	Params in:	(int) $angle: the angle of the shadow
	#				(int) $blur: the blur distance
	#				(mixed) $bgColor: the color of the background
	#	Params out:
	#	Notes:	
	#			
	{
		// *** A higher number results in a smoother shadow
		define('STEPS', $blur*2);

		// *** Set the shadow distance
		$shadowDistance = $blur*0.25; 

		// *** Set blur width and height
		$blurWidth = $blurHeight = $blur;
		

		if ($shadowAngle == 0) {
			$distWidth = 0;
			$distHeight = 0;	
		} else {
			$distWidth = $shadowDistance * cos(deg2rad($shadowAngle));
			$distHeight = $shadowDistance * sin(deg2rad($shadowAngle));
		}


		// *** Convert color
		if (strtolower($bgColor) != 'transparent') {
			$rgbArray = $this->formatColor($bgColor);
			$r0 = $rgbArray['r'];
			$g0 = $rgbArray['g'];
			$b0 = $rgbArray['b'];			
		}

	
		$image = $this->imageResized;
		$width = $this->width;
		$height = $this->height;
	

	    $newImage = imagecreatetruecolor($width, $height);
		imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $width, $height);


		// *** RGB
		$rgb = imagecreatetruecolor($width+$blurWidth,$height+$blurHeight);
		$colour = imagecolorallocate($rgb, 0, 0, 0);
		imagefilledrectangle($rgb, 0, 0, $width+$blurWidth, $height+$blurHeight, $colour);
		$colour = imagecolorallocate($rgb, 255, 255, 255);
		//imagefilledrectangle($rgb, $blurWidth*0.5-$distWidth, $blurHeight*0.5-$distHeight, $width+$blurWidth*0.5-$distWidth, $height+$blurWidth*0.5-$distHeight, $colour);
		imagefilledrectangle($rgb, $blurWidth*0.5-$distWidth, $blurHeight*0.5-$distHeight, $width+$blurWidth*0.5-$distWidth, $height+$blurWidth*0.5-$distHeight, $colour);
		//imagecopymerge($rgb, $newImage, 1+$blurWidth*0.5-$distWidth, 1+$blurHeight*0.5-$distHeight, 0,0, $width, $height, 100);
		imagecopymerge($rgb, $newImage, $blurWidth*0.5-$distWidth, $blurHeight*0.5-$distHeight, 0,0, $width+$blurWidth, $height+$blurHeight, 100);

		
		// *** Shadow (alpha)
		$shadow = imagecreatetruecolor($width+$blurWidth,$height+$blurHeight);
	    imagealphablending($shadow, false);
		$colour = imagecolorallocate($shadow, 0, 0, 0);
		imagefilledrectangle($shadow, 0, 0, $width+$blurWidth, $height+$blurHeight, $colour);
 
	
		for($i=0;$i<=STEPS;$i++) {

			$t = ((1.0*$i)/STEPS);
			$intensity = 255*$t*$t;
		 
			$colour = imagecolorallocate($shadow, $intensity, $intensity, $intensity);     
			$points = array(
				$blurWidth*$t,				$blurHeight,     // Point 1 (x, y)
				$blurWidth,					$blurHeight*$t,  // Point 2 (x, y)
				$width,						$blurHeight*$t,  // Point 3 (x, y)
				$width+$blurWidth*(1-$t),	$blurHeight,     // Point 4 (x, y)
				$width+$blurWidth*(1-$t),	$height,		 // Point 5 (x, y)
				$width,						$height+$blurHeight*(1-$t),  // Point 6 (x, y)
				$blurWidth,					$height+$blurHeight*(1-$t),  // Point 7 (x, y)
				$blurWidth*$t,				$height			 // Point 8 (x, y)
			);
			imagepolygon($shadow, $points, 8, $colour);
		}

		for($i=0;$i<=STEPS;$i++) {
			
			$t = ((1.0*$i)/STEPS);
		    $intensity = 255*$t*$t;

			$colour = imagecolorallocate($shadow, $intensity, $intensity, $intensity);
			imagefilledarc($shadow, $blurWidth-1, $blurHeight-1, 2*(1-$t)*$blurWidth, 2*(1-$t)*$blurHeight, 180, 268, $colour, IMG_ARC_PIE);
			imagefilledarc($shadow, $width, $blurHeight-1, 2*(1-$t)*$blurWidth, 2*(1-$t)*$blurHeight, 270, 358, $colour, IMG_ARC_PIE);
			imagefilledarc($shadow, $width, $height, 2*(1-$t)*$blurWidth, 2*(1-$t)*$blurHeight, 0, 90, $colour, IMG_ARC_PIE);
			imagefilledarc($shadow, $blurWidth-1, $height, 2*(1-$t)*$blurWidth, 2*(1-$t)*$blurHeight, 90, 180, $colour, IMG_ARC_PIE);
		}

  
		$colour = imagecolorallocate($shadow, 255, 255, 255);
		imagefilledrectangle($shadow, $blurWidth, $blurHeight, $width, $height, $colour);
		imagefilledrectangle($shadow, $blurWidth*0.5-$distWidth, $blurHeight*0.5-$distHeight, $width+$blurWidth*0.5-1-$distWidth, $height+$blurHeight*0.5-1-$distHeight, $colour);


		// *** The magic
        imagealphablending($rgb, false);
       
        for ($theX=0;$theX<imagesx($rgb);$theX++){
			for ($theY=0;$theY<imagesy($rgb);$theY++){
   
				// *** Get the RGB values for every pixel of the RGB image
				$colArray = imagecolorat($rgb,$theX,$theY);
				$r = ($colArray >> 16) & 0xFF;
				$g = ($colArray >> 8) & 0xFF;
				$b = $colArray & 0xFF;

				// *** Get the alpha value for every pixel of the shadow image
				$colArray = imagecolorat($shadow,$theX,$theY);
				$a = $colArray & 0xFF;
				$a = 127-floor($a/2);
				$t = $a/128.0;

				// *** Create color
				if(strtolower($bgColor) == 'transparent') {
					$myColour = imagecolorallocatealpha($rgb,$r,$g,$b,$a);
				} else {
					$myColour = imagecolorallocate($rgb,$r*(1.0-$t)+$r0*$t,$g*(1.0-$t)+$g0*$t,$b*(1.0-$t)+$b0*$t);
				}

				// *** Add color to new rgb image
				imagesetpixel($rgb, $theX, $theY, $myColour);    
			}
		}
		   
		imagealphablending($rgb, true);
		imagesavealpha($rgb, true);

		$this->imageResized = $rgb;

		imagedestroy($image);
		imagedestroy($newImage);
		imagedestroy($shadow);
	}
	
## --------------------------------------------------------	

    private function openImage($file) 
    # Author:     Jarrod Oberto
    # Purpose:    
    # Param in:   
    # Param out:  n/a
    # Reference: 
    # Notes:
    # 
    {
			
		if ((!file_exists($file) && !$this->file_exists_remote($file))) { if ($this->debug) { die('Image not found.'); }else{ die(); }};

        // *** Get extension
        $extension = strrchr($file, '.');
        $extension = strtolower($extension);

        switch($extension) 
        {
            case '.jpg':
            case '.jpeg':
                $img = @imagecreatefromjpeg($file);
                break;
            case '.gif':
                $img = @imagecreatefromgif($file);
                break;
            case '.png':
                $img = @imagecreatefrompng($file);
                break;        
            case '.bmp':
                $img = @$this->imagecreatefrombmp($file);
                break;
            case '.psd':
                $img = @$this->imagecreatefrompsd($file);
                break;
			
				
            // ... etc

            default:
                $img = false;
                break;
        }

        return $img;
    }
		
## --------------------------------------------------------	
	
    public function saveImage($savePath, $imageQuality="100")
    # Author:     Jarrod Oberto
    # Purpose:    Saves the image
    # Param in:   $savePath: Where to save the image including filename:
    #             $imageQuality: image quality you want the image saved at 0-100
    # Param out:  n/a
    # Reference: 
    # Notes:	  * gif doesn't have a quality parameter
	#			  * jpg has a quality setting 0-100 (100 being the best)
    #			  * png has a quality setting 0-9 (0 being the best)
	#
	#             * bmp files have no native support for bmp files. We use a
	#				third party class to save as bmp.
    {

		if (!is_resource($this->imageResized)) { if ($this->debug) { die('saveImage: This is not a resource.'); }else{ die(); }}	
		
		// *** Get extension
        $extension = strrchr($savePath, '.');
        $extension = strtolower($extension);

		$error = '';

        switch($extension)
        {
            case '.jpg':
            case '.jpeg':
				if (imagetypes() & IMG_JPG) {
					imagejpeg($this->imageResized, $savePath, $imageQuality);
				} else { $error = 'jpg'; }
                break;

            case '.gif':
				if (imagetypes() & IMG_GIF) {
					imagegif($this->imageResized, $savePath);
				} else { $error = 'gif'; }
                break;

            case '.png':
				// *** Scale quality from 0-100 to 0-9
				$scaleQuality = round(($imageQuality/100) * 9);

				// *** Invert qualit setting as 0 is best, not 9
				$invertScaleQuality = 9 - $scaleQuality;

				if ($this->pngInterlace) {
					imageinterlace();
				}
				if (imagetypes() & IMG_PNG) {
					 imagepng($this->imageResized, $savePath, $invertScaleQuality);
				} else { $error = 'png'; }
                break;

            case '.bmp':
				file_put_contents($savePath, $this->GD2BMPstring($this->imageResized));
			    break;

			
            // ... etc

            default:
				// *** No extension - No save.
				$this->errorArray[] = 'This file type (' . $extension . ') is not supported. File not saved.';
                break;
        }

		//imagedestroy($this->imageResized);

		// *** Display error if a file type is not supported.
		if ($error != '') {
			$this->errorArray[] = $error . ' support is NOT enabled. File not saved.';
		}       
    }

## --------------------------------------------------------

	public function displayImage($fileType = 'jpg', $imageQuality="100")
    # Author:     Jarrod Oberto
    # Date:       18-11-09
    # Purpose:    Display images directly to the browser
    # Param in:   The image type you want to display
    # Param out:  
    # Reference:
    # Notes:
    #
	{
		if (!is_resource($this->imageResized)) { if ($this->debug) { die('saveImage: This is not a resource.'); }else{ die(); }}	

        switch($fileType)
        {
            case 'jpg':
            case 'jpeg':
				header('Content-type: image/jpeg');
				imagejpeg($this->imageResized, '', $imageQuality);
                break;

            // ... etc

            default:
				// *** No extension - No save.
                break;
        }
		

		//imagedestroy($this->imageResized);
	}

## --------------------------------------------------------

    public function testGDInstalled()
    # Author:     Jarrod Oberto
    # Purpose:    Test to see if GD is installed
    # Param in:   n/a
    # Param out:  (bool) True is gd extension loaded otherwise false
    # Reference:
    # Notes:      
    # 
    {
        if(extension_loaded('gd') && function_exists('gd_info'))
        {
            $gdInstalled = true;
        }
        else
        {
            $gdInstalled = false;
        }  

        return $gdInstalled;        
    }
	
## --------------------------------------------------------

    public function testIsImage($image)
    # Author:     Jarrod Oberto
    # Purpose:    Test if file is an image
    # Param in:   n/a
    # Param out:  n/a
    # Reference:
    # Notes:      
    # 
    {
        if ($image) 
        {
            $fileIsImage = true;
        } 
        else
        {
            $fileIsImage = false;
        }  

        return $fileIsImage;        
    }

## --------------------------------------------------------

    public function testFunct()
    # Author:     Jarrod Oberto
    # Purpose:    Test Function
    # Param in:   n/a
    # Param out:  n/a
    # Reference:
    # Notes:      
    # 
    {
        echo $this->height;       
    }

## --------------------------------------------------------

    public function setForceStretch($value)
    # Author:     Jarrod Oberto
    # Purpose:
    # Param in:   (bool) $value
    # Param out:  n/a
    # Reference:
    # Notes:
    #
    {
        $this->forceStretch = $value;
    }

## --------------------------------------------------------

    public function setFile($fileName)
    # Author:     Jarrod Oberto
    # Purpose:    
    # Param in:   n/a
    # Param out:  n/a
    # Reference:
    # Notes:      
    # 
    {
        self::__construct($fileName);
    }

## --------------------------------------------------------

	public function getFileName()
    # Author:     Jarrod Oberto
    # Purpose:    
    # Param in:   n/a
    # Param out:  n/a
    # Reference:
    # Notes:      
    # 
    {
    	return $this->fileName;
    }

## --------------------------------------------------------

	public function getHeight()
    {
    	return $this->height;
    }

## --------------------------------------------------------

	public function getWidth()
    {
    	return $this->width;
    }

## --------------------------------------------------------

	public function getOriginalHeight()
    {
    	return $this->heightOriginal;
    }

## --------------------------------------------------------

	public function getOriginalWidth()
    {
    	return $this->widthOriginal;
    }

## --------------------------------------------------------

	public function getErrors()
    # Author:     Jarrod Oberto
    # Purpose:    Returns the error array
    # Param in:   n/a
    # Param out:  Array of errors
    # Reference:
    # Notes:
    #
	{
		return $this->errorArray;
	}

## --------------------------------------------------------
	
	protected function formatColor($value)
    # Author:     Jarrod Oberto
    # Purpose:    Determine color method passed in and return color as RGB
    # Param in:   (mixed) $value: (array) Could be an array of RGB
	#							  (str) Could be hex #ffffff or #fff, fff, ffffff
    # Param out:  
    # Reference:
    # Notes:	  This border is added to the INSIDE of the image
    #		
	{
		$rgbArray = array();
		
		// *** If it's an array it should be R, G, B
		if (is_array($value)) {
			
			if (key($value) == 0 && count($value) == 3) {
				
				$rgbArray['r'] = $value[0];
				$rgbArray['g'] = $value[1];
				$rgbArray['b'] = $value[2];
				
			} else {
				$rgbArray = $value;	
			}
		} else if (strtolower($value) == 'transparent') {
			
			$rgbArray = array(
				'r' => 255,
				'g' => 255,
				'b' => 255,
				'a' => 127
			);
			
		} else {
			
			// *** ...Else it should be hex. Let's make it RGB
			$rgbArray = $this -> hex2dec($value);
		}
		
		return $rgbArray;
	}
	
	## --------------------------------------------------------
	
	function hex2dec($hex) 
	# Purpose:	Convert #hex color to RGB
	{
		$color = str_replace('#', '', $hex);

		if (strlen($color) == 3) {
		  $color = $color . $color;
		}

		$rgb = array(
			'r' => hexdec(substr($color, 0, 2)),
			'g' => hexdec(substr($color, 2, 2)),
			'b' => hexdec(substr($color, 4, 2)),
			'a' => 0
		);
		return $rgb;
	}		

	## --------------------------------------------------------
	
	private function createImageColor ($colorArray) 
	{
		$r = $colorArray['r'];
		$g = $colorArray['g'];
		$b = $colorArray['b'];				
			
		return imagecolorallocate($this->imageResized, $r, $g, $b);		
	}
	
	## --------------------------------------------------------
	
	private function testColorExists($colorArray) 
	{
		$r = $colorArray['r'];
		$g = $colorArray['g'];
		$b = $colorArray['b'];		
		
		if (imagecolorexact($this->imageResized, $r, $g, $b) == -1) {
			return false;
		} else {
			return true;
		}
	}
	
	## --------------------------------------------------------
	
	private function findUnusedGreen()
	# Purpose:	We find a green color suitable to use like green-screen effect.
	#			Therefore, the color must not exist in the image.
	{
		$green = 255;
		
		do {

			$greenChroma = array(0, $green, 0);		
			$colorArray = $this->formatColor($greenChroma);	
			$match = $this->testColorExists($colorArray);
			$green--;
			
		} while ($match == false && $green > 0);
		
		// *** If no match, just bite the bullet and use green value of 255
		if (!$match) {
			$greenChroma = array(0, $green, 0);
		}
		
		return $greenChroma;
	} 
	
	## --------------------------------------------------------
	
	private function findUnusedBlue()
	# Purpose:	We find a green color suitable to use like green-screen effect.
	#			Therefore, the color must not exist in the image.
	{
		$blue = 255;
		
		do {

			$blueChroma = array(0, 0, $blue);		
			$colorArray = $this->formatColor($blueChroma);	
			$match = $this->testColorExists($colorArray);
			$blue--;
			
		} while ($match == false && $blue > 0);
		
		// *** If no match, just bite the bullet and use blue value of 255
		if (!$match) {
			$blueChroma = array(0, 0, $blue);
		}		
		
		return $blueChroma;
	}	

	## --------------------------------------------------------
	
	private function file_exists_remote($url)
	# EXPERIEMENTAL
	{
		if ((strpos($url, "http")) === false) $url = "http://" . $url;
		if (is_array(@get_headers($url)))
			return true;
		else
			return false;
	}	


## --------------------------------------------------------

    public function __destruct() {
		if (is_resource($this->imageResized)) {
			imagedestroy($this->imageResized);
		}
	}	
	
## --------------------------------------------------------
	
}




/*
 *		Example with complete API calls:
 *
 *
 *			===============================
 *			Compulsary
 *			===============================
 *
 *			include("classes/resize_class.php");
 *
 *			// *** Initialise object
 *			$resizeObj = new resize('images/cars/large/a.jpg');
 *
 *			// *** Turn off stretching (optional)
 *			$resizeObj -> setForceStretch(false); 
 *
 *			// *** Resize object
 *			$resizeObj -> resizeImage(150, 100, 0);
 *
 *			===============================
 *			Image options - can run none, one, or all.
 *			===============================
 *
 *			//	*** Add watermark
 *		    $resizeObj -> addWatermark('stamp.png');
 *
 *          // *** Add text
 *			$resizeObj -> addText('testing...');
 *
 *			===============================
 *			Output options - can run one, or the other, or both.
 *			===============================
 *
 *			// *** Save image to disk
 *			$resizeObj -> saveImage('images/cars/large/b.jpg', 100);
 *
 *	        // *** Or output to screen (params in can be jpg, gif, png)
 *			$resizeObj -> displayImage('png');
 *
 *			===============================
 *			Return options - return errors. nice for debuggin.
 *			===============================
 *
 *			// *** Return error array
 *			$errorArray = $resizeObj -> getErrors();
 * 
 *
 *			===============================
 *			Cleanup options - not really neccessary, but good practice
 *			===============================
 *
 *			// *** Free used memory
 *			$resizeObj -> __destruct();
 */

?>