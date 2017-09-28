# Indexed Search Autocomplete

Extends the TYPO3 Core Extension Indexed_Search searchform with an autocomplete feature.

## Minimal Dependencies

* TYPO3 CMS 8.7.x
* PHP 7.x
* Jquery 1.x

# Quick Install Guide

### Install the TYPO3 Core indexed_search extensions

The extension works with the TYPO3 Core indexed_search extension. So please install and configure this one first.
Then install this plugin.
Afterward add the class '.indexed-search-atocomplete-sword' to the search-input-box in your fluid and the folowing div at the place you want to have the results:
<div class="search-autocomplete-results" data-mode="word" data-maxresults="10" data-minlength="2" data-searchurl="{f:uri.action(action: 'search', pageType: '7423794', noCache: 1, noCacheHash: 1)}"></div>

This div is also the one where you can configure most options of the Plugin:
(see Options)

### Options

* Enable or Disable the JQuery-Source in the Extension Settings. (Backend -> Extension-Settings)
* data-mode="word" => the following values are possible: word or link. Depending on which option the plugin suggests either words or links while typing. You can edit both template files unter indexed_search_autocomplete/Resources/Private/Partials/ (Fluid)
* data-maxresults="10" => The amount of entrys a suggetion can have max. (Fluid)
* data-minlength="2" => how many characters have to be in the input-box for the plugin to make it's first suggestion. (Fluid)

# Contact &amp; Communication

## GIT

We are on github:<br />
https://github.com/ingeniumdesign/indexed_search_autocomplete/


## Agency

INGENIUMDESIGN<br />
TYPO3 - Internetagentur<br />
In der Eisenbach 22<br />
65510 Idstein<br />
<br />
http://www.ingeniumdesign.de/<br />
info@ingeniumdesign.de

## Slack