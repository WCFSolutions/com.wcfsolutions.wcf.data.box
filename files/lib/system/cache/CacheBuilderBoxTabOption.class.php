<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches the box tab options and box tab option categories.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	system.cache
 * @category	Community Framework
 */
class CacheBuilderBoxTabOption implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		list($cache, $packageID) = explode('-', $cacheResource['cache']);
		$data = array(
			'categories' => array(),
			'options' => array(),
			'categoryStructure' => array(),
			'optionToCategories' => array()
		);

		// option categories
		// get all option categories and filter categories with low priority
		$sql = "SELECT		categoryName, categoryID
			FROM		wcf".WCF_N."_box_tab_option_category option_category,
					wcf".WCF_N."_package_dependency package_dependency
			WHERE 		option_category.packageID = package_dependency.dependency
					AND package_dependency.packageID = ".$packageID."
			ORDER BY	package_dependency.priority";
		$result = WCF::getDB()->sendQuery($sql);
		$optionCategories = array();
		while ($row = WCF::getDB()->fetchArray($result)) {
			$optionCategories[$row['categoryName']] = $row['categoryID'];
		}

		if (count($optionCategories) > 0) {
			// get needed option categories
			$sql = "SELECT		option_category.*, package.packageDir
				FROM		wcf".WCF_N."_box_tab_option_category option_category
				LEFT JOIN	wcf".WCF_N."_package package
				ON		(package.packageID = option_category.packageID)
				WHERE		categoryID IN (".implode(',', $optionCategories).")
				ORDER BY	showOrder";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$data['categories'][$row['categoryName']] = $row;
				if (!isset($data['categoryStructure'][$row['parentCategoryName']])) {
					$data['categoryStructure'][$row['parentCategoryName']] = array();
				}

				$data['categoryStructure'][$row['parentCategoryName']][] = $row['categoryName'];
			}
		}

		// options
		// get all options and filter options with low priority
		$sql = "SELECT		optionID
			FROM		wcf".WCF_N."_box_tab_option option_table,
					wcf".WCF_N."_package_dependency package_dependency
			WHERE 		option_table.packageID = package_dependency.dependency
					AND package_dependency.packageID = ".$packageID."
			ORDER BY	package_dependency.priority";
		$result = WCF::getDB()->sendQuery($sql);
		$options = array();
		while ($row = WCF::getDB()->fetchArray($result)) {
			$options[] = $row['optionID'];
		}

		if (count($options) > 0) {
			// get needed options
			$sql = "SELECT		*
				FROM		wcf".WCF_N."_box_tab_option
				WHERE		optionID IN (".implode(',', $options).")
				ORDER BY	showOrder";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				// unserialize additional data
				$row['additionalData'] = (empty($row['additionalData']) ? array() : @unserialize($row['additionalData']));

				if (!isset($data['options'][$row['boxTabType']])) {
					$data['options'][$row['boxTabType']] = array();
				}
				$data['options'][$row['boxTabType']][$row['optionName']] = $row;

				if (!isset($data['optionToCategories'][$row['boxTabType']])) {
					$data['optionToCategories'][$row['boxTabType']] = array();
				}
				if (!isset($data['optionToCategories'][$row['boxTabType']][$row['categoryName']])) {
					$data['optionToCategories'][$row['boxTabType']][$row['categoryName']] = array();
				}
				$data['optionToCategories'][$row['boxTabType']][$row['categoryName']][] = $row['optionName'];
			}
		}

		return $data;
	}
}
?>