DROP TABLE IF EXISTS wcf1_box_tab;
CREATE TABLE wcf1_box_tab (
	boxTabID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	packageID INT(10) NOT NULL DEFAULT 0,
	boxID INT(10) NOT NULL DEFAULT 0,
	boxTab VARCHAR(255) NOT NULL DEFAULT '',
	boxTabType VARCHAR(125) NOT NULL DEFAULT '',
	showOrder INT(10) NOT NULL DEFAULT 0,
	KEY (packageID),
	KEY (boxID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

@INSERT INTO	wcf1_box_tab
SELECT		boxID AS boxTabID, packageID, boxID, CONCAT('boxTab', boxID) AS boxTab, boxType AS boxTabType, 1 AS showOrder
FROM		wcf1_box;

ALTER TABLE wcf1_box DROP boxType;

DROP TABLE IF EXISTS wcf1_box_tab_type;
CREATE TABLE wcf1_box_tab_type (
	boxTabTypeID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	packageID INT(10) NOT NULL DEFAULT 0,
	boxTabType VARCHAR(125) NOT NULL DEFAULT '',
	classFile VARCHAR(255) NOT NULL,
	UNIQUE KEY (packageID, boxTabType),
	KEY (packageID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_box_tab_option_category;
CREATE TABLE wcf1_box_tab_option_category (
	categoryID INT(10) NOT NULL AUTO_INCREMENT,
	packageID INT(10) NOT NULL DEFAULT 0,
	categoryName VARCHAR(255) NOT NULL DEFAULT '',
	parentCategoryName VARCHAR(255) NOT NULL DEFAULT '',
	showOrder INT(10) NOT NULL DEFAULT 0,
	permissions TEXT,
	options TEXT,
	PRIMARY KEY (categoryID),
	UNIQUE KEY (categoryName, packageID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_box_tab_option;
CREATE TABLE wcf1_box_tab_option  (
	optionID INT(10) NOT NULL AUTO_INCREMENT,
	packageID INT(10) NOT NULL DEFAULT 0,
	boxTabType VARCHAR(125) NOT NULL DEFAULT '',
	optionName VARCHAR(200) NOT NULL DEFAULT '',
	categoryName VARCHAR(255) NOT NULL DEFAULT '',
	optionType VARCHAR(255) NOT NULL DEFAULT '',
	defaultValue MEDIUMTEXT,
	validationPattern TEXT,
	selectOptions MEDIUMTEXT,
	enableOptions MEDIUMTEXT,
	showOrder INT(10) NOT NULL DEFAULT 0,
	permissions TEXT,
	options TEXT,
	additionalData MEDIUMTEXT,
	PRIMARY KEY (optionID),
	UNIQUE KEY (boxTabType, optionName, packageID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_box_tab_option_value;
CREATE TABLE wcf1_box_tab_option_value  (
	boxTabID INT(10) NOT NULL DEFAULT 0,
	optionID INT(10) NOT NULL DEFAULT 0,
	optionValue MEDIUMTEXT,
	PRIMARY KEY (boxTabID, optionID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

@INSERT INTO	wcf1_box_tab_type
SELECT		boxTypeID AS boxTabTypeID, packageID, boxType AS boxTabType, classFile
FROM		wcf1_box_type;

DROP TABLE IF EXISTS wcf1_box_type;


@INSERT INTO	wcf1_box_tab_option_category
SELECT		*
FROM		wcf1_box_option_category;

DROP TABLE IF EXISTS wcf1_box_option_category;


@INSERT INTO	wcf1_box_tab_option
SELECT		optionID, packageID, boxType AS boxTabType, optionName, categoryName, optionType, defaultValue,
		validationPattern, selectOptions, enableOptions, showOrder, permissions, options, additionalData
FROM		wcf1_box_option;

DROP TABLE IF EXISTS wcf1_box_option;


@INSERT INTO	wcf1_box_tab_option_value
SELECT		boxID AS boxTabID, optionID, optionValue
FROM		wcf1_box_option_value;

DROP TABLE IF EXISTS wcf1_box_option_value;