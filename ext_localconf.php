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
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY])) {
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY] = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);
}

// Add org. Jquery 3.2 in the Frontend
if (!$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]['disableJquerySource']) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript($_EXTKEY, 'setup', 'page.includeJSFooterlibs.JquerySource = EXT:indexed_search_autocomplete/Resources/Public/JavaScript/Jquery-3.2.1.min.js');
}

/***************
 * Reset extConf array to avoid errors
 */
if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY])) {
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY] = serialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);
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