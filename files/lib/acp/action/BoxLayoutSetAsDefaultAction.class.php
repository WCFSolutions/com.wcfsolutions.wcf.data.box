<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/action/AbstractBoxLayoutAction.class.php');

/**
 * Sets a box layout as default box layout for a package.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.action
 * @category	Community Framework
 */
class BoxLayoutSetAsDefaultAction extends AbstractBoxLayoutAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		// check permission
		WCF::getUser()->checkPermission('admin.box.canEditBoxLayout');

		// set as default
		$this->boxLayout->setAsDefault();

		// reset cache
		WCF::getCache()->clearResource('boxLayout-'.PACKAGE_ID);
		$this->executed();

		// forward to list page
		HeaderUtil::redirect('index.php?page=BoxLayoutList&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>