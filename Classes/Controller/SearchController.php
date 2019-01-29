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

namespace Id\IndexedSearchAutocomplete\Controller;

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
      * Search functions
      * 
      * @var Id\IndexedSearchAutocomplete\Service\SearchService
      * @inject
      */
    protected $searchService = null;

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
}
