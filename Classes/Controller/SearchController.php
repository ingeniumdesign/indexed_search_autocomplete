<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Sebastian Schmal - INGENIUMDESIGN <info@ingeniumdesign.de>
 *  All rights reserved
 *
 *  This file is part of the "indexed_search" Extension for TYPO3 CMS.
 *
 *  For the full copyright and license information, please read the
 *  LICENSE file that was distributed with this source code.
 *
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
        if ($searchmode == 'word') {
            $result = $this->searchAWord($arg, $arg['mr']);
        } else {
            $result = $this->searchASite($arg, $arg['mr']);
        }

        foreach ($result as $key => $value) {
            $this->view->assign($key, $value);
        }
    }

    private function searchAWord($arg, $maxResults) {
        $languageId = $GLOBALS['TSFE']->sys_language_uid;
        
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('index_words');
        $result = $queryBuilder
                ->select('baseword')
                ->from('index_words')
                ->join(
                     'index_words',
                    'index_rel',
                    'ir',
                    $queryBuilder->expr()->eq('ir.wid', 'index_words.wid')
                 )->join(
                    'ir',
                    'index_phash',
                    'ip',
                    $queryBuilder->expr()->eq('ip.phash', 'ir.phash')
                 )
                ->where(
                        $queryBuilder->expr()->like(
                                'index_words.baseword', $queryBuilder->createNamedParameter($queryBuilder->escapeLikeWildcards($arg['s']) . '%')
                        ),
                        $queryBuilder->expr()->eq(
                                'ip.sys_language_uid', (int) $languageId
                        )
                )
                ->groupBy('index_words.baseword')
                ->setMaxResults($maxResults)
                ->execute();

        $autocomplete = [];
        while ($row = $result->fetch()) {
            if ($row['baseword'] !== $arg['s']) {
                $autocomplete[] = $row['baseword'];
            }
        }

        return [
            'autocompleteResults' => $autocomplete,
            'mode' => 'word'
        ];
    }

    private function searchASite($arg, $maxResults) {
        $languageId = $GLOBALS['TSFE']->sys_language_uid;
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
            'languageUid' => (int) $languageId,
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
