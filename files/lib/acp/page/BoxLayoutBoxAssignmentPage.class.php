<?php
// wcf imports
require_once(WCF_DIR.'lib/data/box/Box.class.php');
require_once(WCF_DIR.'lib/data/box/layout/BoxLayoutEditor.class.php');
require_once(WCF_DIR.'lib/data/box/position/BoxPosition.class.php');
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows a list of all boxes.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.page
 * @category	Community Framework
 */
class BoxLayoutBoxAssignmentPage extends AbstractPage {
	// system
	public $templateName = 'boxLayoutBoxAssignment';
	public $neededPermissions = 'admin.box.canEditBox';

	/**
	 * removed box id
	 *
	 * @var	integer
	 */
	public $removedBoxID = 0;

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
	 * box position id
	 *
	 * @var	integer
	 */
	public $boxPositionID = 0;

	/**
	 * box position editor object
	 *
	 * @var	BoxPositionEditor
	 */
	public $boxPosition = null;

	/**
	 * list of available box layouts
	 *
	 * @var	array
	 */
	public $boxLayoutOptions = array();

	/**
	 * list of available box positions
	 *
	 * @var	array
	 */
	public $boxPositionOptions = array();

	/**
	 * list of available boxes
	 *
	 * @var	array
	 */
	public $boxOptions = array();

	/**
	 * list of boxes
	 *
	 * @var	array
	 */
	public $boxList = array();

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['removedBoxID'])) $this->removedBoxID = intval($_REQUEST['removedBoxID']);

		// get box layout
		if (isset($_REQUEST['boxLayoutID'])) {
			$this->boxLayoutID = intval($_REQUEST['boxLayoutID']);
			$this->boxLayout = new BoxLayoutEditor($this->boxLayoutID);

			// get box position
			if (isset($_REQUEST['boxPositionID'])) {
				$this->boxPositionID = intval($_REQUEST['boxPositionID']);
				$this->boxPosition = new BoxPosition($this->boxPositionID);
			}
		}
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function readData() {
		parent::readData();

		// get box layout options
		$this->boxLayoutOptions = BoxLayout::getBoxLayouts();

		// get position options
		$this->boxPositionOptions = BoxPosition::getBoxPositionOptions();

		// get boxes
		if ($this->boxPosition !== null) {
			$boxes = WCF::getCache()->get('box-'.PACKAGE_ID);
			$boxToLayouts = WCF::getCache()->get('boxLayout-'.PACKAGE_ID, 'boxes');

			$boxIDArray = array();
			if (isset($boxToLayouts[$this->boxLayoutID][$this->boxPositionID])) {
				$boxIDArray = $boxToLayouts[$this->boxLayoutID][$this->boxPositionID];
			}

			foreach ($boxIDArray as $boxID => $showOrder) {
				$this->boxList[] = array(
					'box' => $boxes[$boxID],
					'showOrder' => $showOrder
				);
			}

			// get box options
			$this->boxOptions = Box::getBoxOptions(array_keys($boxIDArray));
		}
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		// init form
		if ($this->boxPosition !== null) {
			require_once(WCF_DIR.'lib/acp/form/BoxLayoutBoxAddForm.class.php');
			new BoxLayoutBoxAddForm($this->boxLayout, $this->boxPosition);
		}

		WCF::getTPL()->assign(array(
			'boxLayoutID' => $this->boxLayoutID,
			'boxLayout' => $this->boxLayout,
			'boxPositionID' => $this->boxPositionID,
			'boxPosition' => $this->boxPosition,
			'boxLayoutOptions' => $this->boxLayoutOptions,
			'boxPositionOptions' => $this->boxPositionOptions,
			'boxOptions' => $this->boxOptions,
			'boxes' => $this->boxList,
			'removedBoxID' => $this->removedBoxID
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show() {
		// enable menu item
		WCFACP::getMenu()->setActiveMenuItem('wcf.acp.menu.link.content.box.layout.boxAssignment');

		parent::show();
	}
}
?>