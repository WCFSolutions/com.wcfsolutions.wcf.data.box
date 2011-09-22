<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/form/BoxLayoutAddForm.class.php');

/**
 * Shows the box layout edit form.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.form
 * @category	Community Framework
 */
class BoxLayoutEditForm extends BoxLayoutAddForm {
	// system
	public $activeMenuItem = 'wcf.acp.menu.link.content.box.layout';
	public $neededPermissions = 'admin.box.canEditBoxLayout';
	
	/**
	 * box layout id
	 * 
	 * @var	integer
	 */
	public $boxLayoutID = 0;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get box layout
		if (isset($_REQUEST['boxLayoutID'])) $this->boxLayoutID = intval($_REQUEST['boxLayoutID']);
		$this->boxLayout = new BoxLayoutEditor($this->boxLayoutID);
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (!count($_POST)) {
			// get values
			$this->title = $this->boxLayout->title;
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		AbstractForm::save();
		
		// update box layout
		$this->boxLayout->update($this->title);
		
		// reset cache
		WCF::getCache()->clearResource('boxLayout-'.PACKAGE_ID);
		$this->saved();
		
		// show success message
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'boxLayoutID' => $this->boxLayoutID,
			'boxLayout' => $this->boxLayout
		));
	}
}
?>