<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/action/AbstractBoxTabAction.class.php');

/**
 * Sorts the structure of box tabs.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.action
 * @category	Community Framework
 */
class BoxTabSortAction extends AbstractBoxTabAction {
	/**
	 * new show order
	 * 
	 * @var integer
	 */
	public $showOrder = 0;
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get show order
		if (isset($_REQUEST['showOrder'])) $this->showOrder = intval($_REQUEST['showOrder']);
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('admin.box.canEditBoxTab');
		
		// update show order
		$this->boxTab->updateShowOrder($this->showOrder);
		
		// reset cache
		BoxTabEditor::clearCache();
		$this->executed();
	}
}
?>