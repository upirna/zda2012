<?php





class Standard_Filter_File_Resize implements Zend_Filter_Interface 
{
	
	
	/**
	 * Dimension
	 */
	const DIMENSION_BIGGER = 'bigger';
	const DIMENSION_SMALLER = 'smaller';
	const DIMESION_WIDTH = 'width';
	const DIMESION_HEIGHT = 'height';
	
	
	/**
	 * Size (px)
	 *
	 * @var int
	 */
	protected $_size;
	
	
	/**
	 * Dimension
	 *
	 * @var bigger|smaller|width|height
	 */
	protected $_dimension;
	
	
	/**
	 * Fixed ratio
	 *
	 * @var boolean
	 */
	protected $_fixedRatio;
	
	
	/**
	 * If smaller do not resize
	 * 
	 * @var boolean
	 */
	protected $_ifSmallerDoNotResize;
	

	protected $_duplicate;
	
	
	
	/**
	 * Constructor
	 *
	 * @param int|array $size
	 * @param string $dimension
	 * @param boolean $fixedRatio
	 * @param boolean|string $new The path to save the file to. If false, existing image will be changed
	 * @return void
	 */
	public function __construct($size = null, $dimension = self::DIMENSION_BIGGER, 
		$fixedRatio = true, $ifSmallerDoNotResize = true, $new = false) {

		/**
		 * Set size
		 */
		if(!is_array($size) && !is_numeric($size)) {
			throw new Exception("You  must declare size!");
		}
		
		
		if(is_array($size) && $fixedRatio == true) {
			throw new Exception("Nonsens! You can not declare width, height and fixed ratio at once!");
		}
		
		
		$this->_size = $size;
		
		
		/**
		 * If smaller do not resize
		 */
		$this->_ifSmallerDoNotResize = $ifSmallerDoNotResize;
		
		
		
		/**
		 * Dimension
		 */
		switch ($dimension) {
			case self::DIMENSION_BIGGER:
			case self::DIMENSION_SMALLER:
			case self::DIMESION_HEIGHT:
			case self::DIMESION_WIDTH:
				$this->_dimension = $dimension;
			break;
			
			default:
				throw new Exception('Incorrect type of dimension!');
			break;
		}
		
		
		/**
		 * Fixed ratio
		 */
		if(!is_bool($fixedRatio)) {
			throw new Exception('Specify boolean value for $fiexedRatio!');
		}
		
		$this->_fixedRatio = $fixedRatio;
		
	}
	
	
    /**
     * Defined by Zend_Filter_Interface
     *
     * Resize image
     *
     * @param  string $value Full path of file to change
     * @return string The given $value
     * @throws Zend_Filter_Exception
     */
    public function filter($value) {
        
    	
    	if(is_null($value)) {
    		return;
    	}
    	
    	/**
    	 * Check if file exists and if it is writable
    	 */
    	if (!file_exists($value)) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception("File '$value' not found");
        }

        if (!is_writable($value)) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception("File '$value' is not writable");
        }

        
        /**
         * Get image info
         */
		$info =	getimagesize($value);
		$originalWidth = $info[0];
		$originalHeight = $info[1];
		$type = $info[2];
        
        
		/**
		 * Create a new image from file
		 */
		switch ($type) {
			
			/**
			 * Create GIF image
			 */
            case 1:   
            	$image = imagecreatefromgif($value);
           	break;
           	
           	
           	/**
           	 * Create JPEG image
           	 */
            case 2:
            	$image = imagecreatefromjpeg($value);
            break;
            
            
            /**
             * Create PNG image
             */
            case 3:
            	$image = imagecreatefrompng($value);
            break;
            
            
            /**
             * Throw an exception
             */
            default:
	            require_once 'Zend/Filter/Exception.php';
	            throw new Zend_Filter_Exception("Can not declare image type!");
            break;
        } 
		
        
        /**
         * If ratio is not fixed
         */
        if(!$this->_fixedRatio) {
        	
        	if(is_array($this->_size)) {
        		$newWidth = $this->_size[0];
        		$newHeight = $this->_size[1];
        	}
        	
        	if(is_numeric($this->_size)) {
        		$newHeight = $this->_size;
        		$newWidth  = $this->_size;
        	}
        }
        
        
        /**
         * Calculate new width/height if ratio is fixed
         */
        if($this->_fixedRatio) {
        	
        		/**
				 * Set new height and new width
				 * Code for this functionality could be shorter, but such code 
				 * is more surveyable
				 */
				switch ($this->_dimension) {
					
					/**
					 * Assign size to bigger dimension of image
					 */
					case self::DIMENSION_BIGGER:

						if($originalHeight > $originalWidth) {
							$originalRatio = $originalWidth/$originalHeight;
							$newHeight = $this->_size;
							$newWidth = $newHeight * $originalRatio; 
						} else {
							$originalRatio = $originalHeight / $originalWidth;
							$newWidth = $this->_size;
							$newHeight = $newWidth * $originalRatio;
						}
						
					break;
						
					
					/**
					 * Assign size to smaller dimension of image
					 */
					case self::DIMENSION_SMALLER:
						
						if($originalHeight < $originalWidth) {
							$originalRatio = $originalWidth/$originalHeight;
							$newHeight = $this->_size;
							$newWidth = $newHeight * $originalRatio; 	
						} else {
							$originalRatio = $originalHeight / $originalWidth;
							$newWidth = $this->_size;
							$newHeight = $newWidth * $originalRatio;
						}
						
					break;
					
					
					/**
					 * Assign size to height
					 */
					case self::DIMESION_HEIGHT:
						
						$originalRatio = $originalWidth/$originalHeight;
						$newHeight = $this->_size;
						$newWidth = $newHeight * $originalRatio; 		
						
					break;	
					
					
					/**
					 * Assign size to width
					 */
					case self::DIMESION_WIDTH:
						
						$originalRatio = $originalHeight / $originalWidth;
						$newWidth = $this->_size;
						$newHeight = $newWidth * $originalRatio;	
						
					break;
				}
        }
        
        
        /**
         * If smaller do not resize
         */
        if($this->_ifSmallerDoNotResize) {
        	if($newWidth > $originalWidth) {
        		return;
        	}
        }
        
        
        /**
         * Create a new true color image
         */
		$tmp = imagecreatetruecolor($newWidth, $newHeight);

		
		/**
		 * Copy and resize part of an image with resampling
         * @param Destination image link resource.
         * @param Source image link resource.
         * @param x-coordinate of destination point.
         * @param y-coordinate of destination point.
         * @param x-coordinate of source point. 
         * @param y-coordinate of source point.
         * @param Destination width. 
         * @param Destination height.
         * @param Source width. 
         * @param Source height. 
		 */
		imagecopyresampled($tmp, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
		
		
		/**
		 * Output image to browser or file
		 */
		switch ($type) {
			
			/**
			 * Output GIF image
			 */
            case 1:   
            	imagegif($tmp, $value);
           	break;
           	
           	
           	/**
           	 * Output JPEG image
           	 */
            case 2:
            	imagejpeg($tmp, $value, 100);
            break;
            
            
            /**
             * Output PNG image
             */
            case 3:
            	imagepng($tmp, $value, 100);
            break;

        } 	


        return $value;
    }
    
    

    
}
