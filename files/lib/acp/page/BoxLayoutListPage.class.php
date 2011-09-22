<?php
// wcf imports
require_once(WCF_DIR.'lib/data/box/layout/BoxLayoutList.class.php');
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
class BoxLayoutListPage extends SortablePage {
	// system
	public $templateName = 'boxLayoutList';
	public $defaultSortField = 'title';
	public $neededPermissions = array('admin.box.canEditBox', 'admin.box.canDeleteBox');
	
	/**
	 * deleted box layout id
	 * 
	 * @var	integer
	 */
	public $deletedBoxLayoutID = 0;
	
	/**
	 * box layout list object
	 * 
	 * @var	BoxLayoutList
	 */
	public $boxLayoutList = null;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['deletedBoxLayoutID'])) $this->deletedBoxLayoutID = intval($_REQUEST['deletedBoxLayoutID']);
		
		// init box layout list
		$this->boxLayoutList = new BoxLayoutList();
		$this->boxLayoutList->sqlConditions = "	box_layout.packageID IN (
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
		
		// read box layouts
		$this->boxLayoutList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->boxLayoutList->sqlLimit = $this->itemsPerPage;
		$this->boxLayoutList->sqlOrderBy = ($this->sortField != 'boxes' ? 'box_layout.' : '').$this->sortField." ".$this->sortOrder;
		$this->boxLayoutList->readObjects();
	}
	
	/**
	 * @see SortablePage::validateSortField()
	 */
	public function validateSortField() {
		parent::validateSortField();
		
		switch ($this->sortField) {
			case 'boxLayoutID':
			case 'title':
			case 'boxes': break;
			default: $this->sortField = $this->defaultSortField;
		}
	}
	
	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();
		
		return $this->boxLayoutList->countObjects();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'boxLayouts' => $this->boxLayoutList->getObjects(),
			'deletedBoxLayoutID' => $this->deletedBoxLayoutID
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// enable menu item
		WCFACP::getMenu()->setActiveMenuItem('wcf.acp.menu.link.content.box.layout.view');
		
		parent::show();
	}
}
?>