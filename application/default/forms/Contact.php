<?php 

class Default_Contact_Form extends Zend_Form
{
	
	/**
	 * Default decorators
	 * 
	 * @array
	 */
	protected $_defaultDecorators = array(
		'ViewHelper',
    	'Errors',
    	array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
    	array('Label', array('tag' => 'td')),
   		array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
	);
	
	
	/**
	 * Button decorstors
	 * 
	 * @var array
	 */
	protected $_buttonDecorators = array(
		'ViewHelper',
    	array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
    	array(array('label' => 'HtmlTag'), array('tag' => 'td', 'placement' => 'prepend')),
    	array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
	);
	
	
	/**
	 * Upload decorators
	 * 
	 * @var array
	 */
	protected $_defaultUploadDecorators = array(
		'File',
		'Errors',
		array('Description', array('escape' => false, 'tag' => 'div')),
		array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
		array('Label', array('tag' => 'td', 'class' => 'label')),
		array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
	);	
	
	
	/**
	 * Email from
	 * 
	 * @var string
	 */
	protected $_emailFrom = 'info@toff.si';
	
	
	/**
	 * Administrator's email
	 * 
	 * @var string
	 */
	protected $_emailAdmin = 'info@toff.si';
	
	
	/**
	 * Administrator's name
	 * 
	 * @var string
	 */
	protected $_nameAdmin = 'Administrator';
	
	
	/**
	 * Sender's name
	 * 
	 * @var string
	 */
	protected $_nameFrom = 'Toff';
	
	
	/**
	 * Charset
	 * 
	 * @var string
	 */
	protected $_charset = 'utf-8';
	
	
	/**
	 * Body of email
	 * 
	 * @var string
	 */
	protected $_bodyTextOfEmailToUser;
	
	
	/**
	 * Body of email
	 * 
	 * @var string
	 */
	protected $_bodyTextOfEmailToAdmin;
	
	
	/**
	 * Body of email
	 * 
	 * @var string
	 */
	protected $_bodyHtmlOfEmailToUser;
	
	
	/**
	 * Body of email
	 * 
	 * @var string
	 */
	protected $_bodyHtmlOfEmailToAdmin;
	
	
	/**
	 * Subject of email
	 * 
	 * @var string
	 */
	protected $_subjectOfEmailToUser;
	
	
	/**
	 * Subject of email
	 * 
	 * @var string
	 */
	protected $_subjectOfEmailToAdmin;
	
	
	/**
	 * Construct form
	 * 
	 * @author Uros Pirnat
	 * @param array $options
	 * @return void
	 */
	public function __construct($options = array())
	{

		$this->_subjectOfEmailToUser = $this->getTranslator()->translate('Confirmation of request');
		$this->_subjectOfEmailToAdmin = $this->getTranslator()->translate('New request on page');
		
		
		$this->setAttrib('enctype', 'multipart/form-data');
		$this->setDecorators(array(
		    'FormElements',
		    array('HtmlTag', array('tag' => 'table')),
		    'Form',
		));
		
		
		/**
		 * Firstname
		 */
		$firstname = new Zend_Form_Element_Text('firstname');
		$firstname->setLabel($this->getTranslator()->translate('Firstname'));
		$firstname->setRequired(true);
		$firstname->addValidator('NotEmpty', true);
		$firstname->addValidator('StringLength', true, array(2, 40));
		$firstname->addFilter('StringTrim');
		$firstname->addFilter('StripTags');
		$firstname->setDecorators($this->_defaultDecorators);
		
		
		/**
		 * Lastname
		 */
		$lastname = new Zend_Form_Element_Text('lastname');
		$lastname->setLabel($this->getTranslator()->translate('Lastname'));
		$lastname->setRequired(true);
		$lastname->addValidator('NotEmpty', true);
		$lastname->addValidator('StringLength', true, array(2, 40));
		$lastname->addFilter('StringTrim');
		$lastname->addFilter('StripTags');
		$lastname->setDecorators($this->_defaultDecorators);

		
		/**
		 * E-mail
		 */
		$email = new Zend_Form_Element_Text('email');
		$email->setLabel($this->getTranslator()->translate('Email address'));
		$email->setRequired(true);
		$email->addValidator('NotEmpty', true);
		$email->addValidator('EmailAddress', true);
		$email->addFilter('StringTrim');
		$email->addFilter('StripTags');
		$email->setDecorators($this->_defaultDecorators);
		
		
		/**
		 * Telephone
		 */
		$telephone = new Zend_Form_Element_Text('telephone');
		$telephone->setLabel($this->getTranslator()->translate('Telephone'));
		$telephone->addValidator('StringLength', true, array(6, 80));
		$telephone->addFilter('StringTrim');
		$telephone->addFilter('StripTags');
		$telephone->setDecorators($this->_defaultDecorators);
		
		
		/**
		 * Address
		 */
		$address = new Zend_Form_Element_Text('address');
		$address->setLabel($this->getTranslator()->translate('Address'));
		$address->addValidator('StringLength', true, array(3, 255));
		$address->addFilter('StringTrim');
		$address->addFilter('StripTags');
		$address->setDecorators($this->_defaultDecorators);
		
		
		/**
		 * Message
		 */
		$options = array(
			'cols' => '50',
			'rows' => '13'
		);
		
		
		$message = new Zend_Form_Element_Textarea('message', $options);
		$message->setLabel($this->getTranslator()->translate('Message'));
		$message->setRequired(true);
		$message->addValidator('NotEmpty', true);
		$message->addFilter('StringTrim');
		$message->addFilter('StripTags');
		$message->setDecorators($this->_defaultDecorators);
		
		
    	/**
    	 * Submit
    	 */
		$defaultDecorators = array(
		    'ViewHelper',
			'Errors',
			array('HtmlTag', array('tag' => 'div', 'class'=>'right'))
		);
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel($this->getTranslator()->translate('Submit contact form'));
		$submit->setDecorators($this->_buttonDecorators);
		
		
		/**
		 * Add elements
		 */
		$this->addElement($firstname);
		$this->addElement($lastname);
		$this->addElement($email);
		$this->addElement($telephone);
		$this->addElement($address);
		$this->addElement($message);
		$this->addElement($submit);
		
	}
	
	
	/**
	 * Save form
	 * 
	 * @author Uros Pirnat
	 * @return boolean
	 */
	public function saveForm()
	{
		$data = $this->getData();		
		$success = Contact::save($data);
		
		return $success;
		
	}
	
	
	/**
	 * Send emails
	 * 
	 * @author Uros Pirnat
	 * @return void
	 */
	public function sendEmails()
	{
		/**
		 * Email to user
		 */
		$firstname = $this->getElement('firstname')->getValue();
		$lastname = $this->getElement('lastname')->getValue();
		$email = $this->getElement('email')->getValue();
		$fullName = $firstname . ' ' . $lastname;
		
		 
		$mailUser = new Zend_Mail($this->_charset);
		$mailUser->setBodyText($this->_bodyTextOfEmailToUser);
		$mailUser->setBodyHtml($this->_bodyHtmlOfEmailToUser);
		$mailUser->setFrom($this->_emailFrom, $this->_nameFrom);
		$mailUser->addTo($email, $fullName);
		$mailUser->setSubject($this->_subjectOfEmailToUser);
		$mailUser->send();
					
					
		/**
		 * Email to administrator
		 */
		$mailAdmin = new Zend_Mail($this->_charset);
		$mailAdmin->setBodyText($this->_bodyTextOfEmailToAdmin);
		$mailAdmin->setBodyHtml($this->_bodyHtmlOfEmailToAdmin);
		$mailAdmin->setFrom($this->_emailFrom, $this->_nameFrom);
		$mailAdmin->addTo($this->_emailAdmin, $this->_nameAdmin);
		$mailAdmin->setSubject($this->_subjectOfEmailToAdmin);
		$mailAdmin->send();	
	}
	
	
	/**
	 * Set body text of email to user
	 * 
	 * @author Uros Pirnat
	 * @param string $text
	 * @return void
	 */
	public function setBodyTextOfEmailToUser($text)
	{
		$this->_bodyTextOfEmailToUser = $text;
		
		return $this;
	}
	
	
	/**
	 * Set body html of email to user
	 * 
	 * @author Uros Pirnat
	 * @param string $text
	 * @return void
	 */
	public function setBodyHtmlOfEmailToUser($html)
	{
		$this->_bodyHtmlOfEmailToUser = $html;
		
		return $this;	
	}
	
	
	/**
	 * Set body text of email to admin
	 * 
	 * @author Uros Pirnat
	 * @param string $text
	 * @return void
	 */
	public function setBodyTextOfEmailToAdmin($text)
	{
		$this->_bodyTextOfEmailToAdmin = $text;
		
		return $this;
	}
	
	
	/**
	 * Set body html of email to admin
	 * 
	 * @author Uros Pirnat
	 * @param string $text
	 * @return void
	 */
	public function setBodyHtmlOfEmailToAdmin($html)
	{
		$this->_bodyHtmlOfEmailToAdmin = $html;
		
		return $this;	
	}
	
	
	/**
	 * Get data
	 * 
	 * @author Uros Pirnat
	 * @return array
	 */
	public function getData()
	{
		$data = array(
			'firstname' => $this->getElement('firstname')->getValue(),
			'lastname' => $this->getElement('lastname')->getValue(),
			'email' => $this->getElement('email')->getValue(),
			'telephone' => $this->getElement('telephone')->getValue(),
			'address' => $this->getElement('address')->getValue(),
			'message' => $this->getElement('message')->getValue()
		);
		
		return $data;
	}
	
	
	/**
	 * Set email from
	 * 
	 * @author Uros Pirnat
	 * @param string $email
	 * @return object
	 */
	public function setEmailFrom($email)
	{
		$this->_emailFrom = $email;
		
		return $this;
	}
	
	
	/**
	 * Set name form
	 * 
	 * @author Uros Pirnat
	 * @param string $name
	 * @return object
	 */
	public function setNameFrom($name)
	{
		$this->_nameFrom = $name;
		
		return $this;
	}
	
	
	/**
	 * Set admin name
	 * 
	 * @author Uros Pirnat
	 * @param string $name
	 * @return object
	 */
	public function setAdminName($name)
	{
		$this->_nameAdmin = $name;
		return $this;
	}
	
	
	/**
	 * Set admin email
	 * 
	 * @author Uros Pirnat
	 * @param string $email
	 * @return object
	 */
	public function setAdminEmail($email)
	{
		$this->_emailAdmin = $email;
		return $this;
	}
	
	
	
	
}