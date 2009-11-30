<?php

########################################################################
# Extension Manager/Repository config file for ext: "pt_gsasocket"
#
# Auto generated 27-11-2009 17:01
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'GSA Socket',
	'description' => 'GSA Socket provides access to the additional (non TYPO3) GSA database tables required by GSA Shop (pt_gsashop) and all extensions of the \'General Shop Applications\' (GSA) category.',
	'category' => 'General Shop Applications',
	'author' => 'Rainer Kuhn',
	'author_email' => 't3extensions@punkt.de',
	'shy' => '',
	'dependencies' => 'cms,pt_tools',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'punkt.de GmbH',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '1.0.0',
	'_md5_values_when_last_written' => 'a:20:{s:10:".cvsignore";s:4:"081c";s:8:".project";s:4:"f7e0";s:21:"ext_conf_template.txt";s:4:"11ea";s:12:"ext_icon.gif";s:4:"4546";s:14:"doc/DevDoc.txt";s:4:"b4f4";s:14:"doc/manual.sxw";s:4:"660b";s:19:"doc/wizard_form.dat";s:4:"32d4";s:20:"doc/wizard_form.html";s:4:"b814";s:42:"res/class.tx_ptgsasocket_gsaDbAccessor.php";s:4:"7d51";s:43:"res/class.tx_ptgsasocket_gsaDbConnector.php";s:4:"5372";s:48:"res/class.tx_ptgsasocket_paradoxDataAccessor.php";s:4:"8a08";s:38:"res/class.tx_ptgsasocket_staticLib.php";s:4:"3670";s:49:"res/class.tx_ptgsasocket_textfileDataAccessor.php";s:4:"62d6";s:17:"res/sql/.htaccess";s:4:"b59d";s:22:"res/sql/px_DTABUCH.sql";s:4:"ff1b";s:22:"res/sql/px_laender.sql";s:4:"4c61";s:44:".settings/com.zend.php.javabridge.core.prefs";s:4:"662d";s:51:".settings/org.eclipse.php.core.projectOptions.prefs";s:4:"dd41";s:17:".cache/.dataModel";s:4:"15bd";s:21:".cache/.wsdlDataModel";s:4:"3376";}',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'pt_tools' => '1.0.0-',
			'php' => '5.1.0-0.0.0',
			'typo3' => '4.0.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'pt_gsaminidb' => '1.0.0-',
		),
	),
	'suggests' => array(
	),
);

?>