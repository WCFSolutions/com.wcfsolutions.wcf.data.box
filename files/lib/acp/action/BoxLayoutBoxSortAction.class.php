<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/action/AbstractBoxLayoutAction.class.php');
require_once(WCF_DIR.'lib/data/box/BoxEditor.class.php');
require_once(WCF_DIR.'lib/data/box/position/BoxPosition.class.php');

/**
 * Sorts the structure of boxes.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.action
 * @category	Community Framework
 */
class BoxLayoutBoxSortAction extends AbstractBoxLayoutAction {
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
	 * box position id
	 * 
	 * @var	integer
	 */
	public $boxPositionID = 0;
	
	/**
	 * box object
	 * 
	 * @var	BoxPosition
	 */
	public $boxPosition = null;
	
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
		
		// get box
		if (isset($_REQUEST['boxID'])) $this->boxID = intval($_REQUEST['boxID']);
		$this->box = new BoxEditor($this->boxID);
		
		// get box position
		if (isset($_REQUEST['boxPositionID'])) $this->boxPositionID = intval($_REQUEST['boxPositionID']);
		$this->boxPosition = new BoxPosition($this->boxPositionID);
		
		// get show order
		if (isset($_REQUEST['showOrder'])) $this->showOrder = intval($_REQUEST['showOrder']);
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('admin.box.canEditBoxLayout');
		
		// update show order
		$this->boxLayout->updateBoxShowOrder($this->boxPositionID, $this->boxID, $this->showOrder);
		
		// reset cache
		BoxEditor::clearCache();
		$this->executed();
	}
}
?>