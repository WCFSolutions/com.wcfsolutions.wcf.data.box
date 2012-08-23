<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/action/AbstractBoxLayoutAction.class.php');
require_once(WCF_DIR.'lib/data/box/BoxEditor.class.php');
require_once(WCF_DIR.'lib/data/box/position/BoxPosition.class.php');

/**
 * Removes a box from a box layout.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.action
 * @category	Community Framework
 */
class BoxLayoutBoxRemoveAction extends AbstractBoxLayoutAction {
	/**
	 * box id
	 *
	 * @var	integer
	 */
	public $boxID = 0;

	/**
	 * box editor object
	 *
	 * @var	BoxEditor
	 */
	public $box = null;

	/**
	 * box position id
	 *
	 * @var	integer
	 */
	public $boxPositionID = 0;

	/**
	 * box position object
	 *
	 * @var	BoxPosition
	 */
	public $boxPosition = null;

	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// get box
		if (isset($_REQUEST['boxID'])) $this->boxID = intval($_REQUEST['boxID']);
		$this->box = new BoxEditor($this->boxID);

		// get box position
		if (isset($_REQUEST['boxPositionID'])) $this->boxPositionID = intval($_REQUEST['boxPositionID']);
		$this->boxPosition = new BoxPosition($this->boxPositionID);
	}

	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();

		// check permission
		WCF::getUser()->checkPermission('admin.box.canEditBoxLayout');

		// remove box
		$this->boxLayout->removeBox($this->box->boxID, $this->boxPosition->boxPositionID);

		// reset cache
		BoxEditor::clearCache();
		$this->executed();

		// forward to list page
		HeaderUtil::redirect('index.php?page=BoxLayoutBoxAssignment&boxLayoutID='.$this->boxLayout->boxLayoutID.'&boxPositionID='.$this->boxPosition->boxPositionID.'&removedBoxID='.$this->boxID.'&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>