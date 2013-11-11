// Scripts file for the admin screens

jQuery(document).ready(function($) {

	// support for the media library from meta box file fields
	if( typeof wp !== 'undefined' && $('#post-body-content').length ) {

		if( typeof wp.media !== 'undefined' ) {

			var _custom_media = true,
			_orig_send_attachment = wp.media.editor.send.attachment;

			$('.add-file_button').click(function(e) {
				var send_attachment_bkp = wp.media.editor.send.attachment;
				var button = $(this);
				var targetfield = button.attr('data-fieldid');
				var displaybox = $(this).next();

				_custom_media = true;
				wp.media.editor.send.attachment = function(props, attachment){
					if ( _custom_media ) {
						$("#"+targetfield).val(attachment.id);
						$(displaybox).html('<a href="' + attachment.url + '" target="_blank">' + attachment.title + '</a>');
						$("#"+targetfield).next().show();

						var butt = $('input[data-fieldid=' + targetfield + ']');
						$(butt).hide();

					} else {
						return _orig_send_attachment.apply( this, [props, attachment] );
					};
				}

				wp.media.editor.open(button);
				return false;
			});

			$('.add_media').on('click', function(){
				_custom_media = false;
			});

			$('.remove-file_link').on('click', function(){
				$(this).hide();
				var targetfield = $(this).attr('data-fieldid');
				$("#"+targetfield).val('');
				var displaybox = $('div[data-fieldid=' + targetfield + ']');
				$(displaybox).html(' ');
				var butt = $('input[data-fieldid=' + targetfield + ']');
				$(butt).show();
			});

		}

	}

});

// Used to add shortcode buttons
var MCEBUTTON = (function(){

	return {
		init : function(Args) {
			groupName = Args[0];
			groupButtons = Args[1];
			pluginName = 'tinymce.plugins.' + groupName;

		},
		addTheButtons : function() {

			tinymce.create(pluginName, {

				init : function(ed, url) {

					// Iterate through buttons in group
					jQuery.each( groupButtons, function( key, buttonObj ) {

						// extract and build attribute string for this button
						var attributeString = "";
						if( typeof buttonObj.button_attributes != 'undefined') {
							jQuery.each(buttonObj.button_attributes, function(key, val) {
								attributeString += " " + key + "=\"" + val + "\"";
							});
						}
						var imgsrc = url + '/img/button_' + buttonObj.button_name + '.png';
						if( UrlExists( imgsrc ) == false ) {
							var imgsrc = url + '/img/button_default.png';
						}
						// add the button
						ed.addButton(buttonObj.button_name, {
							title : '' + buttonObj.button_title + '',
							image : imgsrc,
							onclick : function() {
								if(buttonObj.button_self_close) {
									ed.selection.setContent('[' + buttonObj.button_name + attributeString + ']');
								} else {
									ed.selection.setContent('[' + buttonObj.button_name + attributeString + ']' + ed.selection.getContent() + '[/' + buttonObj.button_name + ']');
								}
							}
						});
					});

				},

				createControl : function(n, cm) {
					return null;
				}
			});

			tinymce.PluginManager.add( groupName, eval(pluginName) );
		}
	};
}());

function UrlExists(url) {
    try{
    	var http = new XMLHttpRequest();
    	http.open('HEAD', url, false);
    	http.send();
    } catch(err) {
    	return false;
    }
    return http.status!=404;
}