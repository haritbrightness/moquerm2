define([
    'jquery',
    'mage/utils/wrapper',
    'Swissup_Firecheckout/js/model/layout',
    'Swissup_Firecheckout/js/utils/harlem'
], function ($, wrapper, layout, harlem) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return function (target) {
        if (!checkoutConfig || !checkoutConfig.isFirecheckout) {
            return target;
        }

        target.handleHash = wrapper.wrap(
            target.handleHash,
            function (original) {
                var hashString = window.location.hash.replace('#', '');

                if (hashString === 'cart') {
                    return;
                }

                return original();
            }
        );

        target.setHash = wrapper.wrap(
            target.setHash ? target.setHash : function () {},
            function (original, hash) {
                if (hash === 'cart') { // see js/plugin/step-cart.js
                    hash = this.steps().sort(this.sortItems)[1].code;
                }

                return original(hash);
            }
        );

        target.registerStep = wrapper.wrap(
            target.registerStep,
            function (originalAction, code, alias, title, isVisible, navigate, sortOrder) {
                arguments[4] = wrapper.wrap(
                    isVisible,
                    function (o, flag) {
                        if (!layout.isMultistep()) {
                            // expand all steps
                            flag = true;
                            o(flag);
                        } else if (typeof flag === 'undefined') {
                            flag = o();
                        } else {
                            o(flag);
                        }

                        $('body').toggleClass('fc-step-' + code, flag);

                        return flag;
                    }
                );

                return originalAction.apply(
                    target,
                    Array.prototype.slice.call(arguments, 1)
                );
            }
        );

        target.navigateTo = wrapper.wrap(
            target.navigateTo,
            function (originalAction, code, scrollToElementId) {
                var scrollTo = $('#' + code);

                if (code !== 'cart') {
                    if (scrollToElementId && $('#' + scrollToElementId).length) {
                        scrollTo = $('#' + scrollToElementId);
                    }

                    // parent logic will not scroll the viewport because of
                    // isProcessed check. Scroll it by ourself.
                    if (!target.isProcessed(code)) {
                        $('body, html').animate({
                            scrollTop: scrollTo.offset().top - 20
                        }, 200, function () {
                            harlem.shake(scrollTo.find('.step-title'));
                        });
                    }

                    return originalAction(code, scrollToElementId);
                }

                target.steps().forEach(function (element) {
                    if (element.code === code) {
                        element.navigate();
                    }
                });
            }
        );

        return target;
    };
});
