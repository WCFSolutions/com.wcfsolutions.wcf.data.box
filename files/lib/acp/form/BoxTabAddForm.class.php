<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/form/DynamicOptionListForm.class.php');
require_once(WCF_DIR.'lib/data/box/BoxEditor.class.php');
require_once(WCF_DIR.'lib/data/box/tab/BoxTabEditor.class.php');
require_once(WCF_DIR.'lib/data/ckeditor/CKEditor.class.php');

/**
 * Shows the box tab add form.
 *
 * @author	Sebastian Oettl
 * @copyright	2009-2012 WCF Solutions <http://www.wcfsolutions.com/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.data.box
 * @subpackage	acp.form
 * @category	Community Framework
 */
class BoxTabAddForm extends DynamicOptionListForm {
	// system
	public $templateName = 'boxTabAdd';
	public $neededPermissions = 'admin.box.canAddBoxTab';
	public $menuItemName = 'wcf.acp.menu.link.content.box.tab.add';

	/**
	 * list of options
	 *
	 * @var	array
	 */
	public $options = array();

	/**
	 * box tab type id
	 *
	 * @var	integer
	 */
	public $boxTabTypeID = 0;

	/**
	 * box tab type data
	 *
	 * @var	array
	 */
	public $boxTabType = array();

	/**
	 * list of available box tab types
	 *
	 * @var	array<BoxTabType>
	 */
	public $boxTabTypes = array();

	/**
	 * box tab editor object
	 *
	 * @var	BoxTabEditor
	 */
	public $boxTab = null;

	/**
	 * ckeditor object
	 *
	 * @var	CKEditor
	 */
	public $ckeditor = null;

	// parameters
	public $boxID = 0;
	public $boxTabName = '';
	public $showOrder = 0;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// get box type
		if (isset($_REQUEST['boxTabTypeID'])) {
			$this->boxTabTypeID = intval($_REQUEST['boxTabTypeID']);
			$sql = "SELECT	*
				FROM	wcf".WCF_N."_box_tab_type
				WHERE	boxTabTypeID = ".$this->boxTabTypeID."
					AND packageID IN (
						SELECT	dependency
						FROM	wcf".WCF_N."_package_dependency
						WHERE	packageID = ".PACKAGE_ID."
					)";
			$this->boxTabType = WCF::getDB()->getFirstRow($sql);
		}
		else {
			$sql = "SELECT		*
				FROM 		wcf".WCF_N."_box_tab_type
				WHERE		packageID IN (
							SELECT	dependency
							FROM	wcf".WCF_N."_package_dependency
							WHERE	packageID = ".PACKAGE_ID."
						)
				ORDER BY	boxTabTypeID ASC";
			$this->boxTabType = WCF::getDB()->getFirstRow($sql);
			if (isset($this->boxTabType['boxTabTypeID'])) $this->boxTabTypeID = $this->boxTabType['boxTabTypeID'];
		}
		if (!$this->boxTabType['boxTabTypeID']) {
			throw new IllegalLinkException();
		}

		// init ckeditor
		if ($this->boxTabType['boxTabType'] == 'content') {
			$this->ckeditor = new CKEditor('text');
			$this->ckeditor->setConfigOptions(array(
				'baseHref' => "'".$this->ckeditor->encodeJS('../')."'",
				'height' => "'300px'"
			));
		}

		// get box id
		if (isset($_REQUEST['boxID'])) $this->boxID = intval($_REQUEST['boxID']);
	}

	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if (isset($_POST['boxTabName'])) $this->boxTabName = StringUtil::trim($_POST['boxTabName']);
		if (isset($_POST['showOrder'])) $this->showOrder = intval($_POST['showOrder']);
	}

	/**
	 * @see Form::validate()
	 */
	public function validate() {
		// box id
		try {
			$box = new Box($this->boxID);
		}
		catch (IllegalLinkException $e) {
			$this->errorType['boxID'] = 'empty';
		}

		// box tab name
		try {
			if (empty($this->boxTabName)) {
				throw new UserInputException('boxTabName');
			}
		}
		catch (UserInputException $e) {
			$this->errorType[$e->getField()] = $e->getType();
		}

		// validate dynamic options
		parent::validate();
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();

		// create box tab
		$this->box = BoxTabEditor::create($this->boxID, $this->boxTabName, $this->boxTabType['boxTabType'], $this->activeOptions, $this->showOrder, WCF::getLanguage()->getLanguageID());
		BoxTabEditor::clearCache();
		BoxEditor::clearCache();

		// reset box cache
		BoxTab::resetBoxTabCacheByBoxTabType($this->boxTabType['boxTabType']);
		$this->saved();

		// show empty add form
		WCF::getTPL()->assign('success', true);

		// reset values
		$this->boxTabName = '';
		$this->showOrder = 0;

		foreach ($this->activeOptions as $key => $option) {
			unset($this->activeOptions[$key]['optionValue']);
		}
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		$this->options = $this->getOptionTree();
		if (!count($_POST)) {
			if (isset($this->options[0]['categoryName'])) {
				$this->activeTabMenuItem = $this->options[0]['categoryName'];
			}
		}

		// get box tab types
		$sql = "SELECT		box_tab_type.*, package.packageDir
			FROM		wcf".WCF_N."_package_dependency package_dependency,
					wcf".WCF_N."_box_tab_type box_tab_type
			LEFT JOIN	wcf".WCF_N."_package package
			ON		(package.packageID = box_tab_type.packageID)
			WHERE 		box_tab_type.packageID = package_dependency.dependency
					AND package_dependency.packageID = ".PACKAGE_ID."
			ORDER BY	package_dependency.dependency";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->boxTabTypes[$row['boxTabTypeID']] = WCF::getLanguage()->get('wcf.acp.box.tab.type.'.$row['boxTabType']);
		}
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'action' => 'add',
			'boxTabTypes' => $this->boxTabTypes,
			'boxTabTypeID' => $this->boxTabTypeID,
			'boxTabType' => $this->boxTabType,
			'ckeditor' => $this->ckeditor,
			'boxID' => $this->boxID,
			'boxTabName' => $this->boxTabName,
			'showOrder' => $this->showOrder,
			'options' => $this->options,
			'boxes' => Box::getBoxes()
		));
	}

	/**
	 * @see Form::show()
	 */
	public function show() {
		// set active menu item
		WCFACP::getMenu()->setActiveMenuItem($this->menuItemName);

		// check master password
		WCFACP::checkMasterPassword();

		// get user options and categories from cache
		$this->readCache();

		// show form
		parent::show();
	}

	/**
	 * Returns the tree of options.
	 *
	 * @param	string		$parentCategoryName
	 * @param	integer		$level
	 * @return	array
	 */
	protected function getOptionTree($parentCategoryName = '', $level = 0) {
		$options = array();

		if (isset($this->cachedCategoryStructure[$parentCategoryName])) {
			// get super categories
			foreach ($this->cachedCategoryStructure[$parentCategoryName] as $superCategoryName) {
				$superCategory = $this->cachedCategories[$superCategoryName];

				if ($this->checkCategory($superCategory)) {
					if ($level <= 1) {
						$superCategory['categories'] = $this->getOptionTree($superCategoryName, $level + 1);
					}
					if ($level > 1 || count($superCategory['categories']) == 0) {
						$superCategory['options'] = $this->getCategoryOptions($superCategoryName);
					}
					else {
						$superCategory['options'] = $this->getCategoryOptions($superCategoryName, false);
					}

					if ((isset($superCategory['categories']) && count($superCategory['categories']) > 0) || (isset($superCategory['options']) && count($superCategory['options']) > 0)) {
						$options[] = $superCategory;
					}
				}
			}
		}

		return $options;
	}

	/**
	 * @see DynamicOptionListForm::readCache()
	 */
	protected function readCache() {
		// get cache contents
		WCF::getCache()->addResource('boxtaboption-'.PACKAGE_ID, WCF_DIR.'cache/cache.boxtaboption-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderBoxTabOption.class.php');
		$this->cachedCategories = WCF::getCache()->get('boxtaboption-'.PACKAGE_ID, 'categories');
		$cachedOptions = WCF::getCache()->get('boxtaboption-'.PACKAGE_ID, 'options');
		$this->cachedOptions = (isset($cachedOptions[$this->boxTabType['boxTabType']]) ? $cachedOptions[$this->boxTabType['boxTabType']] : array());
		$this->cachedCategoryStructure = WCF::getCache()->get('boxtaboption-'.PACKAGE_ID, 'categoryStructure');
		$cachedOptionToCategories = WCF::getCache()->get('boxtaboption-'.PACKAGE_ID, 'optionToCategories');
		$this->cachedOptionToCategories = (isset($cachedOptionToCategories[$this->boxTabType['boxTabType']]) ? $cachedOptionToCategories[$this->boxTabType['boxTabType']] : array());

		// get active options
		$this->loadActiveOptions($this->activeCategory);
	}
}
?>