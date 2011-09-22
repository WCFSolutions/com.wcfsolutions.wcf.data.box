<?php
// wcf imports
require_once(WCF_DIR.'lib/data/box/layout/BoxLayout.class.php');

/**
 * Provides functions to manage box layouts.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	data.box.layout
 * @category	Community Framework
 */
class BoxLayoutEditor extends BoxLayout {
	/**
	 * Creates a new BoxLayoutEditor object.
	 * 
	 * @param	integer		$boxLayoutID
	 * @param 	array<mixed>	$row
	 * @param	BoxLayout	$cacheObject
	 * @param	boolean		$useCache
	 */
	public function __construct($boxLayoutID, $row = null, $cacheObject = null, $useCache = true) {
		if ($useCache) parent::__construct($boxLayoutID, $row, $cacheObject);
		else {
			$sql = "SELECT	*
				FROM	wcf".WCF_N."_box_layout
				WHERE	boxLayoutID = ".$boxLayoutID;
			$row = WCF::getDB()->getFirstRow($sql);
			parent::__construct(null, $row);
		}
	}
	
	/**
	 * Creates a new box layout.
	 * 
	 * @param	string			$title
	 * @param	integer			$packageID
	 * @return	BoxLayoutEditor
	 */
	public static function create($title, $packageID = PACKAGE_ID) {		
		$sql = "INSERT INTO	wcf".WCF_N."_box_layout
					(packageID, title)
			VALUES		(".$packageID.", '".escapeString($title)."')";
		WCF::getDB()->sendQuery($sql);
		
		$boxLayoutID = WCF::getDB()->getInsertID("wcf".WCF_N."_box_layout", 'boxLayoutID');
		return new BoxLayoutEditor($boxLayoutID, null, null, false);
	}
	
	/**
	 * Sets this box layout as default box layout for the package with the given package id.
	 * 
	 * @param	integer			$packageID
	 */
	public function setAsDefault($packageID = PACKAGE_ID) {
		// remove old default
		$sql = "UPDATE	wcf".WCF_N."_box_layout
			SET	isDefault = 0
			WHERE	isDefault = 1
				AND packageID = ".$packageID;
		WCF::getDB()->sendQuery($sql);
		
		// set new default
		$sql = "UPDATE	wcf".WCF_N."_box_layout
			SET	isDefault = 1
			WHERE	boxLayoutID = ".$this->boxLayoutID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Updates this box layout.
	 * 
	 * @param	string		$title
	 */
	public function update($title) {
		$sql = "UPDATE	wcf".WCF_N."_box_layout
			SET	title = '".escapeString($title)."'
			WHERE	boxLayoutID = ".$this->boxLayoutID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Deletes this box layout.
	 */
	public function delete() {
		$sql = "DELETE FROM	wcf".WCF_N."_box_layout
			WHERE		boxLayoutID = ".$this->boxLayoutID;
		WCF::getDB()->sendQuery($sql);	
	}
	
	/**
	 * Adds the box with the given box id to this layout.
	 * 
	 * @param	integer		$boxID
	 * @param	integer		$boxPositionID
	 */
	public function addBox($boxID, $boxPositionID) {
		// get next number in row
		$sql = "SELECT	MAX(showOrder) AS showOrder
			FROM	wcf".WCF_N."_box_to_layout
			WHERE	boxLayoutID = ".$this->boxLayoutID."
				AND boxPositionID = ".$boxPositionID;
		$row = WCF::getDB()->getFirstRow($sql);
		if (!empty($row)) $showOrder = intval($row['showOrder']) + 1;
		else $showOrder = 1;
		
		// add box
		$sql = "REPLACE INTO	wcf".WCF_N."_box_to_layout
					(boxID, boxLayoutID, boxPositionID, showOrder)
			VALUES		(".$boxID.", ".$this->boxLayoutID.", ".$boxPositionID.", ".$showOrder.")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Updates the position of a box directly.
	 * 
	 * @param	integer		$boxPositionID
	 * @param	integer		$boxID
	 * @param	integer		$showOrder
	 */
	public function updateBoxShowOrder($boxPositionID, $boxID, $showOrder) {
		$sql = "UPDATE	wcf".WCF_N."_box_to_layout
			SET	showOrder = ".$showOrder."
			WHERE 	boxLayoutID = ".$this->boxLayoutID."
				AND boxPositionID = ".$boxPositionID."
				AND boxID = ".$boxID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Removes the box with the given box id from this layout.
	 * 
	 * @param	integer		$boxID
	 * @param	integer		$boxPositionID
	 */
	public function removeBox($boxID, $boxPositionID) {
		$sql = "DELETE FROM	wcf".WCF_N."_box_to_layout
			WHERE		boxLayoutID = ".$this->boxLayoutID."
					AND boxPositionID = ".$boxPositionID."
					AND boxID = ".$boxID;
		WCF::getDB()->sendQuery($sql);	
	}
}
?>