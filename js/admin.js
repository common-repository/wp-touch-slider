
function disableElementsById(elementIds, disable, value, criticalValue, differentElements){
	disable = (value == criticalValue)? disable : !disable;
	differentElements = (differentElements instanceof Array)? differentElements : [];
	
	for(var i = 0; i < elementIds.length; i++){
		var field = jQuery('#' + elementIds[i]);
		var tagName = field.prop('tagName');
		if('DIV' == tagName){
			(disable)? jQuery('#' + elementIds[i]).slider('disable') : jQuery('#' + elementIds[i]).slider('enable');
		} else{
			field.prop('disabled', disable);
		}
	}
	
	if(differentElements.length > 0){
		for(var i = 0; i < differentElements.length; i++){
			var field = jQuery('#' + differentElements[i]);
			var tagName = field.prop('tagName');
			if('DIV' == tagName){
				(disable)? jQuery('#' + differentElements[i]).slider('enable') : jQuery('#' + differentElements[i]).slider('disable');
			} else{
				field.prop('disabled', !disable);
			}
		}
	}
}
//-----------------------------------------------------
function wpoc_removeSlide(index, evt){
	IN_SORTING = true;
	setTimeout(function(){
		IN_SORTING = false;
	}, 1000);
	evt.preventDefault();
	if(confirm(wpoc_you_sure)){
		jQuery('#slide_element_' + index).remove();
	}
}
//-----------------------------------------------------
function wpoc_duplicateSlide(sld_id, evt){
	IN_SORTING = true;
	setTimeout(function(){
		IN_SORTING = false;
	}, 1000);
	evt.preventDefault();
	wpoc_addNewSlide(sld_id);
}
//-----------------------------------------------------
function wpoc_addNewSlide(sld_id){
	sld_id = (typeof sld_id != 'undefined')? sld_id : 0;
	var i = Number(jQuery('#slides_index').val()) + 1;
	jQuery('#slides_index').val(i);
	var o = Number(jQuery('#slides_order').val()) + 1;
	jQuery('#slides_order').val(o);
	wpoc_loading();
	jQuery.ajax({
		url: wpoc_admin_url,
		method: 'POST',
		data: {
			action: 'wpoc_create_slide',
			index: i,
			order: i,
			sld_id: sld_id
		},
		success: function(data){
			wpoc_loading();
			if(data){
				jQuery('#sortable-slides').append(data);
				jQuery('#slide_element_' + i + ' div.collapsible-panel div.header').click(function(e){
					if(!IN_SORTING){
						jQuery(this).next('.collapsible-panel div.body').slideToggle(400);
						jQuery(this).toggleClass('active');
						e.preventDefault();
					} else{
						IN_SORTING = false;
					}
				});
			}
		},
		error: function(){
			wpoc_loading();
		}
	});
}
//-----------------------------------------------------
function wpoc_slideTypeChange(el){
	var type = wpoc_getFieldValue((new String(el.name)).replace(/(\[|\])/g, '\\$1'));
	var index = (new String(el.id)).split('_').reverse()[0];
	var title = el.options[el.selectedIndex].text;
	jQuery('#default_container_' + index).css('display', 'none');
	jQuery('#video_container_' + index).css('display', 'none');
	jQuery('#text_container_' + index).css('display', 'none');
	
	var iconClass = '';
	switch(type){
		case 'default':
		iconClass = 'fa-picture-o';
		break;
		
		case 'video':
		iconClass = 'fa-youtube-play';
		break;
		
		case 'text':
		iconClass = 'fa-file-text-o';
		break;
	}
		
	jQuery('#slide_element_' + index + ' div.header .icon').removeClass('fa-picture-o');
	jQuery('#slide_element_' + index + ' div.header .icon').removeClass('fa-youtube-play');
	jQuery('#slide_element_' + index + ' div.header .icon').removeClass('fa-file-text-o');
	
	jQuery('#' + type + '_container_' + index).css('display', 'block');
	
	jQuery('#slide_element_' + index + ' div.header .icon > i').addClass(iconClass);
	jQuery('#slide_element_' + index + ' div.header .title').html(title);
}
//-----------------------------------------------------
function wpoc_getFieldValue(fieldName){
	var  fields = jQuery('[name=' + fieldName + ']');
	var tagName = fields.prop('tagName');
	
	switch(tagName){
		case 'SELECT':
		case 'TEXTAREA':
		return fields.val();
		break;
		
		case 'INPUT':
		var type = fields.prop('type');
		switch(type){
			case 'text':
			case 'password':
			case 'hidden':
			return fields.val();
			break;
			
			case 'radio':
			return jQuery('[name=' + fieldName + ']:checked').val();
			break;
			
			case 'checkbox':
			var items = [];
			jQuery('[name=' + fieldName + ']:checked').each(function(){
				items.push($(this).val());
			});
			return items;
			break;
			
			default:
			return fields.val();
		}
		break;
		
		default:
		return fields.val();
	}
}
//-----------------------------------------------------
function wpoc_loading(){
	jQuery('#wpoc-loading').toggleClass('hidden');
}
//-----------------------------------------------------
function init_color_picker(pickerId, hiddenId){
	var picker = jQuery("#" + pickerId).colpick({
		colorScheme: "dark",
		layout: "hex",
		color: jQuery("#" + hiddenId).val(),
		onSubmit:function(hsb, hex, rgb, el){
			jQuery("#" + hiddenId).val(hex);
			jQuery(el).css("background-color", "#" + hex);
			jQuery(el).colpickHide();
			var index = pickerId.split("_").reverse()[0];
			set_default_preview(index);
		}
	});
	picker.css("background-color", "#" + jQuery("#" + hiddenId).val());
	return picker;
}
//-----------------------------------------------------
function set_default_preview(index){
	var backgroundColor = jQuery('#slides_backgroundColor_' + index).val();
	var icon = jQuery('#slides_icon_' + index).val();
	var iconType = jQuery('#slides_iconType_' + index).val();
	var iconColor = jQuery('#slides_iconColor_' + index).val();
	var iconSize = jQuery('#slides_iconSize_' + index).val();
	var largeTitle = jQuery('#slides_largeTitle_' + index).val();
	var largeTitleColor = jQuery('#slides_largeTitleColor_' + index).val();
	var smallTitle = jQuery('#slides_smallTitle_' + index).val();
	var smallTitleColor = jQuery('#slides_smallTitleColor_' + index).val();
	var iconImageUrl = jQuery('#slides_iconImageUrl_' + index).val();
	var largeTitleStyles = jQuery('#slides_largeTitleStyles_' + index).val();
	var smallTitleStyles = jQuery('#slides_smallTitleStyles_' + index).val();
	
	var content = '';
	var img = '';
	if(!wpoc_empty(iconImageUrl)){
		img = '<img src="' + iconImageUrl + '" width="' + iconSize + '" height="' + iconSize + '" />';
	}
	else if(!wpoc_empty(icon)){
		switch(iconType){
			case 'font-awesome':
			img = '<i class="fa fa-' + icon + '"></i>';
			break;
			
			case 'ico-moon':
			img = '<span class="icon icon-' + icon + '"></span>';
			break;
		}
	}
	
	if(!wpoc_empty(img)){
		content += '<span style="' +
					((!wpoc_empty(iconColor))? 'color: #' +  iconColor + ';' : '') +
					' ' +
					((!wpoc_empty(iconSize))? 'font-size: ' +  iconSize + 'px;' : '') +
					'">' + img + '</span>';
	}
	content += '<div class="wpoc-titles-wrapper">';
	if(!wpoc_empty(largeTitle)){
		content += '<h3 style="' + ((!wpoc_empty(largeTitleColor))? 'color: #' +  largeTitleColor + '; ' : '') + largeTitleStyles + '">' + largeTitle + '</h3>';
	}
	if(!wpoc_empty(smallTitle)){
		content += '<h4 style="' + ((!wpoc_empty(smallTitleColor))? 'color: #' +  smallTitleColor + '; ' : '') + smallTitleStyles + '">' + smallTitle + '</h4>';
	}
	content += '</div>';
	
	var preview = jQuery('#default_preview_' + index);
	if(!wpoc_empty(backgroundColor)){
		preview.css('background-image', '');
		preview.css('background-color', '#' + backgroundColor);
	} else{
		preview.css('background-image', 'url(' + wpoc_empty_background + ')');
	}
	preview.html(content);
}
//-----------------------------------------------------
function wpoc_empty(v){
	if(typeof v == 'undefined' || v == null || v == '' || v == '0' || v == 0){
		return true;
	}
	else if(v instanceof Array && v.length == 0){
		return true;
	}
	else if(v instanceof Object){
		var empty = true;
		for(var index in v){
			empty = false;
		}
		if(empty){
			return true;
		}
	}
	return false;
}
//-----------------------------------------------------
function setFontawesomeIcon(icon, index){
	jQuery('#selected_icon_' + index).html('<i class="fa fa-' + icon + ' fa-2x"></i>');
	jQuery('#slides_icon_' + index).val(icon);
	jQuery('#slides_iconType_' + index).val('font-awesome');
	set_default_preview(index);
}
//-----------------------------------------------------
function setIcomoonIcon(icon, index){
	jQuery('#selected_icon_' + index).html('<span class="icon icon-' + icon + '"></i>');
	jQuery('#slides_icon_' + index).val(icon);
	jQuery('#slides_iconType_' + index).val('ico-moon');
	set_default_preview(index);
}
//-----------------------------------------------------
var imgSelectCallbackFunc = null;
var imgSelectCallbackFuncParams = null;
window.original_send_to_editor = window.send_to_editor;

window.send_to_editor = function(html){
	var fileurl = '';
	fileurl = jQuery('img', html).attr('src');
	if(!fileurl){
		var regex = /src="(.+?)"/;
		var rslt = html.match(regex);
		fileurl = rslt[1];
		
		window[imgSelectCallbackFunc](fileurl, imgSelectCallbackFuncParams);
	}
	
	tb_remove();
	jQuery('html').removeClass('Image');
}
//-----------------------------------------------------
function wpoc_add_image(callback, params){
	imgSelectCallbackFunc = callback;
	imgSelectCallbackFuncParams = params;
	
	jQuery('html').addClass('Image');
	
	var frame;
	if (wpoc_wordpress_ver >= "3.5") {
		if (frame) {
			frame.open();
			return;
		}
		frame = wp.media();
		frame.on("select", function(){
			var attachment = frame.state().get("selection").first();
			var fileurl = attachment.attributes.url;
			frame.close();
			
			//var url = encodeURIComponent(fileurl);
			window[imgSelectCallbackFunc](fileurl, imgSelectCallbackFuncParams);
		});
		frame.open();
	}
	else {
		tb_show("", "media-upload.php?type=image&amp;TB_iframe=true&amp;tab=library");
		return false;
	}
	
}
//-----------------------------------------------------
function wpoc_select_icon_img(url, index){
	jQuery('#slides_iconImageUrl_' + index).val(url);
	jQuery('#icon_image_wrapper_' + index).html('<img src="' + url + '" />');
	jQuery('#add_icon_img_btn_' + index).css('display', 'none');
	jQuery('#remove_icon_img_btn_' + index).css('display', 'block');
	set_default_preview(index);
}
//-----------------------------------------------------
function wpoc_remove_icon_image(index){
	jQuery('#slides_iconImageUrl_' + index).val('');
	jQuery('#icon_image_wrapper_' + index).html('');
	jQuery('#remove_icon_img_btn_' + index).css('display', 'none');
	jQuery('#add_icon_img_btn_' + index).css('display', 'block');
	set_default_preview(index);
}
//-----------------------------------------------------
function deleteSlider(sdr_id){
	if(confirm(wpoc_you_sure)){
		jQuery.ajax({
			url: wpoc_admin_url,
			method: 'POST',
			dataType: 'html',
			data: {
				action: 'wpoc_delete_slider',
				sdr_id: sdr_id
			},
			success: function(data){
				if('1' == data || 1 == data){
				console.log("Fesfwef");
					jQuery('#slider_' + sdr_id).remove();
				}
			}
		});
	}
}
//-----------------------------------------------------
