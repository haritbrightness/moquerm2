define([
    'jquery',
    'underscore',
    'Swissup_Checkout/js/is-in-viewport'
], function ($, _, isInViewport) {
    'use strict';

    var debouncedScroll = _.debounce(function () {
        var selector = [
                '.modal-content div.mage-error:visible',
                'div.field-error:visible',
                'div.mage-error:visible',
                '.firecheckout-msg:visible',
                'div.message-error:visible'
            ].join(', '),
            messages,
            timeout = 0,
            visibleMessage;

        messages = $(selector).filter(function () {
            return $(this).css('visibility') === 'visible';
        });

        if (!messages.length) {
            return;
        }

        visibleMessage = messages.toArray().find(isInViewport);

        if (!visibleMessage) {
            visibleMessage = messages.first();
            timeout = 200;
            $('html, body').animate({
                scrollTop: visibleMessage.offset().top - 70
            }, timeout);
        }

        setTimeout(function () {
            $(visibleMessage).addClass('firecheckout-shake')
                .one(
                    'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend',
                    function () {
                        $(this).removeClass('firecheckout-shake');
                    }
                );
        }, timeout);
    }, 300);

    return debouncedScroll;
});
