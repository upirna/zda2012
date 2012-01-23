<?php




/**
 * TinyMCE helper
 *
 * @uses helper Zend_View_Helper
 */
class Zend_View_Helper_TinymceNew {
	
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
	 * Tinymce
	 *
	 * @param string $name
	 * @param string $value
	 * @param int $height
	 * @return string
	 */
	public function tinymce($name, $value = null, $attribs = null) {

		/**
		 * InvalidElements
		 */
		$invalidElements = '';
		if(isset($attribs['invalidElements'])) {
			$invalidElements = $attribs['invalidElements'];
		}
		
		
		/**
		 * Check if any set is selected
		 */
		$set = isset($attribs['set']) ? $attribs['set'] : 'standard';
		unset($attribs['set']);
		switch($set) {
			
			case 'minimal':
				$set = $this->_minimalSet();
				break;
			
			case 'standard':
				$set = $this->_standardSet();
				break;
			
			case 'full':
				$set = $this->_fullSet();
				break;
			
		}
		
		/**
		 * Validation of XHTML
		 */
		$validation = '';
		if(isset($attribs['validation'])) {
			$validation = $attribs['validation'];
			unset($attribs['validation']);
			
			switch($validation) {
				
				case 'full':
					$validation = $this->_fullValidationOfXhtml();
					break;
			}
			
		}
		
		
		
		/**
		 * Height
		 */
		$height = isset($attribs['height']) ? $attribs['height'] : '500px';
		
		
		/**
		 * Template
		 */
		$template = '';
		if(isset($attribs['template'])) {
			$template = $attribs['template'];
			unset($attribs['template']);
		}
		
		
		
		/**
		 * Base URL
		 */
		$baseUrl = $this->view->baseUrl();
		
		/**
		 * Headscript
		 */
		$this->view->headScript()->appendFile( $baseUrl . '/scripts/tiny_mce/tiny_mce.js');
		

		
		$plugins = $set['plugins'];
		$themeAdvancedButtons1 = $set['themeAdvancedButtons1'];
		$themeAdvancedButtons2 = $set['themeAdvancedButtons2'];
		$themeAdvancedButtons3 = $set['themeAdvancedButtons3'];
		$themeAdvancedButtons4 = $set['themeAdvancedButtons4']; 
		
		/**
		 * Remove buttons
		 */
		$this->_checkButtons($themeAdvancedButtons1, $attribs);
		$this->_checkButtons($themeAdvancedButtons2, $attribs);
		$this->_checkButtons($themeAdvancedButtons3, $attribs);
		$this->_checkButtons($themeAdvancedButtons4, $attribs);
		
		
		/**
		 * Script
		 */
		$script  = 'function tinyMCE' . $name . '() { tinyMCE.init({';
		
			/**
			 * Mode and textarea name
			 */
			$script .= 'mode: "exact",';
			$script .= 'invalid_elements: "' . $invalidElements . '",';
			$script .= 'elements : "' . $name . '",';
			
			/**
			 * Plugns
			 */
			$script .= 'plugins : "' . implode(',', $plugins) . '",';
			
			/**
			 * Themes
			*/
			$script .= 'theme : "advanced",'; 
	
			/**
			 * Buttons
			 */
			$script .= 'theme_advanced_buttons1 : "' . implode(',', $themeAdvancedButtons1) . '",';
			$script .= 'theme_advanced_buttons2 : "' . implode(',', $themeAdvancedButtons2) . '",';
			$script .= 'theme_advanced_buttons3 : "' . implode(',', $themeAdvancedButtons3) . '",';
			$script .= 'theme_advanced_buttons4 : "' . implode(',', $themeAdvancedButtons4) . '",';
			
			/**
			 * Toolbar loation, resizing
			 */
			$script .= 'theme_advanced_toolbar_location : "top",';
			$script .= 'theme_advanced_toolbar_align : "left",';
			$script .= 'theme_advanced_statusbar_location : "bottom",';
			$script .= 'theme_advanced_resizing : true,';
			$script .= $validation;
			
			/**
			 * Drop lists for link/image/media/template dialogs
			 */
			$script .= 'template_external_list_url : "lists/template_list.js",';
			$script .= 'external_link_list_url : "lists/link_list.js",';
			$script .= 'external_image_list_url : "lists/image_list.js",';
			$script .= 'media_external_list_url : "lists/media_list.js",';
			$script .= 'remove_linebreaks : false,';
			$script .= 'convert_urls : false,';
			$script .= 'use_native_selects: true,';
			$script .= 'file_browser_callback : "fileBrowserCallBack",';
			
			/**
			 * Custom css
			 */
			if(isset($attribs['content_css'])) {
				$script .= 'content_css : "' . $attribs['content_css'] . '",';
			}	
			
			$script .= $template;
			
			
			
			$params = array(
				'module' => 'filemanager',
				'action' => 'index',
				'controller' => 'index',
				'mode' => 2
			);
			
			
			$this->view->headScript()->appendScript('
			
				function fileBrowserCallBack(field_name, url, type, win) {
					
					var connector = "' . $this->view->url($params, 'default') . '";
	
					switch (type) {
						case "image":
							connector += "?type=img";
							break;
						case "media":
							connector += "?type=media";
							break;
						case "flash": //for older versions of tinymce
							connector += "?type=media";
							break;
						case "file":
							connector += "?type=files";
							break;
					}
					
					
					tinyMCE.activeEditor.windowManager.open({
						file : connector,
						title : "File Manager",
						width : 1000,  // Your dimensions may differ - toy around with them!
						height : 530,
						scrollbars: true,
						resizable : "no",
						inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
						close_previous : "no"
					}, {
						window : win,
						input : field_name
					});
					
					return false;			
			}	
			');
		
		$script .= '});}
		
		
		tinyMCE' . $name . '();
		
		';
		
		
		/**
		 * Append script
		 */
		$this->_script = $script;
		$this->view->headScript()->appendScript($script);
		
		
		/**
		 * Textarea
		 */
		$xhtml = '<textarea style="height:' . $height . ';" id="' . $name . '" name="' . $name . '">' . $value . '</textarea>';		
		
		return $xhtml;
	}
	
	
	/**
	 * Get script
	 * 
	 * @return string
	 */
	public function getScript() {
		return $this->_script;
	}
	
	
	/**
	 * Sets the view field 
	 * 
	 * @param $view Zend_View_Interface
	 */
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	
	/**
	 * Check buttons
	 *
	 * @param array $buttons
	 * @param array $attribs
	 * @param void
	 */
	protected function _checkButtons(&$buttons, $attribs) {
		foreach ($buttons as $key => $button) {
			if((isset($attribs[$button]) && $attribs[$button] == false)) {
				unset($buttons[$key]);
			}
		}
	}
	
	
	/**
	 * Full set
	 *
	 * @return array
	 */
	protected function _fullSet() {
		
		/**
		 * Plugins
		 */
		$plugins = array(
			'safari',
			'pagebreak',
			'style',
			'layer',
			'table',
			'save',
			'advhr',
			'advimage',
			'advlink',
			'emotions',
			'bbcode',
			'iespell',
			//'inlinepopups',
			'insertdatetime',
			'preview',
			'media',
			'searchreplace',
			'print',
			'contextmenu',
			'paste',
			'directionality',
			'fullscreen',
			'noneditable',
			'visualchars',
			'nonbreaking',
			'xhtmlxtras',
			'template',
		);
		
		
		/**
		 * Buttons 
		 */
		$themeAdvancedButtons1 = array(
			'save',
			'newdocument',
			'|',
			'bold',
			'italic',
			'underline',
			'strikethrough',
			'|',
			'justifyleft',
			'justifycenter',
			'justifyright',
			'justifyfull',
			'styleselect',
			'formatselect',
			'fontselect',
			'fontsizeselect'
		);
		
		$themeAdvancedButtons2 = array(
			'cut',
			'copy',
			'paste',
			'pastetext',
			'pasteword',
			'|',
			'search',
			'replace',
			'|',
			'bullist',
			'numlist',
			'|',
			'outdent',
			'indent',
			'blockquote',
			'|',
			'undo',
			'redo',
			'|',
			'link',
			'unlink',
			'anchor',
			'image',
			'cleanup',
			'help',
			'code',
			'|',
			'insertdate',
			'inserttime',
			'preview',
			'|',
			'forecolor',
			'backcolor'
		);
		
		$themeAdvancedButtons3 = array(
			'tablecontrols',
			'|',
			'hr',
			'removeformat',
			'visualaid',
			'|',
			'sub',
			'sup',
			'|',
			'charmap',
			'emotions',
			'iespell',
			'media',
			'advhr',
			'|',
			'print',
			'|',
			'ltr',
			'rtl',
			'|',
			'fullscreen'
		);
		
		$themeAdvancedButtons4 = array(
			'insertlayer',
			'moveforward',
			'movebackward',
			'absolute',
			'|',
			'styleprops',
			'|',
			'cite',
			'abbr',
			'acronym',
			'del',
			'ins',
			'attribs',
			'|',
			'visualchars',
			'nonbreaking',
			'template',
			'pagebreak'
		);

		
		return array(
			'plugins' => $plugins,
			'themeAdvancedButtons1' => $themeAdvancedButtons1,
			'themeAdvancedButtons2' => $themeAdvancedButtons2,
			'themeAdvancedButtons3' => $themeAdvancedButtons3,
			'themeAdvancedButtons4' => $themeAdvancedButtons4
		);
		
	}
	
	
	/**
	 * Standard set
	 *
	 * @return array
	 */
	protected function _standardSet() {
		
		/**
		 * Plugins
		 */
		$plugins = array(
			'safari',
			'pagebreak',
			'style',
			'layer',
			'table',
			'save',
			'advhr',
			'advimage',
			'advlink',
			'emotions',
			'iespell',
			'bbcode',
			//'inlinepopups',
			'insertdatetime',
			'preview',
			'media',
			'searchreplace',
			'print',
			'contextmenu',
			'paste',
			'directionality',
			'fullscreen',
			'noneditable',
			'visualchars',
			'nonbreaking',
			'xhtmlxtras',
			'template'
		);
		
		
		/**
		 * Buttons 
		 */
		$themeAdvancedButtons1 = array(
			'newdocument',
			'|',
			'bold',
			'italic',
			'underline',
			'strikethrough',
			'|',
			'undo',
			'redo',
			'|',	
			'justifyleft',
			'justifycenter',
			'justifyright',
			'justifyfull',
			'styleselect',
			'formatselect',
			'fontselect',
			'fontsizeselect',
			'removeformat'
		);
		
		$themeAdvancedButtons2 = array(
			'cut',
			'copy',
			'pastetext',
			'pasteword',
			'template',
			'bullist',
			'numlist',
			'outdent',
			'indent',
			'link',
			'unlink',
			'image',
			'table',
			'forecolor',
			'backcolor',
			'media',
			'cleanup',
			'fullscreen',
			'code',
			'sub',
			'sup',
			'charmap'
		);
		
		$themeAdvancedButtons3 = array(
			'tablecontrols',
		);
		
		
		return array(
			'plugins' => $plugins,
			'themeAdvancedButtons1' => $themeAdvancedButtons1,
			'themeAdvancedButtons2' => $themeAdvancedButtons2,
			'themeAdvancedButtons3' => $themeAdvancedButtons3,
			'themeAdvancedButtons4' => array()
		);
		
	}
	
	
	/**
	 * Minimal set
	 *
	 * @return array
	 */
	protected function _minimalSet() {
		
		/**
		 * Plugins
		 */
		$plugins = array(
			'safari',
			'pagebreak',
			'style',
			'layer',
			'table',
			'save',
			'advhr',
			'advimage',
			'advlink',
			'emotions',
			'iespell',
			//'inlinepopups',
			'insertdatetime',
			'preview',
			'media',
			'searchreplace',
			'print',
			'contextmenu',
			'paste',
			'directionality',
			'fullscreen',
			'noneditable',
			'visualchars',
			'nonbreaking',
			'xhtmlxtras',
			'template'
		);
		
		
		/**
		 * Buttons 
		 */
		$themeAdvancedButtons1 = array(
			'newdocument',
			'|',
			'bold',
			'italic',
			'underline',
			'strikethrough',
			'|',
			'undo',
			'redo',
			'|',	
			'justifyleft',
			'justifycenter',
			'justifyright',
			'justifyfull',
			'removeformat',
			'image',
			'forecolor',
			'backcolor',
			'media',
			'cleanup',
			'fullscreen',
			'code',
			'sub',
			'sup',
			'charmap',
			'bullist',
			'numlist',
			'outdent',
			'indent',
			'link',
			'unlink'
		);
		
		$themeAdvancedButtons2 = array();
		
		$themeAdvancedButtons3 = array();
		
		
		return array(
			'plugins' => $plugins,
			'themeAdvancedButtons1' => $themeAdvancedButtons1,
			'themeAdvancedButtons2' => $themeAdvancedButtons2,
			'themeAdvancedButtons3' => array(),
			'themeAdvancedButtons4' => array()
		);
		
	}
	
	
	/**
	 * Validate all xhtml
	 * 
	 * @return string
	 */
	protected function _fullValidationOfXhtml() {

$validElements = <<< ELEMENTS
valid_elements : ""
+"style[type],"
+"a[accesskey|charset|class|coords|dir<ltr?rtl|href|hreflang|id|lang|name"
  +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|rel|rev"
  +"|shape<circle?default?poly?rect|style|tabindex|title|target|type],"
+"abbr[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"acronym[class|dir<ltr?rtl|id|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"address[class|align|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|style|title],"
+"applet[align<bottom?left?middle?right?top|alt|archive|class|code|codebase"
  +"|height|hspace|id|name|object|style|title|vspace|width],"
+"area[accesskey|alt|class|coords|dir<ltr?rtl|href|id|lang|nohref<nohref"
  +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup"
  +"|shape<circle?default?poly?rect|style|tabindex|title|target],"
+"base[href|target],"
+"basefont[color|face|id|size],"
+"bdo[class|dir<ltr?rtl|id|lang|style|title],"
+"big[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"blockquote[dir|style|cite|class|dir<ltr?rtl|id|lang|onclick|ondblclick"
  +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
  +"|onmouseover|onmouseup|style|title],"
+"body[alink|background|bgcolor|class|dir<ltr?rtl|id|lang|link|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onload|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|onunload|style|title|text|vlink],"
+"br[class|clear<all?left?none?right|id|style|title],"
+"button[accesskey|class|dir<ltr?rtl|disabled<disabled|id|lang|name|onblur"
  +"|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onmousedown"
  +"|onmousemove|onmouseout|onmouseover|onmouseup|style|tabindex|title|type"
  +"|value],"
+"caption[align<bottom?left?right?top|class|dir<ltr?rtl|id|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"center[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"cite[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"code[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"col[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
  +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
  +"|onmousemove|onmouseout|onmouseover|onmouseup|span|style|title"
  +"|valign<baseline?bottom?middle?top|width],"
+"colgroup[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl"
  +"|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
  +"|onmousemove|onmouseout|onmouseover|onmouseup|span|style|title"
  +"|valign<baseline?bottom?middle?top|width],"
+"dd[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
+"del[cite|class|datetime|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|style|title],"
+"dfn[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"dir[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|style|title],"
+"div[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"dl[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|style|title],"
+"dt[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
+"em/i[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"fieldset[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"font[class|color|dir<ltr?rtl|face|id|lang|size|style|title],"
+"form[accept|accept-charset|action|class|dir<ltr?rtl|enctype|id|lang"
  +"|method<get?post|name|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onreset|onsubmit"
  +"|style|title|target],"
+"frame[class|frameborder|id|longdesc|marginheight|marginwidth|name"
  +"|noresize<noresize|scrolling<auto?no?yes|src|style|title],"
+"frameset[class|cols|id|onload|onunload|rows|style|title],"
+"h1[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"h2[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"h3[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"h4[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"h5[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"h6[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"head[dir<ltr?rtl|lang|profile],"
+"hr[align<center?left?right|class|dir<ltr?rtl|id|lang|noshade<noshade|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|size|style|title|width],"
+"html[dir<ltr?rtl|lang|version],"
+"iframe[align<bottom?left?middle?right?top|class|frameborder|height|id"
  +"|longdesc|marginheight|marginwidth|name|scrolling<auto?no?yes|src|style"
  +"|title|width],"
+"img[align<bottom?left?middle?right?top|alt|border|class|dir<ltr?rtl|height"
  +"|hspace|id|ismap<ismap|lang|longdesc|name|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|src|style|title|usemap|vspace|width],"
+"input[accept|accesskey|align<bottom?left?middle?right?top|alt"
  +"|checked<checked|class|dir<ltr?rtl|disabled<disabled|id|ismap<ismap|lang"
  +"|maxlength|name|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect"
  +"|readonly<readonly|size|src|style|tabindex|title"
  +"|type<button?checkbox?file?hidden?image?password?radio?reset?submit?text"
  +"|usemap|value],"
+"ins[cite|class|datetime|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|style|title],"
+"isindex[class|dir<ltr?rtl|id|lang|prompt|style|title],"
+"kbd[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"label[accesskey|class|dir<ltr?rtl|for|id|lang|onblur|onclick|ondblclick"
  +"|onfocus|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
  +"|onmouseover|onmouseup|style|title],"
+"legend[align<bottom?left?right?top|accesskey|class|dir<ltr?rtl|id|lang"
  +"|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"li[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title|type"
  +"|value],"
+"link[charset|class|dir<ltr?rtl|href|hreflang|id|lang|media|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|rel|rev|style|title|target|type],"
+"map[class|dir<ltr?rtl|id|lang|name|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"menu[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|style|title],"
+"meta[content|dir<ltr?rtl|http-equiv|lang|name|scheme],"
+"noframes[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"noscript[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"object[align<bottom?left?middle?right?top|archive|border|class|classid"
  +"|codebase|codetype|data|declare|dir<ltr?rtl|height|hspace|id|lang|name"
  +"|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|standby|style|tabindex|title|type|usemap"
  +"|vspace|width],"
+"ol[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|start|style|title|type],"
+"optgroup[class|dir<ltr?rtl|disabled<disabled|id|label|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"option[class|dir<ltr?rtl|disabled<disabled|id|label|lang|onclick|ondblclick"
  +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
  +"|onmouseover|onmouseup|selected<selected|style|title|value],"
+"p[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"param[id|name|type|value|valuetype<DATA?OBJECT?REF],"
+"pre/listing/plaintext/xmp[align|class|dir<ltr?rtl|id|lang|onclick|ondblclick"
  +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
  +"|onmouseover|onmouseup|style|title|width],"
+"q[cite|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"s[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
+"samp[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"script[charset|defer|language|src|type],"
+"select[class|dir<ltr?rtl|disabled<disabled|id|lang|multiple<multiple|name"
  +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|size|style"
  +"|tabindex|title],"
+"small[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"span[align|class|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|style|title],"
+"strike[class|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|style|title],"
+"strong/b[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"style[dir<ltr?rtl|lang|media|title|type],"
+"sub[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"sup[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"table[align<center?left?right|bgcolor|border|cellpadding|cellspacing|class"
  +"|dir<ltr?rtl|frame|height|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|rules"
  +"|style|summary|title|width],"
+"tbody[align<center?char?justify?left?right|char|class|charoff|dir<ltr?rtl|id"
  +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
  +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
  +"|valign<baseline?bottom?middle?top],"
+"td[abbr|align<center?char?justify?left?right|axis|bgcolor|char|charoff|class"
  +"|colspan|dir<ltr?rtl|headers|height|id|lang|nowrap<nowrap|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|rowspan|scope<col?colgroup?row?rowgroup"
  +"|style|title|valign<baseline?bottom?middle?top|width],"
+"textarea[accesskey|class|cols|dir<ltr?rtl|disabled<disabled|id|lang|name"
  +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect"
  +"|readonly<readonly|rows|style|tabindex|title],"
+"tfoot[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
  +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
  +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
  +"|valign<baseline?bottom?middle?top],"
+"th[abbr|align<center?char?justify?left?right|axis|bgcolor|char|charoff|class"
  +"|colspan|dir<ltr?rtl|headers|height|id|lang|nowrap<nowrap|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|rowspan|scope<col?colgroup?row?rowgroup"
  +"|style|title|valign<baseline?bottom?middle?top|width],"
+"thead[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
  +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
  +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
  +"|valign<baseline?bottom?middle?top],"
+"title[dir<ltr?rtl|lang],"
+"tr[abbr|align<center?char?justify?left?right|bgcolor|char|charoff|class"
  +"|rowspan|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title|valign<baseline?bottom?middle?top],"
+"tt[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
+"u[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
+"ul[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|style|title|type],"
+"var[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title]",


ELEMENTS;
		
		return $validElements;
		
	}
	
	
	
	
	
	
}
