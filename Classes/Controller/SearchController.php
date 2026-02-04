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

namespace ID\IndexedSearchAutocomplete\Controller;

use ID\IndexedSearchAutocomplete\Service\SearchService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Psr\Http\Message\ResponseInterface;

/**
 * SearchController
 *
 * This controller receives the search and handles them.
 */
class SearchController extends ActionController {

    /**
    * Search functions
    *
    * @var SearchService
    */
    protected $searchService = null;


    public function __construct(
        SearchService $searchService
    ) {
        $this->searchService = $searchService;
    }

    /**
    * action search
    *
    * @return ResponseInterface
    */
    public function searchAction(): ResponseInterface {
        // POST-Daten direkt vom Extbase-Request lesen (ist selbst ein PSR-7 ServerRequestInterface)
        $arg = $this->request->getParsedBody();
        if (!is_array($arg)) {
            $arg = [];
        }

        // Mode sicher auslesen, Standard = 'word'
        $mode = $arg['m'] ?? 'word';

        // Check which search to perform
        if ($mode === 'word') {
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
