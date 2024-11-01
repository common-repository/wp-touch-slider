(function() {
    tinymce.create('tinymce.plugins.wpocshortcode', {
        init : function(ed, url) {
			var values = [];
			if(typeof wpoc_slider_shortcode != 'undefined' && wpoc_slider_shortcode instanceof Array){
				for(var i = 0 in wpoc_slider_shortcode){
					var name = (typeof wpoc_slider_shortcode[i].name == 'undefined' || wpoc_slider_shortcode[i].name == '' || wpoc_slider_shortcode[i].name == null)? 
								wpoc_slider_shortcode[i].shortcode : wpoc_slider_shortcode[i].name;
					values.push({
						text: name,
						value: wpoc_slider_shortcode[i].shortcode,
						onclick: function(){
							ed.insertContent(this.value());
						}
					});
				}
			}
            ed.addButton( 'wpocslidercombo', {
                type: 'menubutton',
                title: 'Insert Owl Carousel Slider',
                icon: 'wpoc-slider-icon',
                menu: values
            });
        }
	});
	
    tinymce.PluginManager.add('wpocshortcode', tinymce.plugins.wpocshortcode);
})();