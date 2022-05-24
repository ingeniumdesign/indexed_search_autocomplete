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

namespace ID\IndexedSearchAutocomplete\Controller;

use TYPO3\CMS\Extbase\Annotation\Inject;


/**
 * SearchController
 *
 * This controller receives the search and handles them.
 */
class SearchController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    /**
     * Search repository
     *
     * @var \TYPO3\CMS\IndexedSearch\Domain\Repository\IndexSearchRepository
     */
    protected $searchRepository = null;
    
     /**
      * Search functions
      * 
      * @var \ID\IndexedSearchAutocomplete\Service\SearchService
      * @Inject
      */
    protected $searchService = null;

    /**
     * action search
     *
     * @return string
     */
    public function SearchAction() {

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
    }
}
