jQuery(window).load(function() {
	jQuery(".loader-mod-box").fadeOut("slow");
});
// Add Class In Slideshow
function customPager($classAll) {	
	jQuery(".owl-item.active .slider-detail .detail-top").addClass("detail-top-active");
	jQuery(".owl-item.active .slider-detail .detail-title").addClass("detail-title-active");
	jQuery(".owl-item.active .slider-detail .detail-bottom").addClass("detail-bottom-active");
	jQuery(".owl-item.active .slider-detail .detail-button").addClass("detail-button-active");
}