jQuery.noConflict();

jQuery(function(){
	handle_side_menu();
	handle_toolbar();
	responsiveSidebar();
	checkWidgetName();
	widgetPageVerification();
	checkGridRadio();
	//createGridRadioWall();
	checkScrollerRadio();
	selectScroller();
});

function getUrlParameter(sParam)
{
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) 
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) 
        {
            return sParameterName[1];
        }
    }
}          

function handle_toolbar()
{
	jQuery('.widget-content .page-header').show();
	jQuery('h1.page-title').appendTo('.page-header').addClass('pull-left').find('span').remove();
	jQuery('#toolbar').appendTo('.page-header').addClass('pull-right');
	jQuery('#toolbar-new-module').appendTo('#toolbar').css({"display":"inline-block"});
	jQuery('#toolbar-new-custom').prependTo('#toolbar');
}

function handle_side_menu()
{
	jQuery("#menu-toggler").on("click",function(){
		jQuery("#mw-sidebar").toggleClass("display");
		jQuery(this).toggleClass("display");
		return false
	});
	var b=jQuery("#mw-sidebar").hasClass("menu-min");
	jQuery("#sidebar-collapse").on("click",function(){
		jQuery("#mw-sidebar").toggleClass("menu-min");
		jQuery(this).find('[class*="fa-"]:eq(0)').toggleClass("fa-angle-double-right");
		b=jQuery("#mw-sidebar").hasClass("menu-min");
		if(b){
			jQuery(".open > .submenu").removeClass("open")
		}
	});
	var a="ontouchend" in document;
	jQuery(".nav-list").on("click",function(g){
		var f=jQuery(g.target).closest("a");
		if(!f||f.length==0){
			return
		}
		if(!f.hasClass("dropdown-toggle")){
			//if(b&&ace.click_event=="tap"&&f.get(0).parentNode.parentNode==this){
			if(b&&f.get(0).parentNode.parentNode==this){
				var h=f.find(".menu-text").get(0);
				if(g.target!=h&&!jQuery.contains(h,g.target)){
					return false
				}
			}
			return
		}
		var d=f.next().get(0);
		if(!jQuery(d).is(":visible")){
			var c=jQuery(d.parentNode).closest("ul");
			if(b&&c.hasClass("nav-list")){
				return
			}
			c.find("> .open > .submenu").each(function(){
				if(this!=d&&!jQuery(this.parentNode).hasClass("active")){
					jQuery(this).slideUp(200).parent().removeClass("open")
				}
			})
		}else{
		}
		if(b&&jQuery(d.parentNode.parentNode).hasClass("nav-list")){
			return false
		}
		jQuery(d).slideToggle(200).parent().toggleClass("open");
		return false
	});
	if (getUrlParameter('view') == 'dashboard' || !getUrlParameter('view')) {
		jQuery('#mw-sidebar ul li:nth-child(1)').addClass("open");
	}
	if (getUrlParameter('view') == 'widgets' || getUrlParameter('view') == 'widget') {
		jQuery('#mw-sidebar ul li:nth-child(2)').addClass("open");
	}
	if (getUrlParameter('view') == 'about') {
		jQuery('#mw-sidebar ul li:nth-child(4)').addClass("open");
	}
}

function responsiveSidebar()
{
	if (jQuery(window).width() < 690) {
		jQuery('#mw-sidebar').addClass('menu-min');
	   	jQuery('#sidebar-collapse i.fa').addClass('fa-angle-double-right');
	   
	   	jQuery(document).mouseup(function (e)
	   	{ 
	   	   	if (jQuery(window).width() < 690) {
			   	var container = jQuery("#mw-sidebar");
			   	if (!container.is(e.target) && container.has(e.target).length === 0)
			   	{
					jQuery('#mw-sidebar').addClass('menu-min');
					jQuery('#sidebar-collapse i.fa').addClass('fa-angle-double-right'); 
			   	}
		   	}
	   	});
	}
	else 
	{
	   jQuery('#mw-sidebar').removeClass('menu-min');
	   jQuery('#sidebar-collapse i.fa').removeClass('fa-angle-double-right');
	}
}

function checkWidgetName()
{
	if (jQuery('#jform_name').val() == '')
	{
    	jQuery('#jform_name').val('widget '+jQuery.now()); 
   	}	
	jQuery('#jform_name').focus(function() {
		if (jQuery('#jform_name').val() == '')
		{
			jQuery('#jform_name').val('widget '+jQuery.now()); 
		}
	});
	jQuery('#jform_name').blur(function() {
		if (jQuery('#jform_name').val() == '')
		{
			jQuery('#jform_name').val('widget '+jQuery.now()); 
		}
	});
}

function widgetPageVerification()
{
	var view = getUrlParameter('view');
	if (view !== undefined && view == 'widget')
	{
		jQuery('#widget-form').areYouSure();			
	}
}

function checkGridRadio()
{
	jQuery('.grid-radio-input:checked').parents('.grid-radio').addClass('active');
	
	jQuery('.grid-radio-input').change(function() {     
		jQuery(this).parents('.controls').find('.grid-radio').removeClass('active');
	  	var checked = jQuery(this).attr('checked', true);
	  	if(checked){ 
			jQuery(this).parents('.grid-radio').addClass('active');
	  	}
	});
}

/*function createGridRadioWall()
{
	jQuery("#widget-form #myTabTabs").on("click", 'a', function()
	{
		jQuery('#masonry_layout').hide();
	});
	
	jQuery("#widget-form #myTabTabs").on("click", 'a[href="#masonry_layout"]', function()
	{
		jQuery('#masonry_layout').show('', function()
		{
 			// Isotope
			jQuery('.grid-radio-demo').isotope({
				// General
				itemSelector: ".grid-wall-item",
				layoutMode: 'packery',
				// Vertical list
				vertical: {
					horizontalAlignment: 0
				}
			});	
		});
	});
}*/

function checkScrollerRadio()
{
	jQuery('.scroller-radio-input:checked').parents('.scroller-radio').addClass('active');
	
	jQuery('.scroller-radio-input').change(function() {     
		jQuery(this).parents('.controls').find('.scroller-radio').removeClass('active');
	  	var checked = jQuery(this).attr('checked', true);
	  	if(checked){ 
			jQuery(this).parents('.scroller-radio').addClass('active');
	  	}
	});
}

function selectScroller()
{
	jQuery(".scroller-radio-actions").on("click", 'button', function()
	{
		jQuery(this).parents('.controls').find('.scroller-radio').removeClass('active');
		jQuery(this).parents('.scroller-radio').find('.scroller-radio-input').attr('checked', 'checked');
		var checked = jQuery(this).parents('.scroller-radio').find('.scroller-radio-input').attr('checked', 'checked');
		if(checked){ 
			jQuery(this).parents('.scroller-radio').addClass('active');
		}
	});
}
	
jQuery(window).resize(function() {
	responsiveSidebar();
});