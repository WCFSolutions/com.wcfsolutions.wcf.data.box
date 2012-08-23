<?php
// wcf imports
require_once(WCF_DIR.'lib/data/box/Box.class.php');
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Handles status save action to close/open boxes.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	system.event.listener
 * @category	Community Framework
 */
class StatusSaveActionBoxListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (preg_match('/^box(\d+)_([\w-\.]+)$/', $eventObj->name, $match)) {
			$box = new Box($match[1]);
			$box->close($match[2], ($eventObj->status ? -1 : 1));
			exit;
		}
	}
}
?>