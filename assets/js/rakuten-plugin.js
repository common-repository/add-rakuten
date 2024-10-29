(function() {

    function replaceGalleryShortcodes( content ) {
        return content.replace( /\[rakuten([^\]]*)\]/g, function( match ) {
            return html( 'wp-gallery', match );
        });
    };

    function html( cls, data ) {
        
        data = window.encodeURIComponent( data );
        return "todo";

        // return "<img src='Perfume_photo.jpg' width='100%' />";
        // return '<img src="' + tinymce.Env.transparentSrc + '" class="wp-media mceItem ' + cls + '" ' + 'data-wp-media="' + data + '" data-mce-resize="false" data-mce-placeholder="1" alt="" />';

    };

    tinymce.create("tinymce.plugins.Rakuten", {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function( ed, url ) {
            
            ed.addButton( "rakuten", {
                title : "Rakuten",
                cmd : "rakuten",
                image : `${url}/../img/rakuten-20x20.png`
            });

            ed.addCommand("rakuten", function() {
                jQuery(document).ready(function($){
                    $( "#__wp-rakuten-uploader" ).show();
                });
            });

            jQuery(document).ready(function($){
                $( "#__wp-rakuten-uploader .rakuten-button-insert" ).click(function(e){
                    
                    e.preventDefault();

                    var ids = "";
                    $( "#__wp-rakuten-uploader .selection-view ul li").each(function(){
                        if ($(this).attr("aria-checked") === "true") {
                            var item = $( ".json", this ).text();
                            item = jQuery.parseJSON( item );
                            if ( ids !== "" ) {
                                ids += ", ";
                            }
                            ids += item.itemCode;
                        }
                    });

                    shortcode = `[rakuten ids="${ids}"]`;
                    ed.execCommand( "mceInsertContent", 0, shortcode );
                    $( "#__wp-rakuten-uploader" ).hide();

                });
            });




            /*
            // Quand on est en mode visual
            ed.on( 'BeforeSetContent', function( event ) {
                // 'wpview' handles the gallery shortcode when present
                // if ( ! ed.plugins.wpview || typeof wp === 'undefined' || ! wp.mce ) {
                         event.content = replaceGalleryShortcodes( event.content );
                // }
            });
            */

        },
        /**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
        createControl : function(n, cm) {
            return null;
        },
        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                longname : 'Rakuten Buttons',
                author : 'FALEME Jonathan',
                authorurl : 'https://profiles.wordpress.org/scion-lucifer/',
                infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/example',
                version : "0.1"
            };
        },


    });
    // Register plugin
    tinymce.PluginManager.add( 'rakuten_tinymce', tinymce.plugins.Rakuten );
})();