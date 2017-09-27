<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

/***************
 * Define TypoScript as content rendering template
 */
$GLOBALS['TYPO3_CONF_VARS']['FE']['contentRenderingTemplates'][] = 'indexed_search_autocomplete/Configuration/TypoScript/';


/***************
 * Make the extension configuration accessible
 */
$myConf = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY];
if (!is_array($myConf)) {
    $myConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);
}

// Add org. Jquery 3.2 in the Frontend
if (!$myConf['disableJquerySource']) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript($_EXTKEY, 'setup', 'page.includeJSFooterlibs.JquerySource = {$plugin.tx_indexedsearch_autocomplete.jqueryFile}');
}


\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'ID.' . $_EXTKEY,
	'Search',
	array(
		'Search' => 'search',
		
	),
	// non-cacheable actions
	array(
		'Entry' => 'search',
		
	)
);