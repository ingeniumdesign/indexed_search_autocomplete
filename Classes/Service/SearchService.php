<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2026 Sebastian Schmal - INGENIUMDESIGN <info@ingeniumdesign.de>
 *  All rights reserved
 *
 *  This file is part of the "indexed_search" Extension for TYPO3 CMS.
 *
 *  For the full copyright and license information, please read the
 *  LICENSE file that was distributed with this source code.
 *
 * ************************************************************* */

namespace ID\IndexedSearchAutocomplete\Service;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\IndexedSearch\Domain\Repository\IndexSearchRepository;

/**
 * SearchService
 */
class SearchService implements \TYPO3\CMS\Core\SingletonInterface
{
    public function __construct(
        private readonly Context $context,
        private readonly ConnectionPool $connectionPool,
        private readonly \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager,
        private readonly IndexSearchRepository $searchRepository,
    ) {}

    public function searchAWord($arg, int $fallbackRootPid = 0)
    {
        $languageAspect = $this->context->getAspect('language');
        $languageId = $languageAspect->getId();

        $frontendUserGroupList = implode(',',
            $this->context->getPropertyFromAspect('frontend.user', 'groupIds', [0, -1])
        );

        $setting = $this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );

        // Suchparameter defensiv lesen
        $searchTerm = isset($arg['s']) ? trim((string)$arg['s']) : '';
        $maxResults = max(1, min(50, (int)($arg['mr'] ?? 10)));

        // Wenn kein Suchbegriff übergeben wurde → leeres Ergebnis
        if ($searchTerm === '') {
            return [
                'autocompleteResults' => [],
                'mode' => 'word',
            ];
        }

        // rootPidList aus den indexed_search-Settings; leer = aktuelle Site-Root (Core-Semantik)
        $rootPidList = trim((string)($setting['plugin.']['tx_indexedsearch.']['settings.']['rootPidList'] ?? ''));
        if ($rootPidList === '') {
            $rootPidList = (string)$fallbackRootPid;
        }

        // Fetch all allowed Pages
        $allowedPageIds = array_map(static function ($a) {
            return (int)trim($a);
        }, explode(',', $rootPidList));

        $qbPage = $this->connectionPool->getQueryBuilderForTable('pages');
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
        $qbWords = $this->connectionPool->getQueryBuilderForTable('index_words');
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
            ->join(
                'ip',
                'index_grlist',
                'ig',
                $qbWords->expr()->eq('ig.phash', 'ip.phash')
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
                ),
                $qbWords->expr()->eq(
                    'ig.gr_list',
                    $qbWords->createNamedParameter($frontendUserGroupList)
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

    public function searchASite($arg, int $fallbackRootPid = 0)
    {
        $languageAspect = $this->context->getAspect('language');
        $languageId = $languageAspect->getId();

        $setting = $this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );

        // Suchparameter defensiv lesen
        $searchTerm = isset($arg['s']) ? trim((string)$arg['s']) : '';
        $maxResults = max(1, min(50, (int)($arg['mr'] ?? 10)));

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

        $settings = $setting['plugin.']['tx_indexedsearch.']['settings.'] ?? null;
        if (!is_array($settings)) {
            // indexed_search-TypoScript nicht geladen → kein Suchscope ermittelbar
            return [
                'autocompleteResults' => [],
                'mode' => 'link',
            ];
        }

        $rootPidList = trim((string)($settings['rootPidList'] ?? ''));
        if ($rootPidList === '') {
            $rootPidList = (string)$fallbackRootPid; // Core-Semantik: leer = aktuelle Site-Root
        }

        $searchData = [
            'sortOrder' => 'rank_flag',
            'languageUid' => (int)$languageId,
            'sortDesc' => true,
            'searchType' => true,
            'numberOfResults' => $maxResults,
            'sword' => $searchTerm,
        ];

        $this->searchRepository->initialize($settings, $searchData, [], $rootPidList);
        $resultData = $this->searchRepository->doSearch($search, -1);

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
