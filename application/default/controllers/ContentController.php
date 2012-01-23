<?php 




class ContentController extends Standard_Controller_General
{
    
	/**
	 * Init function
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
    public function init()
    {
        
    }
    
    
    /**
     * Team
     *  
     * @author Uros Pirnat
     * @return void
     */
    public function teamAction()
    {
        $members = TripTeamMember::getAll();
        
        
        
        /**
         * Add dojo modules and global javascript variables
         */
        $this->view->dojo()->requireModule('application.page.Team');
        $this->view->dojo()->javascriptCaptureStart();
            echo 'var baseUrl = "' . $this->view->baseUrl() . '";' . "\n";
            echo 'dojo.addOnLoad(function() { new application.page.Team(); });' . "\n";
        $this->view->dojo()->javascriptCaptureEnd();
        
        
        /**
         * Render
         */
        $this->view->members = $members;
        $this->render('team');
    }
    
    
    /**
     * Companies
     *  
     * @author Uros Pirnat
     * @return void
     */
    public function companiesAction()
    {
        /**
         * Parameters
         */
        $type = $this->getRequest()->getParam('type', 'companies');
        
        switch ($type) {
            
            case 'organisations':
                $type = 2;
                break;
            
            case 'companies':
                $type = 1;
                break;
                
            default:
                $type = null;
                break;
        }
        
        
        $companies = TripCompany::getAll($type);
        
        /**
         * Render
         */
        $this->view->companies = $companies;
        $this->render('companies');     
    }
    
    
    /**
     * Timeline
     *  
     * @author Uros Pirnat
     * @return void
     */
    public function timelineAction()
    {
        $events = TripEvent::getAll();
        
        /**
         * Render
         */
        $this->view->events = $events;
        $this->render('timeline');
    }
    
    
    /**
     * Path
     *  
     * @author Uros Pirnat
     * @return void
     */
    public function pathAction()
    {
        $url = $this->view->url(array(), 'companies');
        
        $this->_redirect($url, array('prependBase' => false));
    }
    
    
    /**
     * Map
     *  
     * @author Uros Pirnat
     * @return void
     */
    public function mapAction()
    {
        
        /**
         * Render
         */
        $this->render('map');
    }
    
    
    
    /**
     * Purpose
     *  
     * @author Uros Pirnat
     * @return void
     */
    public function purposeAction()
    {
        
    }
    
    
    /**
     * Sponsors
     *  
     * @author Uros Pirnat
     * @return void
     */
    public function sponsorsAction()
    {
        /**
         * Parameters
         */
        $type = $this->getRequest()->getParam('type', 'gold');
        $type = Zend_Filter::filterStatic($type, 'Null');
        
        switch ($type) {
            case 'gold':
            case 'silver':
            case 'bronze':
            case 'others':
                break;
            default:
                throw new Exception('Such type does not exist!');
        }    
        
        $tmpSponsors = TripSponsor::getAll();
        
        $sponsors = array();
        if (is_array($tmpSponsors)) {
            foreach ($tmpSponsors as $item) {
                if ($item['type'] == $type) {
                    $sponsors[] = $item;
                }
            }
        }
        

        /**
         * Render
         */
        $this->view->sponsors = $sponsors;
        $this->render('sponsors');

    }
    
    
    
    /**
     * Sponsors - offer
     *  
     * @author Uros Pirnat
     * @return void
     */
    public function offerAction()
    {
        
    }
    
    
    /**
     * Presentation in english
     *  
     * @author Uros Pirnat
     * @return void
     */
    public function aboutUsAction()
    {
        $this->_javascriptEnglishCounter();
        $this->view->showTopMenu = false;
        $this->view->showBottomMenu = false;
    }
    
    
    
}
