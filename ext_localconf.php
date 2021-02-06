<?php
defined('TYPO3_MODE') or die();


// Setup
(function(){

    // Define TypoScript as content rendering template
    $GLOBALS['TYPO3_CONF_VARS']['FE']['contentRenderingTemplates'][] = 'indexed_search_autocomplete/Configuration/TypoScript/';

    /***************
     * Make the extension configuration accessible
     */
    if (class_exists('TYPO3\CMS\Core\Configuration\ExtensionConfiguration')) {
        $config = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
        );
        $indexedSearchAutocompleteConfiguration = $config->get('indexed_search_autocomplete');
    } else {
        // Fallback for CMS8
        // @extensionScannerIgnoreLine
        $indexedSearchAutocompleteConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['indexed_search_autocomplete'];
        if (!is_array($indexedSearchAutocompleteConfiguration)) {
            $indexedSearchAutocompleteConfiguration = unserialize($indexedSearchAutocompleteConfiguration);
        }
    }

    // Check whether to add Jquery 3.2 to the Frontend
    if (!$indexedSearchAutocompleteConfiguration['disableJquerySource']) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('
        page.includeJSFooterlibs.JquerySource = {$plugin.tx_indexedsearch_autocomplete.jqueryFile}
        ');
    }

    // Register Application
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'ID.indexed_search_autocomplete',
        'Search',
        [
            'Search' => 'search',

        ],
        [
            'Search' => 'search',

        ]
    );
})();