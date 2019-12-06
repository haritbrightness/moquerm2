require([
  'jquery', 'Sm_ListingTabs/js/owl.carousel'
], function (jQuery) {
  (function ($) {

    $(document).ready(function () {

      // $(document).on('click', '.product-reviews-summary .reviews-actions a', function (e) {
      //   $("#tab-label-reviews").addClass("active").siblings().removeClass("active");
      //   $("#reviews").css("display", "block").siblings().css("display", "none");
      //   if (
      //     location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '')
      //     &&
      //     location.hostname == this.hostname
      //   ) {
      //     // Figure out element to scroll to
      //     var target = $(this.hash);
      //     target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
      //     // Does a scroll target exist?
      //     if (target.length) {
      //       // Only prevent default if animation is actually gonna happen
      //       event.preventDefault();
      //       $('html, body').animate({
      //         scrollTop: target.offset().top
      //       }, 1000, function () {
      //         // Callback after animation
      //         // Must change focus!
      //         var $target = $(target);
      //         $target.focus();
      //         if ($target.is(":focus")) { // Checking if the target was focused
      //           return false;
      //         } else {
      //           $target.attr('tabindex', '-1'); // Adding tabindex for elements not focusable
      //           $target.focus(); // Set focus again
      //         };
      //       });
      //     }
      //   }
      // });

      // $('a[href*="#"]')
      //   // Remove links that don't actually link to anything
      //   .not('[href="#"]')
      //   .not('[href="#0"]')
      //   .click(function (event) {
      //     // On-page links
      //     if (
      //       location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '')
      //       &&
      //       location.hostname == this.hostname
      //     ) {
      //       // Figure out element to scroll to
      //       var target = $(this.hash);
      //       target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
      //       // Does a scroll target exist?
      //       if (target.length) {
      //         // Only prevent default if animation is actually gonna happen
      //         event.preventDefault();
      //         $('html, body').animate({
      //           scrollTop: target.offset().top
      //         }, 1000, function () {
      //           // Callback after animation
      //           // Must change focus!
      //           var $target = $(target);
      //           $target.focus();
      //           if ($target.is(":focus")) { // Checking if the target was focused
      //             return false;
      //           } else {
      //             $target.attr('tabindex', '-1'); // Adding tabindex for elements not focusable
      //             $target.focus(); // Set focus again
      //           };
      //         });
      //       }
      //     }
      //   });

      // $(".resp-tabs-list li22").on("click", function(e){

      //      $("html, body").stop().animate({
      //        scrollTop: $(".resp-tabs-list").offset().top
      //     }, 600);
      //     return false;
      // });

      // jQuery(".product.info.detailed .resp-tabs-list li").bind('click', function (e) {
      //   e.stopPropagation();
      // });
      // $(".data.switch55").on("click", function(e){

      // e.preventDefault();              

      /* $("html, body").animate({
         scrollTop: $(".resp-tabs-list").offset().top
      }, 600);
      return false;*/
      // });

      /* $(document).on('click', '.data.switch', function (event) {
           event.stopPropagation();     
           
           $('html, body').animate({
               scrollTop: $($(this).attr('href')).offset().top 
           }, 1000, 'linear');                     
       });*/

      $(".header-search-mobile-btn").on("click", function (e) {
        $(this).parent().toggleClass("open");
      });

      $(".filter .filter-options-title").on("click", function (e) {
        $(this).toggleClass("filter-open").siblings().removeClass("filter-open");
        var $this = $(this);

        if ($this.next().hasClass('show')) {
          $this.next().removeClass('show');
          $this.next().slideUp(350);
        } else {
          $this.parent().find('.filter-options-content').removeClass('show');
          $this.parent().find('.filter-options-content').slideUp(350);
          $this.next().toggleClass('show');
          $this.next().slideToggle(350);
        }
      });
      
      $('.our-recommendations-slider').on('initialized.owl.carousel resized.owl.carousel', function(e) {
            $(e.target).toggleClass('hide-nav', e.item.count <= e.page.size);
      });
      $('.sm_megamenu_col_2 .our-recommendations-slider').owlCarousel({
        loop: false,
        margin: 10,
        dots: false,
        responsiveClass: true,
        responsive: {
          0: {
            items: 1,
            nav: true,
            dots: false
          },
          600: {
            items: 1,
            nav: false,
            dots: false
          },
          1000: {
            items: 2,
            nav: true,
            dots: false,
            loop: true
          }
        }
      });
      
      $('.sm_megamenu_col_4 .our-recommendations-slider').owlCarousel({
        loop: false,
        margin: 10,
        dots: false,
        responsiveClass: true,
        responsive: {
          0: {
            items: 1,
            nav: true,
            dots: false
          },
          600: {
            items: 1,
            nav: false,
            dots: false
          },
          1000: {
            items: 4,
            nav: true,
            dots: false,
            loop: true
          }
        }
      });    
      
      equalheight = function (container) {

        var currentTallest = 0,
          currentRowStart = 0,
          rowDivs = new Array(),
          $el,
          topPosition = 0;
        $(container).each(function () {

          $el = $(this);
          $($el).height('auto')
          topPostion = $el.position().top;

          if (currentRowStart != topPostion) {
            for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
              rowDivs[currentDiv].height(currentTallest);
            }
            rowDivs.length = 0; // empty the array
            currentRowStart = topPostion;
            currentTallest = $el.height();
            rowDivs.push($el);
          } else {
            rowDivs.push($el);
            currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
          }
          for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
            rowDivs[currentDiv].height(currentTallest);
          }
        });
      }

    });/*--document ready close--*/

    $(window).load(function () {
      setTimeout(function () {
        equalheight('.product-item-details-wrap');
          equalheight('.payment-country-option-block');
      }, 3000);
    });


    $(window).resize(function () {
      setTimeout(function () {
        equalheight('.product-item-details-wrap');
          equalheight('.payment-country-option-block');
      }, 3000);
    });

  })(jQuery);
});