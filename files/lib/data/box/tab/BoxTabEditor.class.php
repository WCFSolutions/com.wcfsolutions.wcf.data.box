<?php
// wcf imports
require_once(WCF_DIR.'lib/data/box/tab/BoxTab.class.php');
require_once(WCF_DIR.'lib/system/language/LanguageEditor.class.php');

/**
 * Provides functions to manage box tabs.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	data.box.tab
 * @category	Community Framework
 */
class BoxTabEditor extends BoxTab {
	/**
	 * Creates a new BoxTabEditor object.
	 * 
	 * @param	integer		$boxTabID
	 * @param 	array<mixed>	$row
	 * @param	Box		$cacheObject
	 * @param	boolean		$useCache
	 */
	public function __construct($boxTabID, $row = null, $cacheObject = null, $useCache = true) {
		if ($useCache) parent::__construct($boxTabID, $row, $cacheObject);
		else {
			$sql = "SELECT	*
				FROM	wcf".WCF_N."_box_tab
				WHERE	boxTabID = ".$boxTabID;
			$row = WCF::getDB()->getFirstRow($sql);
			parent::__construct(null, $row);
		}
	}
	
	/**
	 * Updates this box tab.
	 * 
	 * @param	integer		$boxID
	 * @param	string		$boxTabName
	 * @param	array		$boxTabOptions
	 * @param	integer		$showOrder
	 * @param	integer		$languageID
	 * @param	integer		$packageID
	 */
	public function update($boxID, $boxTabName, $boxTabOptions = array(), $showOrder = 0, $languageID = 0, $packageID = PACKAGE_ID) {
		// update show order
		if ($boxID == $this->boxID) {
			if ($this->showOrder != $showOrder) {
				if ($showOrder < $this->showOrder) {
					$sql = "UPDATE	wcf".WCF_N."_box_tab
						SET 	showOrder = showOrder + 1
						WHERE 	boxID = ".$boxID."
							AND showOrder >= ".$showOrder."
							AND showOrder < ".$this->showOrder;
					WCF::getDB()->sendQuery($sql);
				}
				else if ($showOrder > $this->showOrder) {
					$sql = "UPDATE	wcf".WCF_N."_box_tab
						SET	showOrder = showOrder - 1
						WHERE	boxID = ".$boxID."
							AND showOrder <= ".$showOrder."
							AND showOrder > ".$this->showOrder;
					WCF::getDB()->sendQuery($sql);
				}
			}
		}
		else {
			$sql = "UPDATE	wcf".WCF_N."_box_tab
				SET	showOrder = showOrder - 1
				WHERE	boxID = ".$boxID."
					AND showOrder >= ".$this->showOrder;
			WCF::getDB()->sendQuery($sql);
			
			$sql = "UPDATE	wcf".WCF_N."_box_tab
				SET	showOrder = showOrder + 1
				WHERE	boxID = ".$boxID."
					AND showOrder >= ".$showOrder;
			WCF::getDB()->sendQuery($sql);				
		}
		
		// update box tab
		$sql = "UPDATE	wcf".WCF_N."_box_tab
			SET	boxID = ".$boxID.",
				".($languageID == 0 ? "boxTab = '".escapeString($boxTabName)."'," : '')."
				showOrder = ".$showOrder."
			WHERE	boxTabID = ".$this->boxTabID;
		WCF::getDB()->sendQuery($sql);
		
		// update language items
		if ($languageID != 0) {
			// save language variables
			$language = new LanguageEditor($languageID);
			$language->updateItems(array('wcf.box.tab.'.$this->boxTab => $boxTabName), 0, $packageID, array('wcf.box.tab.'.$this->boxTab => 1));
			
			// save content
			if ($this->boxTabType == 'content') {
				if (isset($boxTabOptions['text']['optionValue'])) {
					$content = $boxTabOptions['text']['optionValue'];
					$language->updateItems(array('wcf.box.tab.'.$this->boxTab.'.text' => $content), 0, $packageID, array('wcf.box.tab.'.$this->boxTab.'.text' => 1));
					$boxTabOptions['text']['optionValue'] = 'wcf.box.tab.'.$this->boxTab.'.text';
				}
			}
			
			// delete language files
			LanguageEditor::deleteLanguageFiles($languageID, 'wcf.box.tab', $packageID);
		}
		
		// insert box tab options
		self::insertBoxTabOptions($this->boxTabID, $this->boxTabType, $boxTabOptions, true);
		
		// reset content
		if (isset($content)) {
			$boxTabOptions['text']['optionValue'] = $content;
		}
	}
	
	/**
	 * Updates the show order of this box tab.
	 * 
	 * @param	integer		$showOrder
	 */
	public function updateShowOrder($showOrder) {
		$sql = "UPDATE	wcf".WCF_N."_box_tab
			SET 	showOrder = ".$showOrder."
			WHERE 	boxTabID = ".$this->boxTabID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Deletes this box tab.
	 */
	public function delete() {
		self::deleteAll($this->boxTabID);
	}
	
	/**
	 * Creates a new box tab.
	 * 
	 * @param	integer		$boxID
	 * @param	string		$boxTabName
	 * @param	string		$boxTabType
	 * @param	array		$boxTabOptions
	 * @param	integer		$showOrder
	 * @param	integer		$languageID
	 * @param	integer		$packageID
	 * @return	BoxTabEditor
	 */
	public static function create($boxID, $boxTabName, $boxTabType, $boxTabOptions = array(), $showOrder = 0, $languageID = 0, $packageID = PACKAGE_ID) {	
		// get show order
		if ($showOrder == 0) {
			// get next number in row
			$sql = "SELECT	MAX(showOrder) AS showOrder
				FROM	wcf".WCF_N."_box_tab
				WHERE	boxID = ".$boxID;
			$row = WCF::getDB()->getFirstRow($sql);
			if (!empty($row)) $showOrder = intval($row['showOrder']) + 1;
			else $showOrder = 1;
		}
		else {
			$sql = "UPDATE	wcf".WCF_N."_box_tab
				SET 	showOrder = showOrder + 1
				WHERE 	showOrder >= ".$showOrder."
					AND boxID = ".$boxID;
			WCF::getDB()->sendQuery($sql);
		}
		
		// get box tab name
		$boxTab = '';
		if ($languageID == 0) $boxTab = $boxTabName;
		
		// save box
		$sql = "INSERT INTO	wcf".WCF_N."_box_tab
					(packageID, boxID, boxTab, boxTabType, showOrder)
			VALUES		(".$packageID.", ".$boxID.", '".escapeString($boxTab)."', '".escapeString($boxTabType)."', ".$showOrder.")";
		WCF::getDB()->sendQuery($sql);
		
		// get box tab id
		$boxTabID = WCF::getDB()->getInsertID("wcf".WCF_N."_box_tab", 'boxTabID');
		
		// update language items
		if ($languageID != 0) {
			// set name
			$boxTab = "boxTab".$boxTabID;
			$sql = "UPDATE	wcf".WCF_N."_box_tab
				SET	boxTab = '".escapeString($boxTab)."'
				WHERE 	boxTabID = ".$boxTabID;
			WCF::getDB()->sendQuery($sql);
			
			// save language variables
			$language = new LanguageEditor($languageID);
			$language->updateItems(array('wcf.box.tab.'.$boxTab => $boxTabName), 0, $packageID);
			
			// save content
			if ($boxTabType == 'content') {
				if (isset($boxTabOptions['text']['optionValue'])) {
					$content = $boxTabOptions['text']['optionValue'];
					$language->updateItems(array('wcf.box.tab.'.$boxTab.'.text' => $content), 0, $packageID);
					$boxTabOptions['text']['optionValue'] = 'wcf.box.tab.'.$boxTab.'.text';
				}
			}
			
			// delete language files
			LanguageEditor::deleteLanguageFiles($languageID, 'wcf.box.tab', $packageID);
		}
		
		// insert box tab options
		self::insertBoxTabOptions($boxTabID, $boxTabType, $boxTabOptions);
		
		// reset content
		if (isset($content)) {
			$boxTabOptions['text']['optionValue'] = $content;
		}
		
		// return new box tab
		return new BoxTabEditor($boxTabID, null, null, false);
	}
	
	/**
	 * Inserts the box tab options. 
	 * 
	 * @param 	integer		$boxTabID
	 * @param	string		$boxTabType
	 * @param 	array 		$boxTabOptions
	 * @param 	boolean		$update
	 */
	protected static function insertBoxTabOptions($boxTabID, $boxTabType, $boxTabOptions = array(), $update = false) {
		// get default values from options.
		$defaultValues = array();
		if (!$update) {
			$sql = "SELECT	optionID, defaultValue
				FROM	wcf".WCF_N."_box_tab_option
				WHERE	boxTabType = '".escapeString($boxTabType)."'";
			$result = WCF::getDB()->sendQuery($sql);
			
			while ($row = WCF::getDB()->fetchArray($result)) {
				$defaultValues[$row['optionID']] = $row['defaultValue'];	
			}
		}
		
		// build the sql strings. 
		$inserts = '';
		foreach ($boxTabOptions as $option) {
			if (!empty($inserts)) $inserts .= ',';
			$inserts .= "(".$boxTabID.", ".$option['optionID'].", '".escapeString($option['optionValue'])."')";
			
			// the value of this option was send via "activeOptions".
			unset($defaultValues[$option['optionID']]);
		}
		
		// add default values from inactive options.
		foreach ($defaultValues as $optionID => $optionValue) {
			if (!empty($inserts)) $inserts .= ',';
			$inserts .= "(".$boxTabID.", ".$optionID.", '".escapeString($optionValue)."')";
		}
		
		if (!empty($inserts)) {
			$sql = "REPLACE INTO	wcf".WCF_N."_box_tab_option_value
						(boxTabID, optionID, optionValue)
				VALUES 		".$inserts;
			WCF::getDB()->sendQuery($sql);
		}
	}
	
	/**
	 * Deletes all box tabs with the given box tab ids.
	 * 
	 * @param	string		$boxTabIDs
	 */
	public static function deleteAll($boxTabIDs) {
		if (empty($boxTabIDs)) return;
		
		// delete box tab option values
		$sql = "DELETE FROM	wcf".WCF_N."_box_tab_option_value
			WHERE		boxTabID IN (".$boxTabIDs.")";
		WCF::getDB()->sendQuery($sql);
		
		// delete box tab
		$sql = "DELETE FROM	wcf".WCF_N."_box_tab
			WHERE		boxTabID IN (".$boxTabIDs.")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Clears the box tab cache.
	 */
	public static function clearCache() {
		WCF::getCache()->clear(WCF_DIR.'cache', 'cache.boxTab-*.php');
	}
}
?>