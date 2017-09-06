<?php
/***************************************************************
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
 ***************************************************************/

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
    public function searchAction() {
        $arg = $_REQUEST;
        $searchmode = $arg['m'];
        
        if ($searchmode == 'word') {
            return $this->searchAWord($arg, $arg['mr']);
        }
        
        return $this->searchASite($arg, $arg['mr']);
    }
    
    
    private function searchAWord($arg, $maxResults) {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('index_words');
        $result = $queryBuilder
            ->select('baseword')
            ->from('index_words')
            ->where(
                $queryBuilder->expr()->like(
                    'baseword',
                    $queryBuilder->createNamedParameter($queryBuilder->escapeLikeWildcards($arg['s']) . '%')
                )
            )
            ->setMaxResults($maxResults)
            ->execute();
        
        $autocomplete = [];
        while ($row = $result->fetch()) {
            $autocomplete[] = $row['baseword'];
        }
        /*
        // display results
        $filename = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:indexed_search_autocomplete/Resources/Private/Templates/Search/AutocompleteWord.html');
        $tempView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $tempView->setTemplatePathAndFilename($filename);

        $tempView->assignMultiple([
            
        ]);
        $tempHtml = $tempView->render();*/

        return [
            'autocompleteResults' => $autocomplete,
            'mode' => 'link'
        ];
    }
    
    
    private function searchASite($arg, $maxResults) {
        $search = [
            [
                'sword' => $arg['s'],
                "oper" => "AND"
            ]
        ];
        
        $this->searchRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\IndexedSearch\Domain\Repository\IndexSearchRepository::class);
        
        $settings = [
            'targetPid' => 0,
            'displayRules' => true,
            'displayAdvancedSearchLink' => true,
            'displayResultNumber' => false,
            'breadcrumbWrap' => "\/ || \/",
            'displayParsetimes' => false,
            'displayLevel1Sections' => true,
            'displayLevel2Sections' => false,
            'displayLevelxAllTypes' => false,
            'displayForbiddenRecords' => false,
            'alwaysShowPageLinks' => true,
            'mediaList' => '',
            'rootPidList' => true,
            'page_links' => 10,
            'detectDomainRcords' => false,
            'defaultFreeIndexUidList' => '',
            'searchSkipExtendToSubpagesChecking' => false,
            'exactCount' => false,
            'forwardSearchWordsInResultLink' => [
                'no_cache' => true,
                '_typoScriptNodeValue' => false
            ],
            'results' => [
                'titleCropAfter' => 50,
                'titleCropSignifier' => '...',
                'summaryCropAfter' => 180,
                'summaryCropSignifier' => '',
                'hrefInSummaryCropAfter' => 60,
                'hrefInSummaryCropSignifier' => '...',
                'markupSW_summaryMax' => 300,
                'markupSW_postPreLgd' => 60,
                'markupSW_postPreLgd_offset' => 5,
                'markupSW_divider' => [
                    'noTrimWrap' => '| | |',
                    '_typoScriptNodeValue' => '...'
                ]
            ],
            'blind' => [
                'searchType' => false,
                'defaultOperand' => false,
                'sections' => false,
                'freeIndexUid' => true,
                'mediaType' => false,
                'sortOrder' => false,
                'group' => false,
                'languageUid' => false,
                'desc' => false,
                'numberOfResults' => '10,25,50,100'
            ],
            'defaultOptions' => [
                'defaultOperand' => false,
                'sections' => false,
                'freeIndexUid' => -1,
                'mediaType' => -1,
                'sortOrder' => 'rank_flag',
                'languageUid' => -1,
                'sortDesc' => true,
                'searchType' => true,
                'extResume' => true
            ],
            'results.' => [
                 'summaryCropAfter' => 180,
                'summaryCropSignifier' => '',
                'titleCropAfter' => 50,
                'titleCropSignifier' => '...',
                'markupSW_summaryMax' => 300,
                'markupSW_postPreLgd' => 60,
                'markupSW_postPreLgd_offset' => 5,
                'markupSW_divider' => ' ... ',
                'hrefInSummaryCropAfter' => 60,
                'hrefInSummaryCropSignifier' => '...'
            ]
        ];
        
        $searchData = [
           'defaultOperand' => false,
            'sections' => false,
            'freeIndexUid' => -1,
            'mediaType' => -1,
            'sortOrder' => 'rank_flag',
            'languageUid' => -1,
            'sortDesc' => true,
            'searchType' => true,
            'extResume' => true,
            '_sections' => false,
            '_freeIndexUid' => '_',
            'pointer' => false,
            'ext' => '',
            'group' => '',
            'desc' => '',
            'numberOfResults' => 10,
            'extendedSearch' => '',
            'sword' => $arg['s'],
            'submitButton' => 'Suchen',  
        ];
        
        $this->searchRepository->initialize($settings, $searchData, [], 1);
        
        $resultData = $this->searchRepository->doSearch($search, -1);
        
        $result = [];
        foreach($resultData['resultRows'] as $r) {
            $result[] = [
                'page_id' => $r['page_id'],
                'title' => $r['item_title'],
                'description' => $r['item_description']
            ];
            if (count($result) >= $maxResults - 1) {
                break;
            }
        }
        
        /*
        // display results
        $filename = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:indexed_search_autocomplete/Resources/Private/Templates/Search/AutocompleteLink.html');
        $tempView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $tempView->setTemplatePathAndFilename($filename);

        $tempView->assignMultiple();
        $tempHtml = $tempView->render();*/

        return [
            'autocompleteResults' => $result,
            'mode' => 'link'
        ]; 
    }
}

