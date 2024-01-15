<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2024 Sebastian Schmal - INGENIUMDESIGN <info@ingeniumdesign.de>
 *  All rights reserved
 *
 *  This file is part of the "indexed_search" Extension for TYPO3 CMS.
 *
 *  For the full copyright and license information, please read the
 *  LICENSE file that was distributed with this source code.
 *
 * ************************************************************* */

namespace ID\IndexedSearchAutocomplete\Controller;

use ID\IndexedSearchAutocomplete\Service\SearchService;
use TYPO3\CMS\IndexedSearch\Domain\Repository\IndexSearchRepository;

/**
 * SearchController
 *
 * This controller receives the search and handles them.
 */
class SearchController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    /**
     * Search repository
     *
     * @var IndexSearchRepository
     */
    protected $searchRepository = null;

     /**
      * Search functions
      *
      * @var SearchService
      */
    protected $searchService = null;


    public function __construct(IndexSearchRepository $searchRepository, SearchService $searchService)
    {
        $this->searchRepository = $searchRepository;
        $this->searchService = $searchService;
    }

    /**
     * action search
     *
     * @return string
     */
    public function SearchAction(): \Psr\Http\Message\ResponseInterface {

        // Fetch the request
        $arg = $_REQUEST;

        // Check which search to perform
        if ($arg['m'] == 'word') {
            $result = $this->searchService->searchAWord($arg);
        } else {
            $result = $this->searchService->searchASite($arg);
        }

        // Assign the results
        foreach ($result as $key => $value) {
            $this->view->assign($key, $value);
        }

        return $this->htmlResponse();
    }
}
