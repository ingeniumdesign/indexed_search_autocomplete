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
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN,
    );
})();
