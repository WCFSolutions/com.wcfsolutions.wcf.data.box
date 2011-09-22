<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/box/layout/BoxLayoutEditor.class.php');

/**
 * Provides default implementations for box layout actions.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.action
 * @category	Community Framework
 */
abstract class AbstractBoxLayoutAction extends AbstractAction {
	/**
	 * box layout id
	 * 
	 * @var	integer
	 */
	public $boxLayoutID = 0;
	
	/**
	 * box layout editor object
	 * 
	 * @var	BoxLayoutEditor
	 */
	public $boxLayout = null;
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get box layout
		if (isset($_REQUEST['boxLayoutID'])) $this->boxLayoutID = intval($_REQUEST['boxLayoutID']);
		$this->boxLayout = new BoxLayoutEditor($this->boxLayoutID);
	}
}
?>