jQuery(document).ready(function($){

	$('.lang-inline li:first-child a').text('Eng');
	$('.lang-inline li:nth-child(2) a').text('Укр');
	$('.lang-inline li:nth-child(3) a').text('Рус');


	$('.mnwall-readmore a').text('Подробнее');




	$('.uk-overlay.uk-overlay-hover .uk-position-cover').click(function (eventObject) {
		eventObject.preventDefault();
		alert($(this).attr('href'));
	});



	

	

}); //ready