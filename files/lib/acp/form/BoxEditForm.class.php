<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/form/BoxAddForm.class.php');

/**
 * Shows the box edit form.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.form
 * @category	Community Framework
 */
class BoxEditForm extends BoxAddForm {
	// system
	public $menuItemName = 'wcf.acp.menu.link.content.box';
	public $neededPermissions = 'admin.box.canEditBox';

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
		parent::readParameters();

		// get language id
		if (isset($_REQUEST['languageID'])) $this->languageID = intval($_REQUEST['languageID']);
		else $this->languageID = WCF::getLanguage()->getLanguageID();

		// get box
		if (isset($_REQUEST['boxID'])) $this->boxID = intval($_REQUEST['boxID']);
		$this->box = new BoxEditor($this->boxID);
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		// get all available languages
		$this->languages = Language::getLanguageCodes();

		// default values
		if (!count($_POST)) {
			$this->enableTitle = $this->box->enableTitle;
			$this->isClosable = $this->box->isClosable;

			// get name and description
			if (WCF::getLanguage()->getLanguageID() != $this->languageID) $language = new Language($this->languageID);
			else $language = WCF::getLanguage();
			$this->boxName = $language->get('wcf.box.'.$this->box->box);
			if ($this->boxName == 'wcf.box.'.$this->box->box) $this->boxName = '';
			$this->description = $language->get('wcf.box.'.$this->box->box.'.description');
			if ($this->description == 'wcf.box.'.$this->box->box.'.description') $this->description = '';
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
			'boxID' => $this->boxID,
			'languageID' => $this->languageID,
			'languages' => $this->languages
		));
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		AbstractForm::save();

		// fix closed boxes
		if ($this->box->isClosable && !$this->isClosable) {
			$sql = "DELETE FROM	wcf".WCF_N."_box_closed_to_user
				WHERE		boxID = ".$this->box->boxID;
			WCF::getDB()->sendQuery($sql);
		}

		// update box
		$this->box->update($this->boxName, $this->description, $this->enableTitle, $this->isClosable, $this->languageID);

		// reset cache
		BoxEditor::clearCache();
		$this->saved();

		// show success message
		WCF::getTPL()->assign('success', true);
	}
}
?>