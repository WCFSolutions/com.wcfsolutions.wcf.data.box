<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/package/plugin/AbstractOptionPackageInstallationPlugin.class.php');

/**
 * This PIP installs, updates or deletes box tab options.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.package.plugin
 * @category	Community Framework
 */
class BoxTabOptionsPackageInstallationPlugin extends AbstractOptionPackageInstallationPlugin {
	public $tagName = 'boxtaboptions';
	public $tableName = 'box_tab_option';
	public static $reservedTags = array('boxtabtype', 'name', 'optiontype', 'defaultvalue', 'validationpattern', 'enableoptions', 'showorder', 'selectoptions', 'categoryname', 'permissions', 'options', 'attrs', 'cdata');

	/**
	 * @see	AbstractOptionPackageInstallationPlugin::saveOption()
	 */
	protected function saveOption($option, $categoryName, $existingOptionID = 0) {
		// default values
		$boxTabType = $optionName = $optionType = $defaultValue = $validationPattern = $selectOptions = $enableOptions = $permissions = $options = '';
		$showOrder = null;

		// make xml tags-names (keys in array) to lower case
		$this->keysToLowerCase($option);

		// get values
		if (isset($option['boxtabtype'])) $boxTabType = $option['boxtabtype'];
		if (isset($option['name'])) $optionName = $option['name'];
		if (isset($option['optiontype'])) $optionType = $option['optiontype'];
		if (isset($option['defaultvalue'])) $defaultValue = WCF::getLanguage()->get($option['defaultvalue']);
		if (isset($option['validationpattern'])) $validationPattern = $option['validationpattern'];
		if (isset($option['enableoptions'])) $enableOptions = $option['enableoptions'];
		if (isset($option['showorder'])) $showOrder = intval($option['showorder']);
		$showOrder = $this->__getShowOrder($showOrder, $categoryName, $boxTabType);
		if (isset($option['selectoptions'])) $selectOptions = $option['selectoptions'];
		if (isset($option['permissions'])) $permissions = $option['permissions'];
		if (isset($option['options'])) $options = $option['options'];

		// check if optionType exists
		$classFile = WCF_DIR.'lib/acp/option/OptionType'.ucfirst($optionType).'.class.php';
		if (!@file_exists($classFile)) {
			throw new SystemException('Unable to find file '.$classFile, 11002);
		}

		// collect additional tags and their values
		$additionalData = array();
		foreach ($option as $tag => $value) {
			if (!in_array($tag, self::$reservedTags)) $additionalData[$tag] = $value;
		}

		// insert or update option
		$sql = "SELECT	optionID
			FROM 	wcf".WCF_N."_box_tab_option
			WHERE 	optionName = '".escapeString($optionName)."'
				AND boxTabType = '".escapeString($boxTabType)."'
				AND packageID = ".$this->installation->getPackageID();
		$result = WCF::getDB()->getFirstRow($sql);
		$sql = "INSERT INTO 			wcf".WCF_N."_".$this->tableName."
							(packageID, boxTabType,
							optionName, categoryName,
							optionType, defaultValue,
							validationPattern, selectOptions,
							showOrder, enableOptions,
							permissions, options,
							additionalData)
			VALUES				(".$this->installation->getPackageID().",
							'".escapeString($boxTabType)."',
							'".escapeString($optionName)."',
							'".escapeString($categoryName)."',
							'".escapeString($optionType)."',
							'".escapeString($defaultValue)."',
							'".escapeString($validationPattern)."',
							'".escapeString($selectOptions)."',
							".intval($showOrder).",
							'".escapeString($enableOptions)."',
							'".escapeString($permissions)."',
							'".escapeString($options)."',
							'".escapeString(serialize($additionalData))."')
			ON DUPLICATE KEY UPDATE		categoryName = VALUES(categoryName),
							optionType = VALUES(optionType),
							validationPattern = VALUES(validationPattern),
							selectoptions = VALUES(selectOptions),
							showOrder = VALUES(showOrder),
							enableOptions = VALUES(enableOptions),
							permissions = VALUES(permissions),
							options = VALUES(options),
							additionalData = VALUES(additionalData)";
		WCF::getDB()->sendQuery($sql);
		if (isset($result['optionID']) && $this->installation->getAction() == 'update') {
			$optionID = $result['optionID'];
		}
		else {
			$optionID = WCF::getDB()->getInsertID();
		}

		// insert new option and default value to each box
		// get all boxTabIDs
		// don't change values of existing options
		$sql = "SELECT	boxTabID
			FROM	wcf".WCF_N."_box_tab
			WHERE	boxTabType = '".escapeString($boxTabType)."'";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$sql = "INSERT IGNORE INTO	wcf".WCF_N."_box_tab_option_value
							(boxTabID, optionID, optionValue)
				VALUES			(".$row['boxTabID'].",
							 ".$optionID.",
							'".escapeString($defaultValue)."')";
			WCF::getDB()->sendQuery($sql);
		}
	}

	/**
	 * Returns the show order value.
	 *
	 * @param	integer		$showOrder
	 * @param	string		$categoryName
	 * @param	string		$boxTabType
	 * @return	integer
	 */
	protected function __getShowOrder($showOrder, $categoryName, $boxTabType) {
		if ($showOrder === null) {
	        	 // get greatest showOrder value
	          	$sql = "SELECT	MAX(showOrder) AS showOrder
			  	FROM	wcf".WCF_N."_".$this->tableName."
				WHERE	categoryName = '".escapeString($categoryName)."'
					AND boxTabType = '".escapeString($boxTabType)."'";
			$maxShowOrder = WCF::getDB()->getFirstRow($sql);
			if (is_array($maxShowOrder) && isset($maxShowOrder['showOrder'])) {
				return $maxShowOrder['showOrder'] + 1;
			}
			else {
				return 1;
			}
	       	}
	       	else {
			// increase all showOrder values which are >= $showOrder
			$sql = "UPDATE	wcf".WCF_N."_".$this->tableName."
				SET	showOrder = showOrder+1
				WHERE	showOrder >= ".$showOrder."
					AND categoryName = '".escapeString($categoryName)."'
					AND boxTabType = '".escapeString($boxTabType)."'";
			WCF::getDB()->sendQuery($sql);
			// return the wanted showOrder level
			return $showOrder;
       		}
	}
}
?>