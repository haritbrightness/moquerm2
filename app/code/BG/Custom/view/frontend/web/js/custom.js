require([
  'jquery', 'Sm_ListingTabs/js/owl.carousel'
], function (jQuery) {
  (function ($) {

    $(document).ready(function () {
        
        $(function() {
              $('.reviews-actions a').click(function() {              
                if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
                  var target = $(this.hash);
                  target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
                   $("#tab-label-reviews").addClass("active").siblings().removeClass("active");          
                   $(".resp-tab-content").hide();
                   $(".resp-tab-content#reviews").show(); 
                  if (target.length) {
                    $('html,body').animate({
                      scrollTop: target.offset().top
                    }, 500);
                    return false;
                  }
                }
              });
        });            

      $(".header-search-mobile-btn").on("click", function (e) {
        $(this).parent().toggleClass("open");
      });

      $(".filter .filter-options-title1").on("touchstart", function(e) {
          alert("DS");
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
      $('.slider-products').on('initialized.owl.carousel resized.owl.carousel', function(e) {
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