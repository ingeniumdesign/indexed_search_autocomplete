<?php
defined('TYPO3_MODE') or die();


// Setup
(function(){

    // Define TypoScript as content rendering template
    $GLOBALS['TYPO3_CONF_VARS']['FE']['contentRenderingTemplates'][] = 'indexed_search_autocomplete/Configuration/TypoScript/';

    // Retrieve configuration
    $config = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    );

    // Check whether to add Jquery 3.2 to the Frontend
    if (!$config->get['disableJquerySource']) {
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