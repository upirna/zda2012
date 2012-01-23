<?php



class Zend_View_Helper_Tinymce
{
	
	/**
	 * @var Zend_View_Interface 
	 */
	public $view;
	
	
	/**
	 * Script
	 * 
	 * @var string
	 */
	protected $_script;
	
	
	/**
	 * Theme
	 * 
	 * @var string
	 */
	protected $_theme = array(
		'name' => 'advanced',
		'params' => array(
			'theme_advanced_buttons1' => 'bold,italic,underline,undo,redo,link,unlink,image,forecolor,styleselect,removeformat,cleanup,code',
			'theme_advanced_buttons2' => '',
			'theme_advanced_buttons3' => '',
			'theme_advanced_toolbar_location' => 'bottom',
			'theme_advanced_toolbar_align' => 'center',
			'theme_advanced_styles' => 'Code=codeStyle;Quote=quoteStyle'
		)
	);
	
	
	/**
	 * Mode
	 * 
	 * @var string
	 */
	protected $_mode = 'exact';
	
	
	/**
	 * Convert donts to spans
	 * 
	 * @var boolean
	 */
	protected $_convertFontsToSpans = true;
	
	
	/**
	 * Inline styles
	 * 
	 * @var boolean
	 */
	protected $_inlineStyles = false;
	
	
	/**
	 * Remove line breaks
	 * 
	 * @var boolean
	 */
	protected $_removeLineBreaks = false;
	
	
	/**
	 * Add unload trigger
	 * 
	 * @var boolean
	 */
	protected $_addUnloadTrigger = false;
	
	
	/**
	 * Remove styles on paste
	 * 
	 * @var boolean
	 */
	protected $_pasteRemoveStyles = true;
	
	
	/**
	 * Remove spans on paste
	 * 
	 * @var boolean
	 */
	protected $_pasteRemoveSpans = true;
	
	
	/**
	 * Paste use dialog
	 * 
	 * @var boolean
	 */
	protected $_pasteUseDialog = false;
	
	
	/**
	 * Plugins
	 * 
	 * @var array
	 */
	protected $_plugins = array(
		'bbcode',
		'paste'
	);
	
	
	/**
	 * Entity encoding
	 * 
	 * @var string
	 */
	protected $_entityEncoding = 'raw';
	
	
	/**
	 * Content CSS
	 * 
	 * @var array
	 */
	protected $_contentCss = array(
		'css/bbcode.css'
	);
	
	
	/**
	 * Execcommand callback
	 * 
	 * @var string
	 */
	protected $_execcommandCallback;
	
	
	
	/**
	 * Tinymce
	 *
	 * @param string $name
	 * @param string $value
	 * @return string
	 */
	public function tinymce($name, $value = null, $attribs = null)
	{

		/**
		 * Set attributes
		 */
		$this->_setAttrs($attribs);
		
		
		/**
		 * Headscript
		 */
		$baseUrl = $this->view->baseUrl();
		$this->view->headScript()->appendFile($baseUrl . '/scripts/tiny_mce/tiny_mce.js');	
		
		
		
		$this->_script  = 'function tinyMCE' . $name . '() { ';
			$this->_script .= 'tinyMCE.init({';
				$this->_script .= $this->_getThemeCode();
				$this->_script .= 'mode : "' . $this->_mode . '",';
				$this->_script .= 'elements : "' . $name . '",';
				$this->_script .= 'plugins : "' . $this->_getPlugins() . '",';
				$this->_script .= 'content_css : "' . $this->_getContentCss() . '",';
				$this->_script .= 'entity_encoding : "' . $this->_entityEncoding . '",';
				$this->_script .= 'add_unload_trigger : ' . $this->_phpBooleanToJs($this->_addUnloadTrigger) . ',';
				$this->_script .= 'remove_linebreaks : ' . $this->_phpBooleanToJs($this->_removeLineBreaks) . ',';
				$this->_script .= 'paste_remove_styles :' . $this->_phpBooleanToJs($this->_pasteRemoveStyles) . ',';
				$this->_script .= 'paste_remove_spans :' . $this->_phpBooleanToJs($this->_pasteRemoveSpans) . ',';
				$this->_script .= 'paste_use_dialog :' . $this->_phpBooleanToJs($this->_pasteUseDialog) . ',';
				
				
				
				
				$this->_script .= '
				setup : function(ed) {
					ed.addCommand("mceLink", function(ui, v) {
			 			
						var link = tinyMCE.activeEditor.dom.getParent(tinyMCE.activeEditor.selection.getNode(), \'a\');
      
						tinyMCE.activeEditor.windowManager.open({
							file : baseUrl + "/promo/link/select/",
							title : "File Manager",
							width : 400,  // Your dimensions may differ - toy around with them!
							height : 200,
							scrollbars: true,
							resizable : "no",
							inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
							close_previous : "no"
						}, {
							input: link
						}); 
					
					
					
						tinyMCE.activeEditor.windowManager.onClose = function(event) {
							console.log(1234);
						};
					
      					return false;
      
					});
  				}, 
  				';
				
				
				
				if($this->_execcommandCallback) {
					$this->_script .= 'execcommand_callback : "' . $this->_execcommandCallback . '",';
				}
				
				$this->_script .= 'inline_styles : ' . $this->_phpBooleanToJs($this->_inlineStyles) . ',';
				$this->_script .= 'convert_fonts_to_spans : ' . $this->_phpBooleanToJs($this->_convertFontsToSpans);
				
			$this->_script .= '});';
		$this->_script .= '}';
		$this->_script .= 'tinyMCE' . $name . '();';
		
		
		
		$this->view->headScript()->appendScript($this->_script);
		
		
		
		/**
		 * Textarea
		 */
		$xhtml = '<textarea id="' . $name . '" name="' . $name . '">' . $value . '</textarea>';		
		
		return $xhtml;
		
	}
	
	
	
	/**
	 * Content CSS
	 * 
	 * @author Uros Pirnat
	 * @return string
	 */
	protected function _getContentCss()
	{
		$contentCss = implode(',', $this->_contentCss);
		
		return $contentCss;
	}
	
	
	/**
	 * Get theme
	 * 
	 * @author Uros Pirnat
	 * @return string
	 */
	protected function _getThemeCode()
	{
		$script  = 'theme : "' . $this->_theme['name'] . '",';
		
		$params = isset($this->_theme['params']) ? $this->_theme['params'] : array();
		if(is_array($params)) {
			foreach($params as $paramName => $paramValue) {
				if($paramName) {
					$script .= $paramName . ' : "' . $paramValue . '",';
				}
			}
		}
		
		
		return $script;		
		
	}
	
	
	/**
	 * Php boolean to javascript
	 * 
	 * @author Uros Pirnat
	 * @param boolean $value
	 * @return string
	 */
	protected function _phpBooleanToJs($value)
	{
		if($value) {
			return 'true';
		}
		
		return 'false';
	}	
	
	
	/**
	 * Plugins
	 * 
	 * @author Uros Pirnat
	 * @return string
	 */
	protected function _getPlugins()
	{
		$plugins = implode(',', $this->_plugins);
		
		return $plugins;
	}
	
	
	/**
	 * Set attributes
	 * 
	 * @author Uros Pirnat
	 * @param array $attrs
	 * @return object
	 */
	protected function _setAttrs($attrs)
	{
		if(!is_array($attrs)) {
			return;
		}
		
		if(array_key_exists('theme', $attrs)) {
			$this->_theme = $attrs['theme'];
		}
		
		if(array_key_exists('execcommand_callback', $attrs)) {
			$this->_execcommandCallback = $attrs['execcommand_callback'];
		}
		
		
		
		
		return $this;
	}
	
	
	/**
	 * Sets the view field 
	 * 
	 * @param $view Zend_View_Interface
	 */
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	

	
	
	
	
}