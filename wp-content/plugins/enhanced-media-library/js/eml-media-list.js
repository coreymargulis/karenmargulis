( function( $ ) {    
    
    $( document ).ready( function() 
    {
        var $mainFilter = $('select[name="attachment-filter"]'),
            $dataFilter = $('select#filter-by-date'),
            $taxFilters = $('select.eml-attachment-filters'),
            $resetFilters = $('#eml-reset-filters-query-submit');
            

        if ( ! $mainFilter.prop( 'selectedIndex' ) && 
             ! $dataFilter.prop( 'selectedIndex' ) &&
             ! $taxFilters.filter( function() { return $(this).prop( 'selectedIndex' ) } ).get().length )
        {            
            $resetFilters.prop( 'disabled', true );
        }
        else
        {
            $resetFilters.prop( 'disabled', false );
        }
        
        
        $( document ).on( 'change', 'select[name="attachment-filter"]', { 
            checkFilter : $mainFilter, 
            resetFilter : $taxFilters 
        }, resetFilters );
        
        $( document ).on( 'change', 'select.eml-attachment-filters', { 
            checkFilter : $mainFilter, 
            resetFilter : $mainFilter 
        }, resetFilters );
        
        $( document ).on( 'click', '#eml-reset-filters-query-submit', function() { 
        
            $mainFilter.prop( 'selectedIndex', 0 );
            $taxFilters.prop( 'selectedIndex', 0 );
            $dataFilter.prop( 'selectedIndex', 0 );
        });

    });
    
    function resetFilters( event )
    {
        if ( 'uncategorized' == event.data.checkFilter.val() )
        {
            event.data.resetFilter.prop( 'selectedIndex', 0 );
        }
    }

})( jQuery );