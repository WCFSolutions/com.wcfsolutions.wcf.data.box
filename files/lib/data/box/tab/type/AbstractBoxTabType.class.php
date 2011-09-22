<?php
// wcf imports
require_once(WCF_DIR.'lib/data/box/tab/BoxTab.class.php');
require_once(WCF_DIR.'lib/data/box/tab/type/BoxTabType.class.php');

/**
 * Provides default implementations for box tab types.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	data.box.tab.type
 * @category	Community Framework
 */
class AbstractBoxTabType implements BoxTabType {
	/**
	 * @see	BoxTabType::cache()
	 */
	public function cache(BoxTab $boxTab) {}
	
	/**
	 * @see	BoxTabType::getData()
	 */
	public function getData(BoxTab $boxTab) {
		return array();
	}
	
	/**
	 * @see	BoxTabType::resetCache()
	 */	
	public function resetCache(BoxTab $boxTab) {}
	
	/**
	 * @see	BoxTabType::isAccessible()
	 */
	public function isAccessible(BoxTab $boxTab) {
		return true;
	}
	
	/**
	 * @see	BoxTabType::getTemplateName()
	 */	
	public function getTemplateName() {
		return '';
	}
}
?>