<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/form/ACPForm.class.php');
require_once(WCF_DIR.'lib/data/box/BoxEditor.class.php');

/**
 * Shows the box add form.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.form
 * @category	Community Framework
 */
class BoxAddForm extends ACPForm {
	// system
	public $templateName = 'boxAdd';
	public $neededPermissions = 'admin.box.canAddBox';
	public $menuItemName = 'wcf.acp.menu.link.content.box.add';

	// parameters
	public $boxName = '';
	public $description = '';
	public $enableTitle = 1;
	public $isClosable = 1;

	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		$this->enableTitle = $this->isClosable = 0;

		if (isset($_POST['boxName'])) $this->boxName = StringUtil::trim($_POST['boxName']);
		if (isset($_POST['description'])) $this->description = StringUtil::trim($_POST['description']);
		if (isset($_POST['enableTitle'])) $this->enableTitle = intval($_POST['enableTitle']);
		if (isset($_POST['isClosable'])) $this->isClosable = intval($_POST['isClosable']);
	}

	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();

		// box name
		if (empty($this->boxName)) {
			throw new UserInputException('boxName');
		}

		// closed
		if (!$this->enableTitle) {
			$this->isClosable = 0;
		}
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();

		// create box
		$this->box = BoxEditor::create($this->boxName, $this->description, $this->enableTitle, $this->isClosable, WCF::getLanguage()->getLanguageID());
		BoxEditor::clearCache();
		$this->saved();

		// reset values
		$this->boxName = $this->description = '';
		$this->enableTitle = $this->isClosable = 1;

		// show success message
		WCF::getTPL()->assign('success', true);
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'action' => 'add',
			'boxName' => $this->boxName,
			'description' => $this->description,
			'enableTitle' => $this->enableTitle,
			'isClosable' => $this->isClosable
		));
	}

	/**
	 * @see Form::show()
	 */
	public function show() {
		// set active menu item
		WCFACP::getMenu()->setActiveMenuItem($this->menuItemName);

		// show form
		parent::show();
	}
}
?>