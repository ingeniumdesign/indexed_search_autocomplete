<?php

/*
 * This file is part of the package ID\IndexedSearchAutocomplete.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

defined('TYPO3') or die('Access denied.');

(function() {
    // Load TypoScript constants globally
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants(
        "@import 'EXT:indexed_search_autocomplete/Configuration/TypoScript/constants.typoscript'"
    );

    // Load TypoScript setup globally
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
        "@import 'EXT:indexed_search_autocomplete/Configuration/TypoScript/setup.typoscript'"
    );

    // Register Application
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'IndexedSearchAutocomplete',
        'Search',
        [
            \ID\IndexedSearchAutocomplete\Controller\SearchController::class => 'search',
        ],
        [
            \ID\IndexedSearchAutocomplete\Controller\SearchController::class => 'search',
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
    );
})();
