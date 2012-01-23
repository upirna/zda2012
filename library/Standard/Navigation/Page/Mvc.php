<?php 


class Standard_Navigation_Page_Mvc extends Zend_Navigation_Page_Mvc
{
	
    /**
     * Returns href for this page
     *
     * This method uses {@link Zend_Controller_Action_Helper_Url} to assemble
     * the href based on the page's properties.
     *
     * @return string  page href
     */
    public function getHref()
    {
        if ($this->_hrefCache) {
            return $this->_hrefCache;
        }

        if (null === self::$_urlHelper) {
            self::$_urlHelper =
                Zend_Controller_Action_HelperBroker::getStaticHelper('Url');
        }

        $tmpParams = $this->getParams();

        $params = array();
        $param = $this->getModule();
        if ($param) {
            $params['module'] = $param;
        }

        $param = $this->getController();
        if ($param) {
            $params['controller'] = $param;
        }

        $param = $this->getAction();
        if ($param) {
            $params['action'] = $param;
        }
/*        
        $params['
		Id'] = $tmpParams['webshopId'];
        $params['languageId'] = $tmpParams['languageId'];
*/      
        
        if(is_array($tmpParams)) {
        	foreach($tmpParams as $key => $param) {
        		$params[$key] = $param;
        	}
        }

		/*
        $url = self::$_urlHelper->url($params,
                                      $this->getRoute(),
                                      $this->getResetParams());
          */
		$url = '/' . $params['module'] . '/' . $params['controller'] . '/' . $params['action'];
		
		foreach($params as $key => $val) {
			if ($key == 'module' || $key == 'controller' || $key == 'action')
				continue;
			$url .= '/' . $key . '/' . $val;
		}

        return $this->_hrefCache = $url;
    }
	
}
