<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2026 Sebastian Schmal - INGENIUMDESIGN <info@ingeniumdesign.de>
 *  All rights reserved
 *
 *  This file is part of the "indexed_search_autocomplete" Extension.
 *
 *  For the full copyright and license information, please read the
 *  LICENSE.txt file that was distributed with this source code.
 *
 * ************************************************************* */

namespace ID\IndexedSearchAutocomplete\Controller;

use ID\IndexedSearchAutocomplete\Service\SearchService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * SearchController
 *
 * This controller receives the search and handles them.
 */
class SearchController extends ActionController {

    public function __construct(
        private readonly SearchService $searchService
    ) {}

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

        // Site-Root als Fallback-Scope, falls rootPidList nicht konfiguriert ist (Core-Semantik)
        $site = $this->request->getAttribute('site');
        $fallbackRootPid = $site instanceof Site ? $site->getRootPageId() : 0;

        // Mode sicher auslesen, Standard = 'word'
        $mode = $arg['m'] ?? 'word';

        // Check which search to perform
        if ($mode === 'word') {
            $result = $this->searchService->searchAWord($arg, $fallbackRootPid);
        } else {
            $result = $this->searchService->searchASite($arg, $fallbackRootPid);
        }

        // Assign the results
        foreach ($result as $key => $value) {
            $this->view->assign($key, $value);
        }

        return $this->htmlResponse();
    }
}
