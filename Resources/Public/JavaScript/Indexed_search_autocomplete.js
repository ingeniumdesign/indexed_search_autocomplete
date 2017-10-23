jQuery(document).ready(function(){
    if (jQuery('input.search, input.tx-indexedsearch-searchbox-sword, input.indexed-search-atocomplete-sword, input.indexed-search-autocomplete-sword').length > 0) {
        initIndexSearchAutocomplete();
    }
});

function initIndexSearchAutocomplete() {
    jQuery('input.search, input.tx-indexedsearch-searchbox-sword, input.indexed-search-atocomplete-sword, input.indexed-search-autocomplete-sword').on('keypress keyup', function(e) {
        var $input = $(this);
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
        
        var mode = typeof $results.data('mode') === 'undefined' ? 'word' : $results.data('mode');
        var soc = $results.data('searchonclick') == true;
        
        // navigate through the suggestion-results
        if (e.which === 38 || e.which === 40 || e.keyCode === 10 || e.keyCode === 13) { // up / down / enter
            
            if (e.which === 38 && e.type === 'keyup') { // up
                var $prev = $results.find('li.highlighted').prev();
                
                if ($results.find('li.highlighted').length === 0 || $prev.length === 0) {
                    $results.find('li.highlighted').removeClass('highlighted');
                    $results.find('li').last().addClass('highlighted');
                    return;
                }
                
                $results.find('li.highlighted').removeClass('highlighted');
                $prev.addClass('highlighted');
            }
            
            if (e.which === 40 && e.type === 'keyup') { // down
                var $next = $results.find('li.highlighted').next();
                if ($results.find('li.highlighted').length === 0 || $next.length === 0) {
                    $results.find('li.highlighted').removeClass('highlighted');
                    $results.find('li').first().addClass('highlighted');
                    return;
                }
                
                $results.find('li.highlighted').removeClass('highlighted');
                $next.addClass('highlighted');
            }
            
            if ((e.keyCode === 10 || e.keyCode === 13) && e.type === 'keypress') { // enter
                if ($results.is(':visible') && $results.find('li.highlighted').length > 0) {
                    if (mode === 'word') {
                        $results.find('li.highlighted').click();
                        if (soc) {
                            $input.closest('form').submit();
                        }
                    } else {
                        window.location = $results.find('li.highlighted a.navigate-on-enter').attr('href');
                    }
                    e.preventDefault();
                }
            }
            
            return;
        }
        
        if (e.type !== 'keyup') {
            return;
        }
        
        $results.html('');
        
        var val = $(this).val();
        var minlen = typeof $results.data('minlength') === 'undefined' ? 3 : $results.data('minlength');
        var maxResults = typeof $results.data('maxresults') === 'undefined' ? 10 : $results.data('maxresults');
        
        
        if (val.length < minlen) {
            return;
        }
        
       
       $results.addClass('autocomplete_searching');
        
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
               $li = $results
                    .show()
                    .html(data)
                    .removeClass('autocomplete_searching')
                    .find('li');
                $li.click(function() {
                    $input.val($(this).text().trim());
                    $results.html('').hide();
                });
                if ($li.length === 0) {
                    $results.html('').hide();
                }
            }
        });
    }).attr('autocomplete', 'off');
}