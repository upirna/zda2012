<?php

/**
 * File
 * 
 * Serve file from requested path. It has also ability to
 * resize image on the fly.
 * 
 * 
 * @package    Default
 * @copyright  Copyright (c) 2005-2010 Kibuba d.o.o.
 * @version    $Id: FileController.php 570 2011-03-01 07:16:01Z uros.pirnat $
 */



class FileController extends Standard_Controller_Simple
{
	
	
	/**
	 * File
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
	public function fileAction() 
	{
		/**
		 * Parameters
		 */
		$newWidth = $this->getRequest()->getParam('width');
		$newWidth = Zend_Filter::filterStatic($newWidth, 'Digits');
		$newWidth = Zend_Filter::filterStatic($newWidth, 'Null');
		
        $opacity = $this->getRequest()->getParam('opacity');
		$opacity = Zend_Filter::filterStatic($opacity, 'Digits');
		$opacity = Zend_Filter::filterStatic($opacity, 'Null');
        
        $maxHeight = $this->getRequest()->getParam('maxHeight');
		$maxHeight = Zend_Filter::filterStatic($maxHeight, 'Digits');
		$maxHeight = Zend_Filter::filterStatic($maxHeight, 'Null');
		
		$pathCode = $this->getRequest()->getParam('path');
		$pathCode = Zend_Filter::filterStatic($pathCode, 'StringTrim');
		
		$fileName = $this->getRequest()->getParam('file');
		$fileName = Zend_Filter::filterStatic($fileName, 'StringTrim');
		
		$transform = $this->getRequest()->getParam('transform');
		
		
		
		$params = array(
			'newWidth' => $newWidth,
			'opacity' => $opacity,
			'maxHeight' => $maxHeight,
			'pathCode' => $pathCode,
			'fileName' => $fileName
		);
		
		
		$result = self::getFile($fileName, $pathCode, $params);
		
		if($transform) {
			$tmp = ROOT_PATH . '/public/tmp/' . $transform . '_' . $fileName;
			file_put_contents($tmp, $result['content']);
		}
		
		
		echo $result['content'];
		
	}
	
	
	/**
	 * Get file
	 * 
	 * @author Uros Pirnat
	 * @param unknown_type $fileName
	 * @param unknown_type $pathCode
	 * @param unknown_type $params
	 * @throws Exception
	 */
	static public function getFile($fileName, $pathCode, $params = array())
	{
		
		/**
		 * Check parameters
		 */
		if(!$fileName) {
			throw new Exception('Filename is missing!');
		}
		
		
		/**
		 * Parameters
		 */
		$newWidth = null;
		if(array_key_exists('newWidth', $params)) {
			$newWidth = $params['newWidth'];
		}
		
		$maxHeight = null;
		if(array_key_exists('maxHeight', $params)) {
			$maxHeight = $params['maxHeight'];
		}
		
		$opacity = null;
		if(array_key_exists('opacity', $params)) {
			$opacity = $params['opacity'];
		}
		
		$sendHeaders = true;
		if(array_key_exists('sendHeaders', $params)) {
			$sendHeaders = $params['sendHeaders'];
		}
		
		$path = null;
		if(array_key_exists('path', $params)) {
			$path = $params['path'] . DIRECTORY_SEPARATOR;
			$path = self::cleanPath($path);
		}
		
		
		
        /**
         * Path
         */
		if($pathCode) {
        	$path = Path::getPathByCode($pathCode);	
        	
		    if(!$path) {
        		throw new Exception(sprintf('Path with code %s does not exist!', $pathCode));
        	}
        	
		}

        

        
        
        /**
         * Fileinfo
         */
        $config = Zend_Registry::get('config');
        
        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
        	$finfo = new finfo(FILEINFO_MIME, $config->magic->file); 
        } else {
        	$finfo = finfo_open(FILEINFO_MIME_TYPE);
        }
       	
        
        
      	/**
	     * Path to file
	     */
		$fullPath = $path . $fileName;     

		
		if(!file_exists($fullPath)) {
			throw new Exception(sprintf('File (%s) does not exist!', $fullPath));
		}
		
		
		/**
		 * Get mime-type for a specific file
		 */
		if (version_compare(PHP_VERSION, '5.3.0') < 0) {
			$mimeType = $finfo->file($fullPath);	
		} else {
			$mimeType = finfo_file($finfo, $fullPath);
		}
		
		
	
		/**
		 * Edit image
		 */
		switch ($mimeType) {
	
			case 'image/jpg':
			case 'image/jpeg':
			case 'image/jpeg; charset=binary':
			case 'image/gif':
			case 'image/png':
				
				$tmpFile = self::createTmp($fullPath);
				
				if($newWidth or $maxHeight) {
					self::resize($tmpFile, $newWidth, $maxHeight);					
				}

					
				if(!is_null($opacity)) {
					self::makeTransparent($tmpFile, $opacity, 'jpg');
				}
				
				$return = file_get_contents($tmpFile);
				unlink($tmpFile);
					
			break;

				
			default:
				$return = self::fileContent($fullPath);
			break;
		}
			
		
		
		/**
		 * Send headers
		 */
		if($sendHeaders) {
			self::sendHeaders($mimeType, $fileName);
		}
		
		return array(
			'content' => $return,
			'mimeType' => $mimeType
		);
		
	}
	
	
	/**
	 * Create temoprary file in cache directory
	 * 
	 * @author Uros Pirnat
	 * @param string $fullPath
	 * @return string
	 */
	static public function createTmp($fullPath) 
	{

		$filename = basename($fullPath);
		$path = ROOT_PATH . DIRECTORY_SEPARATOR . 'tmp';
		$path .= DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
		$newFile = $path . $filename;
		
		copy($fullPath, $newFile);
		
		return $newFile;
	}
	
	
	/**
	 * Resize JPG
	 * 
	 * @author Uros Pirnat
	 * @param string $file
	 * @return string
	 */
	static public function resize($file, $newWidth, $maxHeight, $fixedRatio = true, $ifSmallerDoNotResize = true) 
	{
		
		/**
		 * Width
		 */
		$options = array(
			'size' => $newWidth,
			'fixedRatio' => $fixedRatio,
			'ifSmallerDoNotResize' => $ifSmallerDoNotResize,
			'dimension' => 'width'
		);
		
		$filter = new Standard_Filter_File_Resize_PrimaryResize($options);
		$filter->filter($file);
		
		
		/**
		 * Max height
		 */
		if($maxHeight) {
			$options = array(
				'size' => $maxHeight,
				'fixedRatio' => $fixedRatio,
				'ifSmallerDoNotResize' => $ifSmallerDoNotResize,
				'dimension' => 'height'
			);
			
			$filter = new Standard_Filter_File_Resize_PrimaryResize($options);
			$filter->filter($file);
		}
		
	}
	
	
	/**
	 * Make transparent
	 * 
	 * @author Uros Pirnat
	 * @param $file
	 * @param $type
	 * @return void
	 */
	static public function makeTransparent($file, $opacity = 50, $type = 'jpg') 
	{
		
		list($width, $height) = getimagesize($file);
		
		switch($type) {
			
			case 'jpg':
			case 'jpeg':
				$originalImage = imagecreatefromjpeg($file);
				break;
		}
		
		
		$newImage = imagecreatetruecolor($width, $height);
		$bg = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
		imagefill($newImage, 0, 0 , $bg);
		
		imagecopymerge($newImage, $originalImage, 0, 0, 0, 0, $width, $height, $opacity);	
		
		
		/**
		 * Output
		 */ 
		imagejpeg($newImage);	
	}
	
	
	/**
	 * File
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
	static public function fileContent($file) {
		$handle = fopen($file, "rb");
		$contents = fread($handle, filesize($file));
		return $contents;
	}	
	
	
	/**
	 * Send headers
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
	static public function sendHeaders($mimeType, $fileName)
	{
		switch ($mimeType) {
	
			case 'image/jpg':
			case 'image/jpeg':
			case 'image/jpeg; charset=binary':
				header("Content-Type: image/jpeg; filename=$fileName");
			break;
				
			case 'image/gif':
			case 'image/gif; charset=binary':
				header("Content-Type: image/gif; filename=$fileName");
			break;
				
			case 'image/png':
				header("Content-Type: image/png; filename=$fileName");
			break;
				
			default:
				header("Content-disposition: attachment; filename=$fileName");
				header("Content-Type: application/force-download");
			break;
		}
		
	}
	
	
	/**
	 * Clean path
	 * 
	 * @author Uros Pirnat
	 * @param string $path
	 * @return string
	 */
	static public function cleanPath($path)
	{
		$replace = DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR;
		$path = str_replace($replace, DIRECTORY_SEPARATOR, $path);
		
		return $path;
		
	}
	
	
	
	
	
}