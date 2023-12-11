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
            'typo3' => '9.5.0-12.9.99',
        ],
        'conflicts' => [],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Sebastian Schmal',
    'author_email' => 'info@ingeniumdesign.de',
    'author_company' => 'INGENIUMDESIGN',
    'version' => '1.0.12',
];
