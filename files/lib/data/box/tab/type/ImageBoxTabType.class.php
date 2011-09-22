<?php
// wcf imports
require_once(WCF_DIR.'lib/data/box/tab/type/AbstractBoxTabType.class.php');

/**
 * Represents an image box tab type.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	data.box.tab.type
 * @category	Community Framework
 */
class ImageBoxTabType extends AbstractBoxTabType {
	/**
	 * @see	BoxTabType::getTemplateName()
	 */
	public function getTemplateName() {
		return 'imageBoxTabType';
	}
}
?>