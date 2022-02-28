# Indexed Search Autocomplete

## TYPO3 Extension `indexed_search_autocomplete`

[![TYPO3 9](https://img.shields.io/badge/TYPO3-9-orange.svg)](https://get.typo3.org/version/9)
[![TYPO3 10](https://img.shields.io/badge/TYPO3-10-orange.svg)](https://get.typo3.org/version/10)
[![TYPO3 11](https://img.shields.io/badge/TYPO3-11-orange.svg)](https://get.typo3.org/version/11)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/INGENIUMDESIGN/)
[![Latest Stable Version](https://poser.pugx.org/id/indexed-search-autocomplete/v/stable)](https://packagist.org/packages/id/indexed-search-autocomplete)
[![Monthly Downloads](https://poser.pugx.org/id/indexed-search-autocomplete/d/monthly)](https://packagist.org/packages/id/indexed-search-autocomplete)
[![License](https://poser.pugx.org/id/indexed-search-autocomplete/license)](https://packagist.org/packages/id/indexed-search-autocomplete)

Extends the TYPO3 Core Extension Indexed_Search searchform with an autocomplete feature.

## Minimal Dependencies

* TYPO3 CMS 8.7.x - 11.4.x
* PHP 7.x
* Jquery 1.x

# Quick Install Guide

### Install the TYPO3 Core indexed_search extensions

**Step 1:** The extension works with the TYPO3 Core indexed_search extension. So please install and configure this one first.

**Step 2:** Install this plugin.

#### Installation using Composer

The recommended way to install the extension is using [Composer][1].

Run the following command within your Composer based TYPO3 project:

```
composer require id/indexed-search-autocomplete
```

#### Installation as extension from TYPO3 Extension Repository (TER)

Download and install the [extension][2] with the extension manager module.

**Step 3:** Find the fluid file that contains the text-input for the search-word.

**Step 4:** Add the class '.indexed-search-autocomplete-sword' to this text-input.

**Step 5:** Now add the following line where you want the results to be displayed (so in most of the cases below the text-input):
```html
<div class="search-autocomplete-results  no-results" data-mode="word" data-searchonclick="false" data-maxresults="10" data-minlength="2" data-searchurl="{f:uri.action(action: 'search', pageType: '7423794', noCache: 1, noCacheHash: 1, extensionName: 'indexedSearchAutocomplete', controller: 'Search')}"></div>
```

**Step 6:** Now you can configure the plugins options with the parameters of that <Div> (see options)

**Step 7:** TYPO3 Site-Config add the new PAGE typeNum 7423794: 
```yaml
routeEnhancers:
  PageTypeSuffix:
   type: PageType
   default: /
   index: ''
   map:
     /: 0
     sitemap.xml: 500001
     autocomplete: 7423794
```
**Additional:** Make sure to disable Indexed-Search option "Use MySQL specific fulltext search", otherwise the word-suggestion won't work.

### Options

* Enable or Disable the **JQuery-Source** in the Extension Settings. (Backend -> Extension-Settings)
  ```page.includeJSFooterlibs.JquerySource = ```
* **data-mode="word"** => the following values are possible: word or link. Depending on which option the plugin suggests either words or links while typing. You can edit both template files unter indexed_search_autocomplete/Resources/Private/Partials/ (Fluid)
* **data-maxresults="10"** => The amount of entrys a suggetion can have max. (Fluid)
* **data-minlength="2"** => how many characters have to be in the input-box for the plugin to make it's first suggestion. (Fluid)
* **data-searchonclick="false"** => If one selects a suggestion, may this submit the form (so basically the search starts after one has selected a word). Possible values are "false" or "true". 

# Contact &amp; Communication

## GIT

We are on github:<br />
https://github.com/ingeniumdesign/indexed_search_autocomplete/


## Agency

INGENIUMDESIGN<br />
TYPO3 - Internetagentur<br />
65510 Idstein<br />
<br />
https://www.ingeniumdesign.de/ <br />
info@ingeniumdesign.de

## Donate

Amazon: https://www.amazon.de/hz/wishlist/ls/13RT2BFNRP05 <br />
PayPay: www.paypal.me/INGENIUMDESIGN/

## Donwloads
TYPO3 TER: https://extensions.typo3.org/extension/indexed_search_autocomplete <br />
Composer: https://packagist.org/packages/id/indexed-search-autocomplete


## Used by

We are searching for LIVE-References or Live-Examples for the TYPO3 indexed_search Autocomplete Extension.<br />
Please be so kind to send us an E-Mail if you're using it. Thanks!

**Links/References:**

https://www.ingeniumdesign.de/ - by INGENIUMDESIGN<br />
https://www.takeoffmedia.de/ - by INGENIUMDESIGN<br />
https://www.easy-sprachreisen.de/ - by INGENIUMDESIGN<br />
https://www.wirtschaft-macht-klimaschutz.de/ - by wilhelm innovative medien GmbH<br />
https://www.radprax.de/ - by wilhelm innovative medien GmbH

[1]: https://packagist.org/packages/id/indexed-search-autocomplete
[2]: https://extensions.typo3.org/extension/indexed_search_autocomplete