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