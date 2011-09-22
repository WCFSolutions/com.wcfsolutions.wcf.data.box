<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/form/ACPForm.class.php');
require_once(WCF_DIR.'lib/data/box/layout/BoxLayoutEditor.class.php');

/**
 * Shows the box layout add form.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.form
 * @category	Community Framework
 */
class BoxLayoutAddForm extends ACPForm {
	// system
	public $templateName = 'boxLayoutAdd';
	public $activeMenuItem = 'wcf.acp.menu.link.content.box.layout.add';
	public $neededPermissions = 'admin.box.canAddBoxLayout';
	
	/**
	 * box layout editor object
	 * 
	 * @var	BoxLayoutEditor
	 */
	public $boxLayout = null;
	
	// parameters
	public $title = '';
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
	}
	
	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		// title
		if (empty($this->title)) {
			throw new UserInputException('title');
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		// save box layout
		$this->boxLayout = BoxLayoutEditor::create($this->title);
		
		// reset cache
		WCF::getCache()->clearResource('boxLayout-'.PACKAGE_ID);
		$this->saved();
		
		// reset values
		$this->title = '';
		
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
			'title' => $this->title
		));
	}
}
?>