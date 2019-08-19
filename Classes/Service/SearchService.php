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

namespace ID\indexedSearchAutocomplete\Service;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * EntryController
 */
class SearchService implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * Search repository
     *
     * @var \TYPO3\CMS\IndexedSearch\Domain\Repository\IndexSearchRepository
     */
    protected $searchRepository = null;

    public function searchAWord($arg, $maxResults)
    {
        $languageId = $GLOBALS['TSFE']->sys_language_uid;
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $configurationManager = $objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
        $setting = $configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );

        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        // Fetch all allowed Pages
        $allowedPageIds = array_map(function ($a) {
            return trim($a);
        }, explode(',', $setting['plugin.']['tx_indexedsearch.']['settings.']['rootPidList']));
        $qbPage = $connectionPool->getQueryBuilderForTable('pages');
        $pages = $qbPage->select('uid', 'pid')->from('pages')->where($qbPage->expr()->eq(
            'sys_language_uid', (int)$languageId
        ))->execute();

        // Create a Map in the style of <Parent-ID> -> <child-IDs>
        $pageMap = [];
        while ($row = $pages->fetch()) {
            if (!isset($pageMap[$row['pid']])) {
                $pageMap[$row['pid']] = [];
            }
            $pageMap[$row['pid']][] = $row['uid'];
        }

        do {
            $found = false;
            foreach ($allowedPageIds as $id) {
                if (isset($pageMap[$id])) {
                    $found = true;
                    $allowedPageIds = array_merge($allowedPageIds, $pageMap[$id]);
                    unset($pageMap[$id]);
                }
            }
        } while ($found);

        // Fetch all Words that belong to an allowed page
        $qbWords = $connectionPool->getQueryBuilderForTable('index_words');
        $result = $qbWords
            ->select('baseword')
            ->from('index_words')
            ->join(
                'index_words',
                'index_rel',
                'ir',
                $qbWords->expr()->eq('ir.wid', 'index_words.wid')
            )->join(
                'ir',
                'index_phash',
                'ip',
                $qbWords->expr()->eq('ip.phash', 'ir.phash')
            )
            ->where(
                $qbWords->expr()->like(
                    'index_words.baseword', $qbWords->createNamedParameter($qbWords->escapeLikeWildcards($arg['s']) . '%')
                ),
                $qbWords->expr()->in(
                    'ip.data_page_id',
                    $qbWords->createNamedParameter(
                        $allowedPageIds,
                        \TYPO3\CMS\Core\Database\Connection::PARAM_INT_ARRAY
                    )
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

    public function searchASite($arg, $maxResults)
    {
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
            'languageUid' => (int)$languageId,
            'sortDesc' => true,
            'searchType' => true,
            'numberOfResults' => $maxResults,
            'sword' => $arg['s']
        ];

        $this->searchRepository->initialize($settings, $searchData, [], $settings['rootPidList']);

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
