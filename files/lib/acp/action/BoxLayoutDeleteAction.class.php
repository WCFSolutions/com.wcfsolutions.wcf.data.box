<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/action/AbstractBoxLayoutAction.class.php');

/**
 * Deletes a box layout.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.action
 * @category	Community Framework
 */
class BoxLayoutDeleteAction extends AbstractBoxLayoutAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		if ($this->boxLayout->isDefault) {
			throw new IllegalLinkException();
		}
		
		// check permission
		WCF::getUser()->checkPermission('admin.box.canEditBoxLayout');
		
		// delete box layout
		$this->boxLayout->delete();
		
		// reset cache
		WCF::getCache()->clearResource('boxLayout-'.PACKAGE_ID);
		$this->executed();
		
		// forward to list page
		HeaderUtil::redirect('index.php?page=BoxLayoutList&deletedBoxLayoutID='.$this->boxLayout->boxLayoutID.'&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>