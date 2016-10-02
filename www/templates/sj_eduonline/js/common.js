jQuery(document).ready(function($){

	$('.lang-inline li:first-child a').text('Eng');
	$('.lang-inline li:nth-child(2) a').text('Укр');
	$('.lang-inline li:nth-child(3) a').text('Рус');


	$('.mnwall-readmore a').text('Подробнее');


	var he = $('body').outerHeight(true);
	


	$('.uk-overlay.uk-overlay-hover .uk-position-cover').click(function (eventObject) {
		eventObject.preventDefault();
		var hrefka = $(this).attr('href');
		$('#yt_wrapper').after('<div class="zoom"><img src="'+hrefka+'"></div>');
		$('.overka').css({'display':'block','height':he});
	});


	$('.overka').click(function () {
		$('.overka').fadeOut();
		$('.zoom').fadeOut();
	});

	$('.zoom').click(function () {
		$('.overka').fadeOut();
		$('.zoom').fadeOut();
	});

	

	

}); //ready