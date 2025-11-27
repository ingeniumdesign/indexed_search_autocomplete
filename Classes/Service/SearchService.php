<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2025 Sebastian Schmal - INGENIUMDESIGN <info@ingeniumdesign.de>
 *  All rights reserved
 *
 *  This file is part of the "indexed_search" Extension for TYPO3 CMS.
 *
 *  For the full copyright and license information, please read the
 *  LICENSE file that was distributed with this source code.
 *
 * ************************************************************* */

namespace ID\IndexedSearchAutocomplete\Service;

use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\IndexedSearch\Domain\Repository\IndexSearchRepository;

/**
 * SearchService
 */
class SearchService implements \TYPO3\CMS\Core\SingletonInterface
{
    public function searchAWord($arg)
    {
        $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        $languageId = $languageAspect->getId();

        $configurationManager = GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::class
        );
        $setting = $configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );

        // Suchparameter defensiv lesen
        $searchTerm = isset($arg['s']) ? trim((string)$arg['s']) : '';
        $maxResults = isset($arg['mr']) ? (int)$arg['mr'] : 10;

        // Wenn kein Suchbegriff übergeben wurde → leeres Ergebnis
        if ($searchTerm === '') {
            return [
                'autocompleteResults' => [],
                'mode' => 'word',
            ];
        }

        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        // Fetch all allowed Pages
        $allowedPageIds = array_map(static function ($a) {
            return trim($a);
        }, explode(',', $setting['plugin.']['tx_indexedsearch.']['settings.']['rootPidList']));

        $qbPage = $connectionPool->getQueryBuilderForTable('pages');
        $pages = $qbPage
            ->select('uid', 'pid')
            ->from('pages')
            ->executeQuery()
            ->fetchAllAssociative();

        // Create a Map in the style of <Parent-ID> -> <child-IDs>
        $pageMap = [];
        foreach ($pages as $row) {
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
        $rows = $qbWords
            ->select('baseword')
            ->from('index_words')
            ->join(
                'index_words',
                'index_rel',
                'ir',
                $qbWords->expr()->eq('ir.wid', 'index_words.wid')
            )
            ->join(
                'ir',
                'index_phash',
                'ip',
                $qbWords->expr()->eq('ip.phash', 'ir.phash')
            )
            ->where(
                $qbWords->expr()->like(
                    'index_words.baseword',
                    $qbWords->createNamedParameter(
                        $qbWords->escapeLikeWildcards($searchTerm) . '%'
                    )
                ),
                $qbWords->expr()->in(
                    'ip.data_page_id',
                    $qbWords->createNamedParameter(
                        $allowedPageIds,
                        \TYPO3\CMS\Core\Database\Connection::PARAM_INT_ARRAY
                    )
                ),
                $qbWords->expr()->eq(
                    'ip.sys_language_uid',
                    (int)$languageId
                )
            )
            ->groupBy('index_words.baseword')
            ->setMaxResults($maxResults)
            ->executeQuery()
            ->fetchAllAssociative();

        $autocomplete = [];
        foreach ($rows as $row) {
            if ($row['baseword'] !== $searchTerm) {
                $autocomplete[] = $row['baseword'];
            }
        }

        return [
            'autocompleteResults' => $autocomplete,
            'mode' => 'word',
        ];
    }

    public function searchASite($arg)
    {
        $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        $languageId = $languageAspect->getId();

        $configurationManager = GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::class
        );
        $setting = $configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );

        // Suchparameter defensiv lesen
        $searchTerm = isset($arg['s']) ? trim((string)$arg['s']) : '';
        $maxResults = isset($arg['mr']) ? (int)$arg['mr'] : 10;

        // Wenn kein Suchbegriff → sofort leeres Ergebnis
        if ($searchTerm === '') {
            return [
                'autocompleteResults' => [],
                'mode' => 'link',
            ];
        }

        $search = [
            [
                'sword' => $searchTerm,
                'oper' => 'AND',
            ],
        ];

        $settings = $setting['plugin.']['tx_indexedsearch.']['settings.'];
        $searchData = [
            'sortOrder' => 'rank_flag',
            'languageUid' => (int)$languageId,
            'sortDesc' => true,
            'searchType' => true,
            'numberOfResults' => $maxResults,
            'sword' => $searchTerm,
        ];

        // IndexSearchRepository für TYPO3 13 korrekt instanziieren
        $context = GeneralUtility::makeInstance(Context::class);
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $timeTracker = GeneralUtility::makeInstance(TimeTracker::class);
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $eventDispatcher = GeneralUtility::makeInstance(EventDispatcherInterface::class);

        /** @var IndexSearchRepository $searchRepository */
        $searchRepository = GeneralUtility::makeInstance(
            IndexSearchRepository::class,
            $context,
            $extensionConfiguration,
            $timeTracker,
            $connectionPool,
            $eventDispatcher
        );

        $searchRepository->initialize($settings, $searchData, [], $settings['rootPidList']);

        $resultData = $searchRepository->doSearch($search, -1);

        $result = [];

        // doSearch() kann false liefern – absichern
        if (is_array($resultData)
            && isset($resultData['resultRows'])
            && is_array($resultData['resultRows'])
        ) {
            foreach ($resultData['resultRows'] as $r) {
                $result[] = [
                    'page_id' => $r['page_id'] ?? null,
                    'title' => $r['item_title'] ?? '',
                    'description' => $r['item_description'] ?? '',
                ];
            }
        }

        return [
            'autocompleteResults' => $result,
            'mode' => 'link',
        ];
    }
}
