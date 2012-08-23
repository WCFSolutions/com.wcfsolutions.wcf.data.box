<?php
// wcf imports
require_once(WCF_DIR.'lib/data/box/tab/type/AbstractBoxTabType.class.php');

/**
 * Represents a html box tab type.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	data.box.tab.type
 * @category	Community Framework
 */
class HTMLBoxTabType extends AbstractBoxTabType {
	/**
	 * @see	BoxTabType::getTemplateName()
	 */
	public function getTemplateName() {
		return 'htmlBoxTabType';
	}
}
?>