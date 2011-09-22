<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/option/OptionType.class.php');

/**
 * OptionTypeSelect is an implementation of OptionType for a box layout select.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.option
 * @category	Community Framework
 */
class OptionTypeBoxlayouts implements OptionType {
	/**
	 * list of box layouts
	 * 
	 * @var	array
	 */
	protected $boxLayouts = null;
	
	/**
	 * @see OptionType::getFormElement()
	 */
	public function getFormElement(&$optionData) {
		if (!isset($optionData['optionValue'])) {
			if (isset($optionData['defaultValue'])) $optionData['optionValue'] = $optionData['defaultValue'];
			else $optionData['optionValue'] = false;
		}
		
		// read box layouts
		$this->readBoxLayouts();
		
		WCF::getTPL()->assign(array(
			'optionData' => $optionData,
			'options' => $this->boxLayouts
		));
		return WCF::getTPL()->fetch('optionTypeSelect');
	}
	
	/**
	 * @see OptionType::validate()
	 */
	public function validate($optionData, $newValue) {
		$this->readBoxLayouts();
		if (!isset($this->boxLayouts[$newValue])) {
			throw new UserInputException($optionData['optionName'], 'validationFailed');
		}
	}
	
	/**
	 * @see OptionType::getData()
	 */
	public function getData($optionData, $newValue) {
		return $newValue;
	}
	
	/**
	 * Gets the box layouts.
	 */
	protected function readBoxLayouts() {
		if ($this->boxLayouts === null) {
			require_once(WCF_DIR.'lib/data/box/layout/BoxLayout.class.php');
			$this->boxLayouts = array(0 => '');
			$boxLayouts = WCF::getCache()->get('boxLayout-'.PACKAGE_ID, 'layouts');
			foreach ($boxLayouts as $boxLayoutID => $boxLayout) {
				$this->boxLayouts[$boxLayoutID] = StringUtil::encodeHTML($boxLayout->title);
			}
		}
	}
}
?>