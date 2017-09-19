<?php
/************************************************************************
 * Extension Manager/Repository config file for ext "indexed_search_autocomplete".
 ************************************************************************/
$EM_CONF[$_EXTKEY] = [
    'title' => 'Indexed Search Autocomplete',
    'description' => 'Extends the TYPO3 Core Extension Indexed_Search searchform with an autocomplete feature.',
    'category' => 'fe',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-8.7.99',
        ],
        'conflicts' => [

        ],
    ],
    'autoload' => [
        'psr-4' => [
            'ID\\IndexedSearchAutocomplete\\' => 'Classes'
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Sebastian Schmal',
    'author_email' => 'info@ingeniumdesign.de',
    'author_company' => 'INGENIUMDESIGN',
    'version' => '8.7.1',
];