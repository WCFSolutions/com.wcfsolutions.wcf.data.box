<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/form/BoxTabAddForm.class.php');

/**
 * Shows the box tab edit form.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.form
 * @category	Community Framework
 */
class BoxTabEditForm extends BoxTabAddForm {
	// system
	public $menuItemName = 'wcf.acp.menu.link.content.box.tab';
	public $neededPermissions = 'admin.box.canEditBoxTab';
	
	/**
	 * box id
	 * 
	 * @var	integer
	 */
	public $boxID = 0;
	
	/**
	 * language id
	 * 
	 * @var	integer
	 */
	public $languageID = 0;
	
	/**
	 * list of available languages
	 * 
	 * @var	array
	 */
	public $languages = array();
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		DynamicOptionListForm::readParameters();
		
		// get language id
		if (isset($_REQUEST['languageID'])) $this->languageID = intval($_REQUEST['languageID']);
		else $this->languageID = WCF::getLanguage()->getLanguageID();
		
		// get box tab
		if (isset($_REQUEST['boxTabID'])) $this->boxTabID = intval($_REQUEST['boxTabID']);
		$this->boxTab = new BoxTabEditor($this->boxTabID);
		
		// get box tab type
		$sql = "SELECT	*
			FROM	wcf".WCF_N."_box_tab_type
			WHERE	boxTabType = '".escapeString($this->boxTab->boxTabType)."'";
		$this->boxTabType = WCF::getDB()->getFirstRow($sql);
		if (!$this->boxTabType['boxTabTypeID']) {
			throw new IllegalLinkException();
		}

		// init ckeditor
		if ($this->boxTabType['boxTabType'] == 'content') {
			$this->ckeditor = new CKEditor('text');
			$this->ckeditor->setConfigOptions(array(
				'baseHref' => "'".$this->ckeditor->encodeJS(RELATIVE_WSIP_DIR)."'",
				'height' => "'300px'"
			));
		}
		
		// get box id
		if (isset($_REQUEST['boxID'])) $this->boxID = intval($_REQUEST['boxID']);
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		// get all available languages
		$this->languages = Language::getLanguageCodes();
		
		// default values
		if (!count($_POST)) {
			$this->boxID = $this->boxTab->boxID;
			$this->showOrder = $this->boxTab->showOrder;
			
			foreach ($this->activeOptions as $key => $option) {
				$value = $this->boxTab->getBoxTabOption($option['optionName']);
				if ($value !== null) {
					$this->activeOptions[$key]['optionValue'] = $value;
				}
			}
			
			// get name
			if (WCF::getLanguage()->getLanguageID() != $this->languageID) $language = new Language($this->languageID);
			else $language = WCF::getLanguage();
			$this->boxTabName = $language->get('wcf.box.tab.'.$this->boxTab->boxTab);
			if ($this->boxTabName == 'wcf.box.tab.'.$this->boxTab->boxTab) $this->boxTabName = '';
			
			// get content
			if ($this->boxTabType['boxTabType'] == 'content') {
				if (isset($this->activeOptions['text']['optionValue'])) {
					$content = $language->get($this->activeOptions['text']['optionValue']);
					if ($content == 'wcf.box.tab.'.$this->boxTab->boxTab.'.text') $content = '';
					$this->activeOptions['text']['optionValue'] = $content;
				}
			}
		}
		
		parent::readData();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'boxTabID' => $this->boxTabID,
			'languageID' => $this->languageID,
			'languages' => $this->languages
		));
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		AbstractForm::save();
		
		// update box tob
		$this->boxTab->update($this->boxID, $this->boxTabName, $this->activeOptions, $this->showOrder, $this->languageID);
		
		// reset cache
		BoxTabEditor::clearCache();
		BoxEditor::clearCache();
		
		// reset box cache
		BoxTab::resetBoxTabCacheByBoxTabType($this->boxTabType['boxTabType']);
		$this->saved();
		
		// show success message
		WCF::getTPL()->assign('success', true);
	}
}
?>