<?php
// wcf imports
require_once(WCF_DIR.'lib/data/box/tab/type/AbstractBoxTabType.class.php');

/**
 * Represents a list box tab type.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	data.box.tab.type
 * @category	Community Framework
 */
class ListBoxTabType extends AbstractBoxTabType {
	/**
	 * list of data
	 * 
	 * @var	array
	 */
	public $data = array();
	
	/**
	 * @see	BoxTabType::cache()
	 */
	public function cache(BoxTab $boxTab) {
		if (!isset($this->data[$boxTab->boxTabID])) {
			$this->data[$boxTab->boxTabID] = array(
				'listItems' => ArrayUtil::trim(explode("\n", StringUtil::trim(StringUtil::unifyNewlines($boxTab->listItems)))),
				'listTag' => (($boxTab->listStyleType == 'none' || $boxTab->listStyleType == 'circle' || $boxTab->listStyleType == 'square' || $boxTab->listStyleType == 'disc') ? 'ul' : 'ol')
			);
		}
	}
	
	/**
	 * @see	BoxTabType::getData()
	 */
	public function getData(BoxTab $boxTab) {
		return $this->data[$boxTab->boxTabID];
	}
	
	/**
	 * @see	BoxTabType::isAccessible()
	 */
	public function isAccessible(BoxTab $boxTab) {
		return count($this->data[$boxTab->boxTabID]['listItems']);
	}
	
	/**
	 * @see	BoxTabType::getTemplateName()
	 */
	public function getTemplateName() {
		return 'listBoxTabType';
	}
}
?>