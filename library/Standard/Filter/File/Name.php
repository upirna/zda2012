<?php





class Standard_Filter_File_Name implements Zend_Filter_Interface 
{
	

	
    /**
     * Defined by Zend_Filter_Interface
     *
     * @param  string $value
     * @return string The given $value
     * @throws Zend_Filter_Exception
     */
    public function filter($value) {
        
    	if(is_null($value)) {
    		return;
    	}
    	
    	
    	/**
    	 * Replace some chars
    	 */
    	$pairs = array(
    		'š' => '',
    		'đ' => '',
    		'č' => '',
    		'ć' => '',
    		'ž' => '',
    		' ' => '_'
    	);
    	
    	if(is_array($pairs)) {
	    	foreach($pairs as $char => $replace) {
	    		$value = str_ireplace($char, $replace, $value);
	    	}
    	}
    	
    	
    	/**
    	 * Remove chars, which are not allowed
    	 */
    	$allowedChars = array(
    		'q', 'w', 'e', 'r', 't', 'z', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l',
    		 'y', 'x', 'c', 'v', 'b', 'n', 'm', '.', '_', '-', '1', '2', '3', '4', '5', '6', '7', '8', '9'
    	);
    	
    	
    	$newName = '';
    	for($i=0; $i < strlen($value); $i++) {
    		if(in_array(strtolower($value[$i]), $allowedChars)) {
    			$newName .= $value[$i];
    		}
    	}
    	

        return $value;
    }
    
    

    
}
