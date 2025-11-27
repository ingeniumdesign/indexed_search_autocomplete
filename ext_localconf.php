<?php

/*
 * This file is part of the package ID\IndexedSearchAutocomplete.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

defined('TYPO3') or die('Access denied.');

(function() {
    // Define TypoScript as content rendering template
    $GLOBALS['TYPO3_CONF_VARS']['FE']['contentRenderingTemplates'][] = 'indexed_search_autocomplete/Configuration/TypoScript/';

    // Make the extension configuration accessible
    $extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    );

    //$indexedSearchAutocompleteConfiguration = $extensionConfiguration->get('indexed_search_autocomplete');

    // Check whether to add Jquery 3.2 to the Frontend
    //if (!$indexedSearchAutocompleteConfiguration['disableJquerySource']) {
    //    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('
    //    page.includeJSFooterlibs.JquerySource = {$plugin.tx_indexedsearch_autocomplete.jqueryFile}
    //    ');
    //}

    // Register Application
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'IndexedSearchAutocomplete',
        'Search',
        [
            \ID\IndexedSearchAutocomplete\Controller\SearchController::class => 'search',
        ],
        [
            \ID\IndexedSearchAutocomplete\Controller\SearchController::class => 'search',
        ]
    );
})();
