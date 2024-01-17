<?php

/*
 * This file is part of the package ID\IndexedSearchAutocomplete.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

defined('TYPO3') or die('Access denied.');

(function(){
    // Register Typoscript
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        'indexed_search_autocomplete',
        'Configuration/TypoScript/',
        'Indexed Search AutoComplete'
    );
})();
