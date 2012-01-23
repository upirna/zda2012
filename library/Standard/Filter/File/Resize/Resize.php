<?php





class Standard_Filter_File_Resize_Resize implements Zend_Filter_Interface 
{
	
	
	/**
	 * Dimension
	 */
	const DIMENSION_BIGGER = 'bigger';
	const DIMENSION_SMALLER = 'smaller';
	const DIMENSION_WIDTH = 'width';
	const DIMENSION_HEIGHT = 'height';

	
	
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
	

	/**
	 * Path, where new file will be saved.
	 * If null, override existing image.
	 * 
	 * @var string|null
	 */
	protected $_filename;
	
	
	
	/**
	 * Constructor
	 *
	 * @param int|array $size
	 * @param string $dimension
	 * @param boolean $fixedRatio
	 * @param boolean|string $new The path to save the file to. If false, existing image will be changed
	 * @return void
	 */
	public function __construct($options = array()) {

		/**
		 * Parameters
		 */
		$this->_size = null;
		if(array_key_exists('size', $options)) {
			$this->_size = $options['size'];
		}
		
		$this->_dimension = self::DIMENSION_BIGGER;
		if(array_key_exists('dimension', $options)) {
			$this->_dimension = $options['dimension'];
		}
		
		$this->_fixedRatio = true;
		if(array_key_exists('fixedRatio', $options)) {
			$this->_fixedRatio = $options['fixedRatio'];
		}
		
		$ifSmallerDoNotResize = true;
		if(array_key_exists('ifSmallerDoNotResize', $options)) {
			$this->_ifSmallerDoNotResize = $options['ifSmallerDoNotResize'];
		}
		
		$this->_filename = null;
		if(array_key_exists('filename', $options)) {
			$this->_filename = $options['filename'];
		}
		
		
		/**
		 * Set size
		 */
		if(!is_array($this->_size) && !is_numeric($this->_size)) {
			throw new Exception("You  must declare size!");
		}
		
		
		if(is_array($this->_size) && $this->_fixedRatio == true) {
			throw new Exception("Nonsens! You can not declare width, height and fixed ratio at once!");
		}
		
		
		/**
		 * Dimension
		 */
		switch ($this->_dimension) {
			case self::DIMENSION_BIGGER:
			case self::DIMENSION_SMALLER:
			case self::DIMENSION_HEIGHT:
			case self::DIMENSION_WIDTH:
				true;
			break;
			
			default:
				throw new Exception('Incorrect type of dimension!');
			break;
		}
		
		
		/**
		 * Fixed ratio
		 */
		if(!is_bool($this->_fixedRatio)) {
			throw new Exception('Specify boolean value for $fiexedRatio!');
		}
		
		
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
            throw new Zend_Filter_Exception("File '$value' not found");
        }

        if (!is_writable($value)) {
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
	            require_once '../Zend/Filter/Exception.php';
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
					case self::DIMENSION_HEIGHT:
						
						
						$originalRatio = $originalWidth / $originalHeight;
						$newHeight = $this->_size;
						$newWidth = $newHeight * $originalRatio; 		
						
					break;	
					
					
					/**
					 * Assign size to width
					 */
					case self::DIMENSION_WIDTH:
						
						
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
        	
        	/**
        	 * Get parameter
        	 */
        	switch ($this->_dimension) {
        		
        		/**
        		 * Wich will be changed?
        		 */
				case self::DIMENSION_BIGGER:

					if($originalHeight > $originalWidth) {
						$parameter = 'height';
					} else {
						$parameter = 'width';
					}
						
				break;

				/**
				 * Wich will be changed?
				 */
				case self::DIMENSION_SMALLER:
						
					if($originalHeight < $originalWidth) {
						$parameter = 'height';
					} else {
						$parameter = 'width';
					}
						
				break;
				
				/**
				 * Heighr
				 */
				case self::DIMENSION_HEIGHT:
					$parameter = 'height';
				break;	
				
				/**
				 * Width
				 */
				case self::DIMENSION_WIDTH:
					$parameter = 'width';
				break;	
        		
        		
        	}
        	
        	if($parameter == 'height') {
        		
        	    if($newHeight > $originalHeight) {
        			return $value;
        		}
        		
        	} else if($parameter == 'width') {
        	    if($newWidth > $originalWidth) {
        			return $value;
        		}
        	}

        }
        
        
        /**
         * Create a new true color image
         */
		$tmp = imagecreatetruecolor($newWidth, $newHeight);
		
		
		/**
		 * Transparency
		 */
		if($type == 3) {
			imagesavealpha($tmp, true);
			imagefill($tmp, 0, 0, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
		}
		
		
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
            	imagepng($tmp, $value, 0);
            break;

        } 	


        return $value;
    }
    
    

    
}
