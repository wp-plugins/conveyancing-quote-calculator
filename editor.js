( function() {

    tinymce.PluginManager.add( 'hoowla_test', function( editor, url ) {

        editor.addButton('hoowla_test_button_key', { 
            title : 'Insert Quote',
            image: url + '/hoowla-icon.png',
            onclick : function() { 

                // Get Hoowla Default Settings
                jQuery.post(ajaxurl, {'action': 'hoowla_get_options'}, function(response) {

                    var result = JSON.parse(response);
                    var html = '[hoowlabutton';
                    if (result.hoowla_default_id) {
                        html += ' id="' + result.hoowla_default_id + '"';
                    }
                    else {
                        html += ' id="QUOTEFORM ID HERE"';
                    }
                    if (result.hoowla_default_text) {
                        html += ' text="' + result.hoowla_default_text + '"';
                    }
                    if (result.hoowla_default_color) {
                        html += ' color="' + result.hoowla_default_color + '"';
                    }
                    if (typeof result.hoowla_default_size != 'undefined') {
                        html += ' size="large"';
                    }
                    html += ']';

                    editor.execCommand('mceInsertContent', false, html);
                });
            } 
        });

    });

})();