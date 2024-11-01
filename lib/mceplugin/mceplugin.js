(function() {
    tinymce.create('tinymce.plugins.wpocshortcode', {
		createControl : function(btn, cm) {
            if(btn == 'wpocslidercombo'){
                var cmbo = cm.createListBox('wpocslidercombo', {
                     title : 'Owl Carousel Slider',
                     onselect : function(v) {
                     	if(tinyMCE.activeEditor.selection.getContent() == ''){
                            tinyMCE.activeEditor.selection.setContent( v );
                        }
                     }
                });
				if(typeof wpoc_slider_shortcode != 'undefined' && wpoc_slider_shortcode instanceof Array)
					for(var i in wpoc_slider_shortcode){
						cmbo.add(wpoc_slider_shortcode[i], wpoc_slider_shortcode[i]);
					}
				}
                return cmbo;
            }
            return null;
        }
	});
	
    tinymce.PluginManager.add('wpocshortcode', tinymce.plugins.wpocshortcode);
})();