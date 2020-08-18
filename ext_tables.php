<?php
defined('TYPO3_MODE') or die();

(function(){

    // Register Typoscript
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        'indexed_search_autocomplete',
        'Configuration/TypoScript/',
        'Indexed Search AutoComplete'
    );

})();


