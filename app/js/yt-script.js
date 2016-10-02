// JavaScript Document
function createCookie(name,value,days) {
  if (days) {
    var date = new Date();
    date.setTime(date.getTime()+(days*24*60*60*1000));
    var expires = "; expires="+date.toGMTString();
  }
  else expires = "";
  document.cookie = name+"="+value+expires+"; path=/";
}

function getCookie(c_name, defaultvalue){	//alert(document.cookie);
	var i,x,y,arrcookies=document.cookie.split(";");
	for (i=0;i<arrcookies.length;i++){
	  x=arrcookies[i].substr(0,arrcookies[i].indexOf("="));
	  y=arrcookies[i].substr(arrcookies[i].indexOf("=")+1);
	  x=x.replace(/^\s+|\s+$/g,"");
	  if (x==c_name){
		  return unescape(y);
	  }
	}
	return defaultvalue;
}

jQuery(document).ready(function() {
	// Begin: Show hide mega navigator
	var ua = navigator.userAgent;
	event = (ua.match(/iPad/i)) ? 'touchstart' : 'click';
	jQuery('.btn-meganavigator').bind(event, function() {
		jQuery('#top1').animate({opacity:'0'});
		jQuery('#top2').animate({opacity:'0'});
		jQuery('#topsearch').animate({opacity:'0'});
		jQuery(this).animate({opacity:'0'},function(){
			jQuery('#meganavigator').animate({top:'0px'},300);
		  });
	});
	jQuery('.meganavigator-close').bind(event, function() {
		jQuery('#meganavigator').animate({top:'-100%'},200,function(){
			jQuery('.btn-meganavigator').animate({opacity:'1'},200);
			jQuery('#top1').animate({opacity:'1'},200);
			jQuery('#top2').animate({opacity:'1'},200);
			jQuery('#topsearch').animate({opacity:'1'},200);
		  });
	});

});



