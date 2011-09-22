<?php
// wcf imports
require_once(WCF_DIR.'lib/data/box/BoxList.class.php');
require_once(WCF_DIR.'lib/page/SortablePage.class.php');

/**
 * Shows a list of all boxes.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.page
 * @category	Community Framework
 */
class BoxListPage extends SortablePage {
	// system
	public $templateName = 'boxList';
	public $defaultSortField = 'boxID';
	public $neededPermissions = array('admin.box.canEditBox', 'admin.box.canDeleteBox');
	
	/**
	 * deleted box id
	 * 
	 * @var	integer
	 */
	public $deletedBoxID = 0;
	
	/**
	 * box list object
	 * 
	 * @var	BoxList
	 */
	public $boxList = null;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['deletedBoxID'])) $this->deletedBoxID = intval($_REQUEST['deletedBoxID']);
		
		// init box list
		$this->boxList = new BoxList();
		$this->boxList->sqlConditions = "box.packageID IN (
							SELECT	dependency
							FROM	wcf".WCF_N."_package_dependency
							WHERE	packageID = ".PACKAGE_ID."
						)";
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function readData() {
		parent::readData();
		
		// read boxes
		$this->boxList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->boxList->sqlLimit = $this->itemsPerPage;
		$this->boxList->sqlOrderBy = ($this->sortField != 'boxTabs' ? 'box.' : '').$this->sortField." ".$this->sortOrder;
		$this->boxList->readObjects();
	}
	
	/**
	 * @see SortablePage::validateSortField()
	 */
	public function validateSortField() {
		parent::validateSortField();
		
		switch ($this->sortField) {
			case 'boxID':
			case 'box':
			case 'boxTabs': break;
			default: $this->sortField = $this->defaultSortField;
		}
	}
	
	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();
		
		return $this->boxList->countObjects();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'boxes' => $this->boxList->getObjects(),
			'deletedBoxID' => $this->deletedBoxID
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// enable menu item
		WCFACP::getMenu()->setActiveMenuItem('wcf.acp.menu.link.content.box.view');
		
		parent::show();
	}
}
?>