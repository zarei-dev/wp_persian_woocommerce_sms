jQuery(document).ready(function () {
    if (jQuery().select2)
        jQuery('.pwoosms_tab_status').select2();
    jQuery('#add_another_sms_tab').on('click', function (e) {
        if (jQuery().select2)
            jQuery('.pwoosms_tab_status').select2("destroy");
        var clone_container = jQuery('#duplicate_this_row_sms');
        var new_count = parseInt(jQuery('#sms_tab_counter').val()) + 1;
        var remove_tab_button = jQuery('#duplicate_this_row_sms .sms_tab_counter');
        var move_tab_content_buttons = jQuery('#duplicate_this_row_sms .button-holder-sms');
        clone_container.children('p').each(function () {
            jQuery(this).clone().insertBefore('#duplicate_this_row_sms').removeClass('hidden_duplicator_row_title_field_sms').removeClass('hidden_duplicator_row_content_field_sms').addClass('new_duplicate_row_sms');
        }).promise().done(function () {
            var duplicate = jQuery('.new_duplicate_row_sms');
            duplicate.find('input').each(function () {
                if (jQuery(this).is('input[name="hidden_duplicator_row_mobile"]')) {
                    jQuery(this).attr('name', 'pwoosms_tab_mobile_' + new_count).attr('id', 'pwoosms_tab_mobile_' + new_count).parents('p').addClass('pwoosms_tab_mobile_' + new_count + '_field').removeClass('hidden_duplicator_row_title_field_sms').find('label').removeAttr('for').attr('for', 'pwoosms_tab_mobile_' + new_count + '_field');
                }
            });
            duplicate.find('select').each(function () {
                if (jQuery(this).is('select[name="hidden_duplicator_row_statuses[]"]')) {
                    jQuery(this).attr('name', 'pwoosms_tab_status_' + new_count + '[]').attr('id', 'pwoosms_tab_status_' + new_count).parents('p').addClass('pwoosms_tab_status_' + new_count + '_field').removeClass('hidden_duplicator_row_content_field_sms').find('label').removeAttr('for').attr('for', 'pwoosms_tab_status_' + new_count + '_field');
                }
            });
            jQuery('#sms_tab_counter').val(new_count);
            duplicate.first().before('<div class="pwoosms-tab-divider"></div>');
        });
        move_tab_content_buttons.clone().insertAfter(jQuery('.pwoosms-tab-divider').last()).addClass('last-button-holder-sms');
        remove_tab_button.clone().prependTo('.last-button-holder-sms').removeAttr('style');
        jQuery('.button-holder-sms').first().prev('.pwoosms-tab-divider').hide();
        jQuery('.last-button-holder-sms').removeAttr('alt').attr('alt', new_count);
        setTimeout(function () {
            jQuery('.last-button-holder-sms').removeClass('last-button-holder-sms');
            jQuery('.new_duplicate_row_sms').removeClass('new_duplicate_row_sms');
        }, 100);
        if (jQuery().select2)
            jQuery('.pwoosms_tab_status').select2();
        e.preventDefault();
    });
    // end duplicate tab

    /*
        Remove a new tab
    */
    jQuery('body').on('click', '.sms_tab_counter', function (e) {
        var clicked_parent = jQuery(this).parents('.button-holder-sms');
        var tab_title_to_remove = clicked_parent.next();
        var tab_content_to_remove = tab_title_to_remove.next();
        var divider_to_remove_next = tab_content_to_remove.next('.pwoosms-tab-divider');
        var divider_to_remove_prev = clicked_parent.prev('.pwoosms-tab-divider');
        tab_title_to_remove.remove();
        tab_content_to_remove.remove();
        divider_to_remove_prev.remove();
        divider_to_remove_next.remove();
        clicked_parent.remove();
        e.preventDefault();
    });
    // end remove

    jQuery('.pwoosms-tab-help-toggle').on('click', function () {
        jQuery('.pwoosms-tab-help').fadeToggle();
    }).show();
});