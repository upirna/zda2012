<?php 


class Gallery_VideoController extends Standard_Controller_General
{
    /**
     * Gallery
     *  
     * @author Uros Pirnat
     * @return void
     */
    public function indexAction()
    {
        $url = 'http://www.youtube.com/user/feusa2012?blend=1&ob=video-mustangbase';
        
        $this->_redirect($url, array(
            'prependBase' => false
        ));

        $this->render('index');
    }
    
    
}