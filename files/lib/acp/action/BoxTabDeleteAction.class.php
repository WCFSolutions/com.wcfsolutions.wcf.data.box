<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/action/AbstractBoxTabAction.class.php');

/**
 * Deletes a box tab.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.action
 * @category	Community Framework
 */
class BoxTabDeleteAction extends AbstractBoxTabAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('admin.box.canDeleteBoxTab');
		
		// delete box tab
		$this->boxTab->delete();
		
		// clear cache
		BoxTabEditor::clearCache();
		BoxEditor::clearCache();
		
		// reset box tab cache
		BoxTab::resetBoxTabCacheByBoxTabType($this->boxTab->boxTabType);
		$this->executed();
		
		// forward to list page
		HeaderUtil::redirect('index.php?page=BoxTabList&boxID='.$this->box->boxID.'&deletedBoxTabID='.$this->boxTabID.'&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>