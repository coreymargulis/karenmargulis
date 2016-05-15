(function($) {
    
    button_bg = '' !== button_bg ? ' style="background-color: ' + button_bg + '"' : '';
	
	$.fn.extend({
		
		duplicateButton: function ( bclass )
		{
            bclass = ' ' + bclass || '';

			if ( $(this).length ) 
			{
                var id = $(this).attr( 'id' ),
                    content = '<li id="wp-admin-bar-wpuxss-tpb-' + id + '"  class="wpuxss-tpb' + bclass + '"><a class="ab-item" href="#" id="top-toolbar-submit" for="' + id + '"' + button_bg + '><span class="ab-icon"></span><span class="ab-label">' + $(this).val() + '</span></a></li>';

                if ( $('.ab-top-menu .wpuxss-tpb').length )
                    $('.ab-top-menu .wpuxss-tpb').last().after( content );
                else if ( $('#wp-admin-bar-new-content').length ) 
                    $('#wp-admin-bar-new-content').after( content );
                else
                    $('.ab-top-menu').append( content );

				return true;
			}
			return false;
		}
		
	});

    var button = $('input[type="submit"].button-primary, input[type="button"].button-primary, input[type="submit"].acf-button').not('.find-box input, .widget-control-save'),
        draft_button = $('input[type="submit"]#save-post').not('.find-box input');
    
    if ( ! button.is( '#bulk_edit' ) ) 
    {
        if ( ! button.attr( 'id' ) )
            button.first().attr( 'id','tpb_publish' );
        button.first().duplicateButton( 'wpuxss-tpb-publish' );
    }
    draft_button.first().duplicateButton( 'wpuxss-tpb-save-post' );
    
    $('#wpadminbar .wpuxss-tpb a').click(function(e) 
    {
        e.preventDefault();
        $('#'+$(this).attr('for')).click();
    });		

})( jQuery );