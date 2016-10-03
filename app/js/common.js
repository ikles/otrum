jQuery(document).ready(function($){

	$('.lang-inline li:first-child a').text('Eng');
	$('.lang-inline li:nth-child(2) a').text('Укр');
	$('.lang-inline li:nth-child(3) a').text('Рус');


	$('.mnwall-readmore a').text('Подробнее');

	
		var he = $('body').outerHeight(true);
	


	$('.uk-overlay.uk-overlay-hover .uk-position-cover').click(function (eventObject) {
		eventObject.preventDefault();
		var hrefka = $(this).attr('href');
		/*$('#yt_wrapper').after('<div class="zoom"><a href="#" class="closer"></a><img src="'+hrefka+'"></div>');*/
		$('.zoom a').after('<img src="'+hrefka+'">');
		$('.zoom').addClass('dblock');
		$('.overka').addClass('dblock');
		$('.closer').addClass('dblock');
	});


	$('.overka').click(function () {
		$('.overka').removeClass('dblock');
		$('.zoom').removeClass('dblock');
		$('.zoom img').remove();
	});

	$('.closer').click(function (eventObject) {
		eventObject.preventDefault();
		$('.overka').removeClass('dblock');
		$('.zoom').removeClass('dblock');
		$('.zoom img').remove();
	});

	

}); //ready