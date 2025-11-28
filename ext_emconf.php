<?php

/*
 * This file is part of the package ID\IndexedSearchAutocomplete.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'Indexed Search Autocomplete',
    'description' => 'Extends the TYPO3 Core Extension Indexed_Search searchform with an autocomplete feature.',
    'category' => 'plugin',
    'version' => '13.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
            'indexed_search' => '13.4.0-13.4.99',
            'php' => '8.2.0-8.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Sebastian Schmal',
    'author_email' => 'info@ingeniumdesign.de',
    'author_company' => 'INGENIUMDESIGN',
];
