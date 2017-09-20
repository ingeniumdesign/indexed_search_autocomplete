<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Sebastian Schmal - INGENIUMDESIGN <info@ingeniumdesign.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

namespace ID\indexedSearchAutocomplete\Controller;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * EntryController
 */
class SearchController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    /**
     * Search repository
     *
     * @var \TYPO3\CMS\IndexedSearch\Domain\Repository\IndexSearchRepository
     */
    protected $searchRepository = null;

    /**
     * action search
     *
     * @return string
     */
    public function SearchAction() {
        $arg = $_REQUEST;
        $searchmode = $arg['m'];

        $result = [];
        if ($searchmode == 'word' && 0) {
            $result = $this->searchAWord($arg, $arg['mr']);
        } else {
            $result = $this->searchASite($arg, $arg['mr']);
        }

        foreach ($result as $key => $value) {
            $this->view->assign($key, $value);
        }
    }

    private function searchAWord($arg, $maxResults) {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('index_words');
        $result = $queryBuilder
                ->select('baseword')
                ->from('index_words')
                ->where(
                        $queryBuilder->expr()->like(
                                'baseword', $queryBuilder->createNamedParameter($queryBuilder->escapeLikeWildcards($arg['s']) . '%')
                        )
                )
                ->setMaxResults($maxResults)
                ->execute();

        $autocomplete = [];
        while ($row = $result->fetch()) {
            $autocomplete[] = $row['baseword'];
        }

        return [
            'autocompleteResults' => $autocomplete,
            'mode' => 'word'
        ];
    }

    private function searchASite($arg, $maxResults) {
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

        $configurationManager = $objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
        
        $setting = $configurationManager->getConfiguration(
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );


        $search = [
            [
                'sword' => $arg['s'],
                "oper" => "AND"
            ]
        ];

        $this->searchRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\IndexedSearch\Domain\Repository\IndexSearchRepository::class);

        $settings = $setting['plugin.']['tx_indexedsearch.']['settings.'];
        $searchData = [
            'sortOrder' => 'rank_flag',
            'languageUid' => -1,
            'sortDesc' => true,
            'searchType' => true,
            'numberOfResults' => $maxResults,
            'sword' => $arg['s']
        ];

        $this->searchRepository->initialize($settings, $searchData, [], 1);

        $resultData = $this->searchRepository->doSearch($search, -1);

        $result = [];
        foreach ($resultData['resultRows'] as $r) {
            $result[] = [
                'page_id' => $r['page_id'],
                'title' => $r['item_title'],
                'description' => $r['item_description']
            ];
        }

        return [
            'autocompleteResults' => $result,
            'mode' => 'link'
        ];
    }

}
