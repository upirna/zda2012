<?php



class ContactController extends Standard_Controller_General 
{

	
	/**
	 * Initialization
	 *
	 * @return void
	 */
	public function init() 
	{
		
		/**
		 * Title
		 */
		$this->view->headTitle($this->view->translate('Contact'), 'SET');
		$this->view->headTitle()->setSeparator(' - ');
		
		
		/**
		 * Add css
		 */
		$baseUrl = $this->getFrontController()->getBaseUrl();
		$url = $baseUrl . '/styles/contact.css';
		$this->view->headLink()->appendStylesheet($url);
		
		
		/**
		 * Add content
		 */
		$content = Content::getByRouteCode('contact');
		
		if(is_array($content)) {
			foreach($content as $c) {
				$this->view->{$c['position']} = $c['content'];
			}
		}
		
		
		
		
	}
	
	
	/**
	 * Redirect
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
	public function indexAction()
	{
		$url = $this->view->url(array(
			'module' => 'default',
			'controller' => 'contact',
			'action' => 'contact'
		), 'default');
		
		$this->_redirect($url, array('prependBase' => false));
	}
	
	
	
    /**
     * Contact action
     * 
     * @author Uros Pirnat
     * @return void
     */
	public function contactAction() 
	{
		
		$form = new Default_Contact_Form();
		
		
		if($_POST) {
			if($form->isValid($_POST)) {
				
				
				/**
				 * Save form and send emails
				 */
				$form->setEmailFrom($this->_config->mail->sendmail->from);
				$form->setNameFrom($this->_config->mail->sendmail->name);
				$form->setAdminEmail($this->_config->mail->admin->mail);
				$form->setAdminName($this->_config->mail->admin->name);
				
				$this->_setDefaultMailTransport();
				
				$form->saveForm();
					
				$data = $form->getData();
				$adminHtml = $this->_mail2adminHtml($data);
				$adminPlain = $this->_mail2adminPlain($data);
				$userHtml = $this->_mail2userHtml($data);
				$userPlain = $this->_mail2userPlain($data);

				$form->setBodyHtmlOfEmailToAdmin($adminHtml);
				$form->setBodyHtmlOfEmailToUser($userHtml);
				$form->setBodyTextOfEmailToAdmin($adminPlain);
				$form->setBodyTextOfEmailToUser($userPlain);
				
				$form->sendEmails();
				
				
				/**
				 * Redirect and reset post
				 */
				$url = $this->view->url(array(
					'module' => 'default',
					'controller' => 'contact',
					'action' => 'success'
				), 'default', true);
				
				$this->_redirect($url, array('prependBase' => false));
					
			}
		}
		
		
		/**
		 * Add CSS
		 */
		$baseUrl = $this->getFrontController()->getBaseUrl();
		$cssLink = $baseUrl . '/styles/contact.css';
		$this->view->headLink()->appendStylesheet($cssLink);	
		
		
		/**
		 * Get content
		 */
		$contents = Content::getByRouteCode('contact');
		
		
		/**
		 * Render
		 */
		$this->view->contents = $contents;
		$this->view->form = $form;
		$this->render('contact');
	}
	
	
	/**
	 * Success
	 * 
	 * @return void
	 */
	public function successAction() {
		$this->render('success');
	}
	
	
	/**
	 * Html email for user
	 *
	 * @author Uros Pirnat
	 * @param array $data
	 * @return string
	 */
	protected function _mail2userHtml($data) 
	{
		$view = new Zend_View();
		$view->addScriptPath(APPLICATION_PATH . '/default/views/scripts/contact/');
		$view->data = $data;
		$xhtml = $view->render('mail2user-html.phtml');
		
		return $xhtml;		
	}
	
	
	/**
	 * Plain mail for user
	 *
	 * @author Uros Pirnat
	 * @param array $data
	 * @return string
	 */
	protected function _mail2userPlain($data) 
	{
		$view = new Zend_View();
		$view->addScriptPath(APPLICATION_PATH . '/default/views/scripts/contact/');
		$view->data = $data;
		$xhtml = $view->render('mail2user-plain.phtml');
		
		return $xhtml;
	}
	
	
	/**
	 * Html email for admin
	 * 
	 * @author Uros Pirnat
	 * @param array $data
	 * @return string
	 */
	protected function _mail2adminHtml($data) 
	{
		$view = new Zend_View();
		$view->addScriptPath(APPLICATION_PATH . '/default/views/scripts/contact/');
		$view->data = $data;
		$xhtml = $view->render('mail2admin-html.phtml');
		
		return $xhtml;
	}
	
	
	/**
	 * Plain email for admin
	 * 
	 * @author Uros Pirnat
	 * @param array $data
	 * @return string
	 */
	protected function _mail2adminPlain($data) 
	{
		$view = new Zend_View();		
		$view->addScriptPath(APPLICATION_PATH . '/default/views/scripts/contact/');
		$view->data = $data;
		$xhtml = $view->render('mail2admin-plain.phtml');
		
		return $xhtml;
		
	}
	
	
}

