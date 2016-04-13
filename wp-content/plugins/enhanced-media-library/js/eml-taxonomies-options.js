( function( $ ) {

    var orderValue,
        l10n = wpuxss_eml_taxonomies_options_l10n_data;



    // remove taxonomy
    $( document ).on( 'click', 'li .wpuxss-eml-button-remove', function() {

        target = $(this).parent();

        if ( target.hasClass( 'wpuxss-eml-clone-taxonomy' ) )
        {
            target.hide( 300, function() {
                $(this).remove();
            });
        }
        else
        {
            if ( confirm( l10n.tax_deletion_confirm ) )
            {
                target.hide( 300, function() {
                    $(this).remove();
                });
            }
        }

        return false;
    });



    // create new taxonomy
    $(document).on( 'click', '.wpuxss-eml-button-create-taxonomy', function()
    {
        $('.wpuxss-eml-media-taxonomy-list').find('.wpuxss-eml-clone').clone().attr('class','wpuxss-eml-clone-taxonomy').appendTo('.wpuxss-eml-media-taxonomy-list').show(300);

        return false;
    });



    // edit taxonomy parameters
    $(document).on( 'click', '.wpuxss-eml-button-edit', function() {

        $(this).parent().find('.wpuxss-eml-taxonomy-edit').toggle(300);

        $(this).html(function(i, html)
        {
            return html == l10n.edit+' \u2193' ? l10n.close+' \u2191' : l10n.edit+' \u2193';
        });

        return false;
    });



    // on change of a singular taxonomy name during creation
    $(document).on( 'blur', '.wpuxss-eml-clone-taxonomy .wpuxss-eml-singular_name', function()
    {
        var dictionary,
        taxonomy_name = $(this).val().toLowerCase(),
        taxonomy_edit_box = $(this).parents('.wpuxss-eml-taxonomy-edit');

        taxonomy_name = taxonomy_name.replace(/(<([^>]+)>)/g,'');

        if ( taxonomy_name != '' )
        {
            // thanks to
            // https://github.com/borodean/jquery-translit
            // https://gist.github.com/richardsweeney/5317392
            // http://www.advancedcustomfields.com/
            // for the 'dictionary' code!
            dictionary = {
                'а': 'a',
                'б': 'b',
                'в': 'v',
                'г': 'g',
                'д': 'd',
                'е': 'e',
                'ё': 'e',
                'ж': 'zh',
                'з': 'z',
                'и': 'i',
                'й': 'i',
                'к': 'k',
                'л': 'l',
                'м': 'm',
                'н': 'n',
                'о': 'o',
                'п': 'p',
                'р': 'r',
                'с': 's',
                'т': 't',
                'у': 'u',
                'ф': 'f',
                'х': 'kh',
                'ц': 'tc',
                'ч': 'ch',
                'ш': 'sh',
                'щ': 'shch',
                'ъ': '',
                'ы': 'y',
                'ь': '',
                'э': 'e',
                'ю': 'iu',
                'я': 'ia',
                'ä': 'a',
                'æ': 'a',
                'å': 'a',
                'ö': 'o',
                'ø': 'o',
                'é': 'e',
                'ë': 'e',
                'ü': 'u',
                'ó': 'o',
                'ő': 'o',
                'ú': 'u',
                'é': 'e',
                'á': 'a',
                'ű': 'u',
                'í': 'i',
                ' ' : '_',
                '-' : '_',
                '\'' : '',
                '&' : '_'
            };

            $.each( dictionary, function(k, v)
            {
                var regex = new RegExp( k, 'g' );
                taxonomy_name = taxonomy_name.replace( regex, v );
            });

            taxonomy_name = taxonomy_name.replace(/[^a-z0-9_\s]/g, '');

            $(this).closest('.wpuxss-eml-clone-taxonomy').attr('id',taxonomy_name);

            if ( $('.wpuxss-eml-clone-taxonomy[id='+taxonomy_name+'], .wpuxss-eml-taxonomy[id='+taxonomy_name+'], .wpuxss-non-eml-taxonomy[id='+taxonomy_name+']').length > 1 )
            {
                alert(l10n.tax_error_duplicate);
            }

            $(this).attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][labels][singular_name]');
            taxonomy_edit_box.find('.wpuxss-eml-name').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][labels][name]');
            taxonomy_edit_box.find('.wpuxss-eml-menu_name').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][labels][menu_name]');
            taxonomy_edit_box.find('.wpuxss-eml-all_items').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][labels][all_items]');
            taxonomy_edit_box.find('.wpuxss-eml-edit_item').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][labels][edit_item]');
            taxonomy_edit_box.find('.wpuxss-eml-view_item').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][labels][view_item]');
            taxonomy_edit_box.find('.wpuxss-eml-update_item').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][labels][update_item]');
            taxonomy_edit_box.find('.wpuxss-eml-add_new_item').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][labels][add_new_item]');
            taxonomy_edit_box.find('.wpuxss-eml-new_item_name').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][labels][new_item_name]');
            taxonomy_edit_box.find('.wpuxss-eml-parent_item').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][labels][parent_item]');
            taxonomy_edit_box.find('.wpuxss-eml-search_items').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][labels][search_items]');

            if( taxonomy_edit_box.find('.wpuxss-eml-edit_item').val() == '' )
                taxonomy_edit_box.find('.wpuxss-eml-edit_item').val(l10n.edit+' '+$(this).val());

            if( taxonomy_edit_box.find('.wpuxss-eml-view_item').val() == '' )
                taxonomy_edit_box.find('.wpuxss-eml-view_item').val(l10n.view+' '+$(this).val());

            if( taxonomy_edit_box.find('.wpuxss-eml-update_item').val() == '' )
                taxonomy_edit_box.find('.wpuxss-eml-update_item').val(l10n.update+' '+$(this).val());

            if( taxonomy_edit_box.find('.wpuxss-eml-add_new_item').val() == '' )
                taxonomy_edit_box.find('.wpuxss-eml-add_new_item').val(l10n.add_new+' '+$(this).val());

            if( taxonomy_edit_box.find('.wpuxss-eml-new_item_name').val() == '' )
                taxonomy_edit_box.find('.wpuxss-eml-new_item_name').val(l10n.new+' '+$(this).val()+' '+l10n.name);

            if( taxonomy_edit_box.find('.wpuxss-eml-parent_item').val() == '' )
                taxonomy_edit_box.find('.wpuxss-eml-parent_item').val(l10n.parent+' '+$(this).val());

            taxonomy_edit_box.find('.wpuxss-eml-taxonomy-name').val(taxonomy_name);

            taxonomy_edit_box.find('.wpuxss-eml-hierarchical').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][hierarchical]');
            taxonomy_edit_box.find('.wpuxss-eml-public').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][public]');
            taxonomy_edit_box.find('.wpuxss-eml-show_admin_column').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][show_admin_column]');
            taxonomy_edit_box.find('.wpuxss-eml-show_in_nav_menus').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][show_in_nav_menus]');
            taxonomy_edit_box.find('.wpuxss-eml-sort').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][sort]');
            taxonomy_edit_box.find('.wpuxss-eml-slug').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][rewrite][slug]').val(taxonomy_name);

            taxonomy_edit_box.find('.wpuxss-eml-admin_filter').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][admin_filter]');
            taxonomy_edit_box.find('.wpuxss-eml-media_uploader_filter').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][media_uploader_filter]');
            taxonomy_edit_box.find('.wpuxss-eml-media_popup_taxonomy_edit').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][media_popup_taxonomy_edit]');

            $(this).closest('.wpuxss-eml-clone-taxonomy').find('.wpuxss-eml-assigned').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][assigned]');
            $(this).closest('.wpuxss-eml-clone-taxonomy').find('.wpuxss-eml-eml_media').attr('name','wpuxss_eml_taxonomies['+taxonomy_name+'][eml_media]');
        }
        else
        {
            $(this).val('');
            taxonomy_edit_box.find('.wpuxss-eml-edit_item').val('');
            taxonomy_edit_box.find('.wpuxss-eml-view_item').val('');
            taxonomy_edit_box.find('.wpuxss-eml-update_item').val('');
            taxonomy_edit_box.find('.wpuxss-eml-add_new_item').val('');
            taxonomy_edit_box.find('.wpuxss-eml-new_item_name').val('');
            taxonomy_edit_box.find('.wpuxss-eml-parent_item').val('');
            taxonomy_edit_box.find('.wpuxss-eml-slug').val('');
        }
    });



    // on change of a plural taxonomy name during creation
    $(document).on( 'blur', '.wpuxss-eml-clone-taxonomy .wpuxss-eml-name', function()
    {
        var taxonomy_plural_name =  $(this).val();
        taxonomy_edit_box = $(this).parents('.wpuxss-eml-taxonomy-edit');

        taxonomy_plural_name = taxonomy_plural_name.replace(/(<([^>]+)>)/g,'');

        if ( taxonomy_plural_name != '' )
        {
            if( taxonomy_edit_box.find('.wpuxss-eml-menu_name').val() == '' )
                taxonomy_edit_box.find('.wpuxss-eml-menu_name').val($(this).val());

            if( taxonomy_edit_box.find('.wpuxss-eml-all_items').val() == '' )
                taxonomy_edit_box.find('.wpuxss-eml-all_items').val(l10n.all+' '+$(this).val());

            if( taxonomy_edit_box.find('.wpuxss-eml-search_items').val() == '' )
                taxonomy_edit_box.find('.wpuxss-eml-search_items').val(l10n.search+' '+$(this).val());

            $(this).closest('.wpuxss-eml-clone-taxonomy').find('.wpuxss-eml-taxonomy-label').text($(this).val());
        }
        else
        {
            $(this).val('');
            taxonomy_edit_box.find('.wpuxss-eml-menu_name').val('');
            taxonomy_edit_box.find('.wpuxss-eml-all_items').val('');
            taxonomy_edit_box.find('.wpuxss-eml-search_items').val('');
            $(this).closest('.wpuxss-eml-clone-taxonomy').find('.wpuxss-eml-taxonomy-label').text(l10n.tax_new);
        }
    });



    // on taxonomy form submit
    $('#wpuxss-eml-form-taxonomies').submit(function( event )
    {
        submit_it = true;
        alert_text = '';

        $('.wpuxss-eml-clone-taxonomy, .wpuxss-eml-taxonomy').each(function( index )
        {
            current_taxonomy = $(this).attr('id');

            if ( !$('.wpuxss-eml-singular_name',this).val() && !$('.wpuxss-eml-name',this).val() )
            {
                submit_it = false;
                alert_text = l10n.tax_error_empty_both;
            }
            else if ( !$('.wpuxss-eml-singular_name',this).val() )
            {
                submit_it = false;
                alert_text = l10n.tax_error_empty_singular;
            }
            else if ( !$('.wpuxss-eml-name',this).val() )
            {
                submit_it = false;
                alert_text = l10n.tax_error_empty_plural;
            }
            else if ( $('.wpuxss-eml-clone-taxonomy[id='+current_taxonomy+'], .wpuxss-eml-taxonomy[id='+current_taxonomy+'], .wpuxss-non-eml-taxonomy[id='+current_taxonomy+']').length > 1 )
            {
                submit_it = false;
                alert_text = l10n.tax_error_duplicate;
            }
        });

        if ( !submit_it ) alert(alert_text);

        return submit_it;
    });



    $( document ).ready( function() {

        orderValue = $('#wpuxss_eml_tax_options_media_order').val();
        $('#wpuxss_eml_tax_options_media_orderby').change();
    });



    $( document ).on( 'change', '#wpuxss_eml_tax_options_media_orderby', function( event ) {

        var isMenuOrder = 'menuOrder' === $( event.target ).val(),
            value;

        orderValue = isMenuOrder ? $('#wpuxss_eml_tax_options_media_order').val() : orderValue;
        value = isMenuOrder ? 'ASC' : orderValue;

        $('#wpuxss_eml_tax_options_media_order').prop( 'disabled', isMenuOrder ).val( value );
    });



    // synchronize parent terms to media items (PRO)
    $( document ).on( 'click', '.eml-button-synchronize-terms', function( event ) {

        var $el, post_type, taxonomy;


        emlConfirmDialog( l10n.sync_warning_title, l10n.sync_warning_text, l10n.sync_warning_yes, l10n.sync_warning_no, 'button button-primary' )
        .done( function() {

            $el = $( event.target );

            post_type = $el.attr( 'data-post-type' );
            taxonomy = $el.attr( 'data-taxonomy' );

            emlFullscreenSpinnerStart( l10n.in_progress_sync_text );

            $.post( ajaxurl, {
                nonce: l10n.bulk_edit_nonce,
                action: 'eml-synchronize-terms',
                post_type: post_type,
                taxonomy: taxonomy
            },function( response ) {
                emlFullscreenSpinnerStop();
    		});
        })
        .fail(function() {
            return;
        });
    });

})( jQuery );
