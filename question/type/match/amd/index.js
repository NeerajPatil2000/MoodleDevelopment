/* eslint-disable no-console */
require(['jquery'], function($) {
    $(window).on('load', (function() {
// Finding all the <div></div> elements with the class matching which was added by me.
// These div tags enclose the custom dropdown created by other team.
        window.console.log($('.MathJax_Display'));
        $('.matching').each(function(i, d) {
// Finding the button inside this div tag.
            var b = $(d).find('button');

// Getting the corresponding original drop-down.
            var c = $(d).attr('class').split(' ')[2];
// If the original drop-down is disabled, the disabling the custom drop-down.
            if ($('select.' + c).is(':disabled')) {
                $(b).attr('disabled', 'disabled');
                $('.MathJax_Display').attr('style', 'display:inline !important');
            } else {
// This code is created based on other team code with some more additions.
                $(this).find('li').click(function() {
                    $('.MathJax_Display').attr('style', 'display:inline !important');
                    $(b).html($(this).html());
                    var v = $(this).attr('value');
                    // eslint-disable-next-line no-unused-expressions
                    $('select.' + c).find('option:selected').removeAttr('selected');
                    var opt = $('select.' + c).find('option')[v];
                    $(opt).attr('selected', 'selected');
                    $(d).find('[selected=selected]').removeAttr('selected');
                    $(this).attr('selected', 'selected');
                });
            }
            // Changing the html of the button tag to the html inside the selected list tag
            $(b).html($(d).find('[selected=selected]').html());
        });
    })
    );
});