jQuery(document).ready(function(){
    if (jQuery('.search').length > 0) {
        initIndexSearchAutocomplete();
    }
});

function initIndexSearchAutocomplete() {
    jQuery('.search, .tx-indexedsearch-searchbox-sword, .indexed-search-atocomplete-sword').on('keyup', function() {
        var $elem = $(this);
        var $results;
        while($elem.prop("tagName") !== 'HTML') {
            $results = $elem.find('.search-autocomplete-results');
            if ($results.length > 0) {
                break;
            }
            $elem = $elem.parent();
        }
        if ($elem.prop("tagName") === 'HTML') {
            console.log("we couldn't find a result div (.search-autocomplete-results)");
            return ;
        }
        
        $results.html('');
        
        var val = $(this).val();
        var minlen = typeof $results.data('minlength') === 'undefined' ? 3 : $results.data('minlength');
        var maxResults = typeof $results.data('maxresults') === 'undefined' ? 10 : $results.data('maxresults');
        var mode = typeof $results.data('mode') === 'undefined' ? 'word' : $results.data('mode');
        
        if (val.length < minlen) {
            return;
        }
        
       
        
        $.ajax({
            url: $results.data('searchurl'),
            cache: false,
            method: 'POST',
            data: {
                s: val,
                m: mode,
                mr: maxResults
            },
            success: function (data) {
               $results.html(data);
            }
        });
    });
}