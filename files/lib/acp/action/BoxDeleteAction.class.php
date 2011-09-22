<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/box/BoxEditor.class.php');

/**
 * Deletes a box.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.action
 * @category	Community Framework
 */
class BoxDeleteAction extends AbstractAction {
	/**
	 * box id
	 * 
	 * @var integer
	 */
	public $boxID = 0;
	
	/**
	 * box object
	 * 
	 * @var BoxEditor
	 */
	public $box = null;
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get box
		if (isset($_REQUEST['boxID'])) $this->boxID = intval($_REQUEST['boxID']);
		$this->box = new BoxEditor($this->boxID);
	}
		
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('admin.box.canDeleteBox');
		
		// delete box
		$this->box->delete();
		
		// clear cache
		BoxEditor::clearCache();
		$this->executed();
		
		// forward to list page
		HeaderUtil::redirect('index.php?page=BoxList&deletedBoxID='.$this->boxID.'&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>