<?php
// wcf imports
require_once(WCF_DIR.'lib/data/box/Box.class.php');
require_once(WCF_DIR.'lib/data/box/tab/BoxTabList.class.php');
require_once(WCF_DIR.'lib/page/SortablePage.class.php');

/**
 * Shows a list of all box tabs.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.page
 * @category	Community Framework
 */
class BoxTabListPage extends SortablePage {
	// system
	public $templateName = 'boxTabList';
	public $defaultSortField = 'showOrder';
	public $neededPermissions = array('admin.box.canEditBoxTab', 'admin.box.canDeleteBoxTab');

	/**
	 * deleted box tab id
	 *
	 * @var	integer
	 */
	public $deletedBoxTabID = 0;

	/**
	 * box tab list object
	 *
	 * @var	BoxTabList
	 */
	public $boxTabList = null;

	/**
	 * box id
	 *
	 * @var	integer
	 */
	public $boxID = 0;

	/**
	 * box object
	 *
	 * @var	Box
	 */
	public $box = null;

	/**
	 * list of boxes
	 *
	 * @var	array<Box>
	 */
	public $boxes = array();

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['deletedBoxTabID'])) $this->deletedBoxTabID = intval($_REQUEST['deletedBoxTabID']);

		// get box
		if (isset($_REQUEST['boxID'])) $this->boxID = intval($_REQUEST['boxID']);
		if ($this->boxID) {
			$this->box = new Box($this->boxID);
		}

		// init box tab list
		if ($this->box !== null) {
			$this->boxTabList = new BoxTabList();
			$this->boxTabList->sqlConditions = 'box_tab.boxID = '.$this->box->boxID;
		}
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function readData() {
		parent::readData();

		// read box tabs
		if ($this->boxTabList !== null) {
			$this->boxTabList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
			$this->boxTabList->sqlLimit = $this->itemsPerPage;
			$this->boxTabList->sqlOrderBy = 'box_tab.'.$this->sortField." ".$this->sortOrder;
			$this->boxTabList->readObjects();
		}

		// get boxes
		$this->boxes = Box::getBoxes();
	}

	/**
	 * @see SortablePage::validateSortField()
	 */
	public function validateSortField() {
		parent::validateSortField();

		switch ($this->sortField) {
			case 'boxTabID':
			case 'boxTab':
			case 'boxTabType':
			case 'showOrder': break;
			default: $this->sortField = $this->defaultSortField;
		}
	}

	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();

		if ($this->boxTabList === null) return 0;
		return $this->boxTabList->countObjects();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'boxTabs' => ($this->boxTabList !== null ? $this->boxTabList->getObjects() : array()),
			'boxID' => $this->boxID,
			'box' => $this->box,
			'boxes' => $this->boxes,
			'deletedBoxTabID' => $this->deletedBoxTabID
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show() {
		// enable menu item
		WCFACP::getMenu()->setActiveMenuItem('wcf.acp.menu.link.content.box.tab.view');

		parent::show();
	}
}
?>