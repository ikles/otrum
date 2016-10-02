/****************************
*  createCookie
****************************/
		
function createCookie(cname,cvalue,exdays) {
	if (d) {
		var d = new Date();
		d.setTime(d.getTime()+(exdays*24*60*60*1000));
		var expires = "expires="+d.toGMTString();
	}
	else expires = "";
	document.cookie = cname + "=" + cvalue + "; " + expires;
}


!function ($) {
jQuery(document).ready(function($){
		var joomla3 = 0;
		//Joomla 3 compatibility fix
		if( $('#content').length > 0 ){
			joomla3 = 1;
		}
		
		/*YtTemplateBackend.switchTab();*/
		var ytheight =  window.innerHeight - 150;
		$('.yt-nav-template .nav-tabs').css("min-height",ytheight);
		$('.yt-nav-template .tab-content').css("min-height",ytheight);
		
		$('.nav-tabs .collapse-menu').click(function(){
			$(this).parents(".yt-nav-template").addClass(function(){
				if($(this).hasClass("folded")){
					$(this).removeClass("folded");
					return "";
				}
				return "folded";
			});
			$(this).parent().find(".nav-tabs").slideToggle();
		});
		
		$('.nav-tabs li a').bind('click', function(){
			createCookie(TMPL_BACKEND+'_tab', $(this).attr('href').replace('#', '').replace ('_params', ''), 1);
		});
		
		
		
		/****************************
		*   Accordion Block
		****************************/
		$("ul.yt-accordion li").each(function() {
			if($(this).index() > 0) {
				$(this).children(".accordion-inner").css('display', 'none');
			}
			else {
				$(this).find(".accordion-heading").addClass('active');
			}
		
			$(this).children(".accordion-heading").bind("click", function() {
				$(this).addClass(function() {
					if($(this).hasClass("active")) return "";
					return "active";
				});
		
				$(this).siblings(".accordion-inner").slideDown(350);
				$(this).parent().siblings("li").children(".accordion-inner").slideUp(350);
				$(this).parent().siblings("li").find(".active").removeClass("active");
			});
		});
		
		
	
        
        $('.info-labels').unwrap();

        $('.group_separator.in_group').each(function(){
			//$(this).parent().parent().removeClass('control-group');
            $(this).parent().parent().addClass('yt-group-title');
        });
        $('.group_separator.no_group').each(function(){
            $(this).parent().parent().addClass('no_group');
        });
		
		$('.tab-pane > .yt-group-title').each(function() {
            $(this).nextUntil('.yt-group-title').addBack().wrapAll('<div class="yt-group clearfix"></div>');
        });

        $('.yt-group > .yt-group-title').each(function() {
            $(this).nextUntil('.yt-group-title').wrapAll('<div class="yt-group-contents"></div>');
        });
		$('.control-group.yt-group-title').each(function(){
            $(this).html( '<h3>' + $(this).text() + '</h3>' )
        });
		
		
		
		$.each($(['overrideLayouts']), function(index, ele){
		
			var rules = $('#' + ele + '_rules');
			var textarea = $('textarea#jform_params_' + ele); //jform_params_overrideLayouts 
			
			var items = textarea.html().split(" ");
			for(var i = 0; i < items.length; i++) {
				
				if(items[i] != "") {
					//var item = new Element('div');
					var item = document.createElement('div');
					item.innerHTML = 'ItemID: <strong>' + items[i].split('=')[0] + '</strong> - Layout: <strong>' + items[i].split('=')[1] + '</strong> <a title="Remove" href="#" class="' + ele + '_remove_rule fa fa-close">  </a>';
					rules.append(item);
				}
			}
			
			
			$(rules).on("click",".overrideLayouts_remove_rule", function(e){ //user click on remove text
				e.preventDefault(); 
				$(this).parent().remove(); 
				var values = $(this).parent().find('strong');
				textarea.text(textarea.html().replace(values[0].innerHTML + "=" + values[1].innerHTML + " ", ''));
			})
			
			$('#'+ ele + '_add_btn').bind('click', function(){
				var rule = document.getElementById(ele + '_input').value + "=" + ((document.getElementById(ele + '_select')) ? document.getElementById(ele + '_select').value : 'enabled') + " "; 
				textareaHtml = textarea.html();
				itemID = document.getElementById(ele + '_input').value;
				
				if(document.getElementById(ele + '_input').value==''){
					alert('Please enter ItemID');
				}else if(textarea.val().indexOf(rule) !=-1) {
					alert('Record already exists');
				}else {
					textarea.text(textarea.html() + rule);// += rule;
					var item = document.createElement('div');
					//var type = document.getElementById(ele + '_input').value.test(/^\d+$/) ? 'ItemID' : 'Option';
					var value = document.getElementById(ele + '_input').value;
					var layout = document.getElementById(ele + '_select') ? document.getElementById(ele + '_select').value : '';
					item.innerHTML = 'ItemID: <strong>' + value + '</strong> - Layout: <strong>' + layout + '</strong> <a title="Remove" href="#" class="' + ele + '_remove_rule fa fa-close">  </a>';
					
					rules.append(item);
				}
			});
		});
		
		/****************************
		*  Themes Color
		****************************/
		$('.presetcolors').closest('.control-group').addClass('pickerblock').hide();
        $('#Colors_params').find('[class$="'+$current_preset+'"]').closest('.pickerblock').show();

        $('#Colors_params .preset-contents').on('click', function(event){
            event.stopImmediatePropagation();
            $(this).closest('.controls').find('.presets').removeClass('active');
            $(this).parent().addClass('active');
         
            $('#Colors_params').find('[class$="'+$(this).data('preset')+'"]').closest('.pickerblock').show();
        });
		
		/****************************
		*  Live Google Font preview
		****************************/
		var previewText = 'Grumpy wizards make toxic brew for the evil Queen and Jack.';
		if( joomla3 ){
			$('#Typography_params .fonts-list')
			.closest('.control-group')
			.append('<div class="span7 font-preview"><h3 class="title-label">Live Preview</h3><p class="preview">'+previewText+'<p>');
		}else{
			$('.yt-fieldset .fonts-list').each(function(){
			$(this).parent().append('<div class="span7 font-preview"><h3 class="title-label">Live Preview</h3><p class="preview">'+previewText+'<p></div>');
		});    
		}
		

		$('.gfonts').change(function(){
			var fontName = "";
			var fontWeight = "";
			var fontUrl = '';
			var pos = '';
			var ids = $(this).attr('id');
			
			fontUrl += $(this).val() + "";
			pos = fontUrl.search(':');
			
			if(pos < 0){
				fontName = fontUrl.replace(/([+])/g," ");
				
			}else{
				fontName = fontUrl.substr(0,pos);
				fontName = fontName.replace(/([+])/g," ");
			}
			
			
			var gfontCookies = createCookie(TMPL_BACKEND+'_fonts',fontName,1);
			
			//if(gfontCookies !=""){
				var link = ("<link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=" + fontUrl +" ' media='screen' />");
				$("head").append(link);
				$(this).parent().parent().parent().find('.preview').css({"font-family":fontName});
			//}



		});
		
});
	
	
	
	
	var YTDepend = window.YTDepend = window.YTDepend || { 	
		radio:function(el, arr){
			if(typeof(arr)!=='undefined'){
				checked = $(el+' input:first');
				checked = $(el).find('input:checked');
				value = $(checked).attr('value');
				$(el+' input').click(function(){
					value = $(this).attr('value');
					YTDepend.preparDisplay(arr, value);
					
				});
				
				YTDepend.preparDisplay(arr, value);
				
			}
		},
		radio2:function(el, arr){
			if(typeof(arr)!=='undefined'){
				checked = $(el+' input:first');
				checked = $(el).find('input:checked');
				value = $(checked).attr('value');
				$(el+' input').click(function(){
					value = $(this).attr('value');
					YTDepend.preparDisplay2(arr, value);
					
				});
				
				YTDepend.preparDisplay2(arr, value);
				
			}
		},
		preparDisplay2: function(arr, value){
			for(i=0; i <arr.length; i++){
				if(arr[i]['1']!=''){
					flag_ele = arr[i]['1'];
					break;
				}
			}
			flag_status = 0;
			for(i=0; i <arr.length; i++){
				if(arr[i]['0']==value){
					arrNew = arr[i]['1'].split(","); 
					for(j=0; j<arrNew.length; j++){
						if(flag_ele == arr[i]['1']){
							flag_status = 1;
						}
						if(arrNew[j]!='') YTDepend.diplayParentRadio(1, '#jform_params_'+arrNew[j]);
					}
				}else{
					arrNew=arr[i]['1'].split(",");
					for(j=0; j<arrNew.length; j++){
						if(arrNew[j]!='' && flag_status==0) YTDepend.diplayParentRadio(0, '#jform_params_'+arrNew[j]);
					}
				}
			}
		},
		preparDisplay: function(arr, value){
			for(i=0; i <arr.length; i++){
				if(arr[i]['0']==value){
					arrNew = arr[i]['1'].split(","); 
					for(j=0; j<arrNew.length; j++){
						if(arrNew[j]!='') YTDepend.diplayParentRadio(1, '#jform_params_'+arrNew[j]);
					}
				}else{
					arrNew=arr[i]['1'].split(",");
					for(j=0; j<arrNew.length; j++){
						if(arrNew[j]!='') YTDepend.diplayParentRadio(0, '#jform_params_'+arrNew[j]);
					}
				}
			}
		},
		diplayParentRadio:function(status, el){
			parent1 = $(el).parent().parent('.control-group');
			if($(parent1).hasClass('control-group')==false){
				parent1 = $(el).parent().parent().parent('.control-group');
			}
			if(status == 1){
				$(parent1).css('display', 'block');
			}else{ 
				$(parent1).css('display', 'none'); 
			}
		}
	}

}(jQuery);



