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

use ID\IndexedSearchAutocomplete\Service\SearchService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\IndexedSearch\Domain\Repository\IndexSearchRepository;

/**
 * EntryController
 */
class SearchController extends ActionController {

    /**
     * Search repository
     *
     * @var IndexSearchRepository
     */
    protected $searchRepository;
    
     /**
      * Search functions
      * 
      * @var SearchService
      */
    protected $searchService;

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
            $result = $this->searchService->searchAWord($arg, $arg['mr']);
        } else {
            $result = $this->searchService->searchASite($arg, $arg['mr']);
        }

        foreach ($result as $key => $value) {
            $this->view->assign($key, $value);
        }
    }
    
    /**
     * @param SearchService $searchService
     * @return void
     */
    public function injectSearchService(SearchService $searchService) {
        $this->searchService = $searchService;
    }
}
