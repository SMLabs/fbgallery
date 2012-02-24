<?php

## --------------------------------------------------------

function getGETValue($value, $defaultValue = '', $valueRequired=true)
#
#	Author:		Jarrod Oberto
#	Date:		May 11
#	Purpose:	Get the value from a $_GET array 
#	Params in:	(str) $value: the value to get
#				(str) $defaultValue: the default value to return should 
#					the value not exist
#	Params out: The return value
#	Notes:	
#
{
	if (array_key_exists($value, $_GET)) {
		if ($_GET[$value] != '' && $valueRequired) {
			return urldecode($_GET[$value]);
			//return $_GET[$value];
			
		}
	}

	return $defaultValue;
}

## --------------------------------------------------------

function createCacheDir ($path, $permissions=0755) 
#
#	Author:		Jarrod Oberto
#	Date:		May 11
#	Purpose:	Attempt to create the cache dir should it not exist
#	Params in:	(str) $path: the cache path
#				(int) $permissions: the folder permissions to set
#	Params out:
#	Notes:	
#		
{
	if(!file_exists($path)) {
		@mkdir($path, $permissions);
		@chmod($path, $permissions);
	} else {
		if (!is_writable($path)) {
			echo 'Path <strong>' . $path . '</strong> directory is not writable.';
			exit;
		}
	}
}

## --------------------------------------------------------

function splitParams ($actions)
#
#	Author:		Jarrod Oberto
#	Date:		May 11
#	Purpose:	Seperates the actions into a key/pair array with action being 
#				the key and params being the associate
#	Params in:	(str) $actions: the actions as inputted by the user
#	Params out: (array) the seperated actions in an array
#	Notes:	
#
{
	$actionsArray = array();
	
	if (trim($actions) != '') {
		
		$actionsTempArray = explode (';', $actions);
		$actionsTempArray = array_map('trim', $actionsTempArray);
		$i = 0;
		foreach ($actionsTempArray as $userAction) {

			$pos = strpos($userAction, '(');

			if ($pos !== false) {

				$action = substr($userAction, 0, $pos);
				$params = substr($userAction, $pos);

				$params = trim($params, '(');
				$params = trim($params, ')');

				// *** Store in a array. The '-n' is added to make key unique as to
				//   * allow the same action to be called
				$actionsArray[$action . '-' . $i] = trim($params);
				$i++;
			}

		}
	}
	
	return $actionsArray;
}

## --------------------------------------------------------

function callActions ($actionsArray, $imageLibObj, $allowedActionsArray)
#
#	Author:		Jarrod Oberto
#	Date:		May 11
#	Purpose:	Call actions to be applied to image
#	Params in:	(array) $actionsArray: The actions and their parameters
#				(obj) $imageLibObj: The image object
#				(array) $allowedActionsArray: A list of allowed methods to be 
#					called.
#	Params out: n/a
#	Notes:	
#		
{

	if (count($actionsArray) > 0) {

		foreach ($actionsArray as $action => $params) {

			$action = removeActionId($action);
			
			$paramsArray = explode(',', $params);
			$paramsArray = array_map('trim', $paramsArray);
						
			if (in_array($action, $allowedActionsArray) && method_exists($imageLibObj, $action)) {
				
				// *** Call action		
				if ($paramsArray[0] == '') {
					@call_user_func_array(array($imageLibObj, $action), array());
				} else {
					@call_user_func_array(array($imageLibObj, $action), $paramsArray );
				}
			}
		}
	}	
}

## --------------------------------------------------------

function getCacheCodes($actionsArray) 
#
#	Author:		Jarrod Oberto
#	Date:		May 11
#	Purpose:	Uniquely identify a cached action by generating a code specific
#				to a particular action
#	Params in:	(array) $actionsArray: an array of actions and there parameters
#	Params out:	(str) A code defining the specified paramaters and there actions
#	Notes:	
#
{
	
	$code = '';
		
	if (count($actionsArray) > 0) {
		
		foreach ($actionsArray as $action => $params) {

			$action = removeActionId($action);

			switch($action) {

				case 'greyscale':
					$code .= '.1' . $params; 	
					break;
				case 'greyScaleDramatic':
					$code .= '.12' . $params; 	
					break;				
				case 'greyScaleEnhanced':
					$code .= '.13' . $params; 	
					break;				
				case 'blackAndWhite':
					$code .= '.2' . $params; 
					break;
				case 'sepia':
					$code .= '.3' . $params; 
					break;
				case 'negative':
					$code .= '.4' . $params; 
					break;
				case 'rotate':
					$code .= '.5' . $params; 
					break;
				case 'addText':
					$code .= '.6' . $params; 
					break;
				case 'addBorder':
					$code .= '.7' . $params; 
					break;
				case 'addReflection':
					$code .= '.8' . $params; 
					break;
				case 'roundCorners':
					$code .= '.9' . $params; 
					break;
				case 'addShadow':
					$code .= '.10' . $params; 
					break;
				case 'addCaptionBox':
					$code .= '.11' . $params; 
					break;
				case 'vintage':
					$code .= '.14' . $params; 
					break;

				default:
					break;
			}
		}
	}
	
	return $code;		
}

## --------------------------------------------------------

function removeActionId($action)
#	Purpose:	Removes the '-n' at the end of the action
{
	$pos = strrpos($action, '-');
	$action = substr($action, 0, $pos);
	
	return $action;
}

## --------------------------------------------------------

function outputImage($filenameHash, $ext, $cachePath)
#
#	Author:		Jarrod Oberto
#	Date:		May 11
#	Purpose:	Output image to browser
#	Params in:  (str) $filenameHash: the cached filename
#				(str) $ext: the file extension to save the image as
#				(str) $cachePath: the path of the cached directory
#	Params out:
#	Notes:	
#		
{
	// *** Read file to string
	$img = file_get_contents($cachePath . '/' . $filenameHash . '.' . $ext);

	switch($ext)
	{
		case 'jpg':
		case 'jpeg':
			header('Content-type: image/jpeg');

			break;
		case 'gif':
			header('Content-type: image/gif');
	
			break;
		case 'png':
			header('Content-type: image/png');

			break;
		case 'bmp':
			//echo 'bmp file format is not supported.';
			break;

		// ... etc

		default:
			// *** No extension - No save.
			break;
	}
		
		// *** Output 
		header('Content-Length: ' . strlen($img));
		echo $img;
		exit();		
}

## --------------------------------------------------------

function file_exists_remote($path){
    return (@fopen($path,"r")==true);
}

//function file_exists_remote($path){
//		if ((strpos($url, "http")) === false) $url = "http://" . $url;
//		if (is_array(@get_headers($url)))
//			return true;
//		else
//			return false;
//}

?>