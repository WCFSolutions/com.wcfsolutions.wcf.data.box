<?php
// wcf imports
require_once(WCF_DIR.'lib/form/AbstractForm.class.php');
require_once(WCF_DIR.'lib/data/box/BoxEditor.class.php');

/**
 * Shows the box layout box add form.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.form
 * @category	Community Framework
 */
class BoxLayoutBoxAddForm extends AbstractForm {
	/**
	 * box layout object
	 * 
	 * @var BoxLayout
	 */
	public $boxLayout = null;
	
	/**
	 * box position object
	 * 
	 * @var BoxPosition
	 */
	public $boxPosition = null;
	
	// parameters
	public $boxID = 0;
	
	/**
	 * Creates a new BoxLayoutBoxAddForm object.
	 * 
	 * @param	BoxLayout	$boxLayout
	 * @param	BoxPosition	$boxPosition
	 */
	public function __construct(BoxLayout $boxLayout, BoxPosition $boxPosition) {
		$this->boxLayout = $boxLayout;
		$this->boxPosition = $boxPosition;
		parent::__construct();
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// get parameters
		if (isset($_POST['boxID'])) $this->boxID = intval($_POST['boxID']);
	}
	
	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		$box = new Box($this->boxID);
		if (!$box->boxID) {
			throw new UserInputException('boxID', 'invalid');
		}
		
		$sql = "SELECT	COUNT(*) AS amount
			FROM	wcf".WCF_N."_box_to_layout
			WHERE	boxLayoutID = ".$this->boxLayout->boxLayoutID."
				AND boxPositionID = ".$this->boxPosition->boxPositionID."
				AND boxID = ".$this->boxID;
		$row = WCF::getDB()->getFirstRow($sql);
		if ($row['amount']) {
			throw new UserInputException('boxID', 'invalid');
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		// add box
		$this->boxLayout->addBox($this->boxID, $this->boxPosition->boxPositionID);
		
		// reset cache
		BoxEditor::clearCache();
		$this->saved();
		
		// forward
		HeaderUtil::redirect('index.php?page=BoxLayoutBoxAssignment&boxLayoutID='.$this->boxLayout->boxLayoutID.'&boxPositionID='.$this->boxPosition->boxPositionID.'&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'boxID' => $this->boxID
		));
	}
}
?>