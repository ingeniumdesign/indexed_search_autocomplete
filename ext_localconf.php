<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

/***************
 * Define TypoScript as content rendering template
 */
$GLOBALS['TYPO3_CONF_VARS']['FE']['contentRenderingTemplates'][] = 'indexed_search_autocomplete/Configuration/TypoScript/';

$TYPO3_CONF_VARS['EXTCONF']['indexed_search']['pi1_hooks']['initialize_postProc'] = 'EXT:indexed_search_autocomplete/Classes/Hooks/IndexedSearchHook.php:&tx_indexed_search_autocomplete_pi1';
$TYPO3_CONF_VARS['FE']['eID_include']['indexed_search_autocomplete'] = 'EXT:indexed_search_autocomplete/Classes/Hooks/FeIndexHook.php';

/***************
 * Make the extension configuration accessible
 */
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY])) {
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY] = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);
}

// Add org. Jquery 3.2 in the Frontend
if (!$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]['disableJquerySource']) {
    #\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $_EXTKEY . '/Configuration/PageTS/Mod/Wizards/newContentElement.txt">');
}


/***************
 * Reset extConf array to avoid errors
 */
if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY])) {
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY] = serialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);
}