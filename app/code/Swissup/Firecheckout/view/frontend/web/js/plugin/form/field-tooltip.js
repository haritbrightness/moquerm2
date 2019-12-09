define([
    'Magento_Ui/js/lib/view/utils/async',
    'underscore',
    'tippy',
    'Swissup_Checkout/js/is-in-viewport',
    'MutationObserver'
], function ($, _, tippy, isInViewport) {
    'use strict';

    var observer = new MutationObserver(function (mutations) {
        var mutation = mutations[0],
            target;

        switch (mutation.type) {
            case 'childList':
                target = mutation.target;
                break;

            case 'characterData':
                target = mutation.target.parentNode;
                break;

            default:
                return;
        }

        _.debounce(function () {
            $(target).attr('title', $(target).text());
        }, 50)();
    });

    return {
        /**
         * Initialize plugin
         */
        init: function () {
            var self = this,
                selector = [
                    '.field div.field-error',
                    '.field div.mage-error',
                    '.field div.message.warning'
                ].join(', ');

            $.async(selector, function (el) {
                $(el).attr('title', $(el).text());
                $(el).attr('tabindex', 0); // fix clicks the on phones
                tippy(el, {
                    arrow: true,
                    zIndex: 10001,
                    dynamicTitle: true,
                    interactive: true,
                    placement: 'top-end',
                    theme: $(el).hasClass('warning') ? 'warning' : 'error'
                });
                self.addElementObservers(el);
            });

            // transform filed tooltips into tippy elements
            // $.async('.field-tooltip .field-tooltip-action', function (el) {
            //     tippy(el, {
            //         arrow: true,
            //         // dynamicTitle: true,
            //         interactive: true,
            //         html: $(el).next('.field-tooltip-content').get(0),
            //         placement: 'bottom-end',
            //         // size: 'large',
            //         theme: 'content'
            //     });
            // });

            self.addObservers();
        },

        /**
         * Show first tooltip when submitting the form
         */
        addObservers: function () {
            $('body').on('fc:validate fc:validate-shipping-information', function () {
                // give a time 'till the document will be scrolled to the element
                setTimeout(function () {
                    var messages = $('div.field-error:visible, div.mage-error:visible'),
                        visibleMessage = messages.toArray().find(isInViewport),
                        error;

                    if (!visibleMessage) {
                        visibleMessage = messages.first();
                    }

                    error = $(visibleMessage).get(0);

                    if (error && error._tippy) {
                        error._tippy.show();
                    }
                }, 200);
            });
        },

        /**
         * Update tooltip text when message is dynamically changed
         *
         * @param {Element} el
         */
        addElementObservers: function (el) {
            observer.observe(el, {
                childList: true,
                characterData: true,
                subtree: true
            });
        }
    };
});
