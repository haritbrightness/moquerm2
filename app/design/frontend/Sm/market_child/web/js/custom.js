/*Custom Script*/
require(['jquery', "Amasty_Base/vendor/slick/slick.min"], function (jQuery) {
    (function ($) {
        
        $('#amrelated-block-5 [data-amrelated-js="slider"]').slick({
            dots:true,
            infinite: true,
            slidesToShow: 3,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1280,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                },
                {
                    breakpoint: 425,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
        
        
        
       
    	/*---------- Responsive table Start ----------*/       

		$('.table-responsive').each(function() {
			var tablesResp_t = [];
			$(this).find("th").each(function() {
				/* push every header into the array as text by first removing its children*/
				tablesResp_t.push($(this).clone().children().remove().end().text().trim());
			});
			$(this).find("tr").each(function() {
				/* put every header into td's before pseudoelement*/
				for (var tablesResp_r = $(this), tablesResp_i = 0; tablesResp_i < tablesResp_t.length; tablesResp_i++) {
					tablesResp_r.find("td").eq(tablesResp_i).attr("data-th", tablesResp_t[tablesResp_i]);
				}
			});
		});
	})(jQuery);
});