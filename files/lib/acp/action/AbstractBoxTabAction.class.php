<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/box/BoxEditor.class.php');
require_once(WCF_DIR.'lib/data/box/tab/BoxTabEditor.class.php');

/**
 * Provides default implementations for box tab actions.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.action
 * @category	Community Framework
 */
abstract class AbstractBoxTabAction extends AbstractAction {
	/**
	 * box tab id
	 * 
	 * @var	integer
	 */
	public $boxTabID = 0;
	
	/**
	 * box tab editor object
	 * 
	 * @var	BoxTabEditor
	 */
	public $boxTab = null;
	
	/**
	 * box editor object
	 * 
	 * @var	BoxEditor
	 */
	public $box = null;
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get box tab
		if (isset($_REQUEST['boxTabID'])) $this->boxTabID = intval($_REQUEST['boxTabID']);
		$this->boxTab = new BoxTabEditor($this->boxTabID);
		
		// get box
		$this->box = new BoxEditor($this->boxTab->boxID);
	}
}
?>