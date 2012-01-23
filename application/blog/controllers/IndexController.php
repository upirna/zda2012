<?php 



class Blog_IndexController extends Standard_Controller_General
{
    
    /**
     * Index
     *  
     * @author Uros Pirnat
     * @return void
     */
    public function indexAction()
    {
        
        $blogs = Blog::getAll();
        
        
        /**
         * Render
         */
        $this->view->blogs = $blogs;
        $this->render('index');
    }
    
    
    
    
    
    
    
}