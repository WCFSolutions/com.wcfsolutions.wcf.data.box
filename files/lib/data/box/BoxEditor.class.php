<?php
// wcf imports
require_once(WCF_DIR.'lib/data/box/Box.class.php');
require_once(WCF_DIR.'lib/system/language/LanguageEditor.class.php');

/**
 * Provides functions to manage boxes.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	data.box
 * @category	Community Framework
 */
class BoxEditor extends Box {
	/**
	 * Creates a new BoxEditor object.
	 *
	 * @param	integer		$boxID
	 * @param 	array<mixed>	$row
	 * @param	Box		$cacheObject
	 * @param	boolean		$useCache
	 */
	public function __construct($boxID, $row = null, $cacheObject = null, $useCache = true) {
		if ($useCache) parent::__construct($boxID, $row, $cacheObject);
		else {
			$sql = "SELECT	*
				FROM	wcf".WCF_N."_box
				WHERE	boxID = ".$boxID;
			$row = WCF::getDB()->getFirstRow($sql);
			parent::__construct(null, $row);
		}
	}
	
	/**
	 * Updates this box.
	 * 
	 * @param	string		$boxName
	 * @param	string		$description
	 * @param	integer		$enableTitle
	 * @param	integer		$isClosable
	 * @param	integer		$languageID
	 * @param	integer		$packageID
	 */
	public function update($boxName, $description, $enableTitle, $isClosable, $languageID = 0, $packageID = PACKAGE_ID) {
		// update box
		$sql = "UPDATE	wcf".WCF_N."_box
			SET	".($languageID == 0 ? "box = '".escapeString($boxName)."'," : '')."
				enableTitle = ".$enableTitle.",
				isClosable = ".$isClosable."
			WHERE	boxID = ".$this->boxID;
		WCF::getDB()->sendQuery($sql);
		
		// update language items
		if ($languageID != 0) {
			// save language variables
			$language = new LanguageEditor($languageID);
			$language->updateItems(array('wcf.box.'.$this->box => $boxName, 'wcf.box.'.$this->box.'.description' => $description), 0, $packageID, array('wcf.box.'.$this->box => 1, 'wcf.box.'.$this->box.'.description' => 1));
			LanguageEditor::deleteLanguageFiles($languageID, 'wcf.box', $packageID);
		}
	}
	
	/**
	 * Deletes this box.
	 */
	public function delete() {
		// get all box tab ids
		$boxTabIDs = '';
		$sql = "SELECT	boxTabID
			FROM	wcf".WCF_N."_box_tab
			WHERE	boxID = ".$this->boxID;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($boxTabIDs)) $boxTabIDs .= ',';
			$boxTabIDs .= $row['boxTabID'];
		}
		if (!empty($boxTabIDs)) {
			// delete box tabs
			require_once(WCF_DIR.'lib/data/box/tab/BoxTabEditor.class.php');
			BoxTabEditor::deleteAll($boxTabIDs);
		}
			
		// delete box to layout
		$sql = "DELETE FROM	wcf".WCF_N."_box_to_layout
			WHERE		boxID = ".$this->boxID;
		WCF::getDB()->sendQuery($sql);
		
		// delete box closed to user
		$sql = "DELETE FROM	wcf".WCF_N."_box_closed_to_user
			WHERE		boxID = ".$this->boxID;
		WCF::getDB()->sendQuery($sql);
		
		// delete box
		$sql = "DELETE FROM	wcf".WCF_N."_box
			WHERE		boxID = ".$this->boxID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Creates a new box.
	 * 
	 * @param	string		$boxName
	 * @param	string		$description
	 * @param	integer		$enableTitle
	 * @param	integer		$isClosable
	 * @param	integer		$languageID
	 * @param	integer		$packageID
	 * @return	BoxEditor
	 */
	public static function create($boxName, $description, $enableTitle, $isClosable, $languageID = 0, $packageID = PACKAGE_ID) {		
		// get box name
		$box = '';
		if ($languageID == 0) $box = $boxName;
		
		// save box
		$sql = "INSERT INTO	wcf".WCF_N."_box
					(packageID, box, enableTitle, isClosable)
			VALUES		(".$packageID.", '".escapeString($box)."', ".$enableTitle.", ".$isClosable.")";
		WCF::getDB()->sendQuery($sql);
		
		// get box id
		$boxID = WCF::getDB()->getInsertID("wcf".WCF_N."_box", 'boxID');
		
		// update language items
		if ($languageID != 0) {
			// set name
			$box = "box".$boxID;
			$sql = "UPDATE	wcf".WCF_N."_box
				SET	box = '".escapeString($box)."'
				WHERE 	boxID = ".$boxID;
			WCF::getDB()->sendQuery($sql);
			
			// save language variables
			$language = new LanguageEditor($languageID);
			$language->updateItems(array('wcf.box.'.$box => $boxName, 'wcf.box.'.$box.'.description' => $description), 0, $packageID);
			LanguageEditor::deleteLanguageFiles($languageID, 'wcf.box', $packageID);
		}
		
		// return new box
		return new BoxEditor($boxID, null, null, false);
	}
	
	/**
	 * Clears the box cache.
	 */
	public static function clearCache() {
		WCF::getCache()->clear(WCF_DIR.'cache', 'cache.box-*.php');
		WCF::getCache()->clear(WCF_DIR.'cache', 'cache.boxLayout-*.php');
	}
}
?>