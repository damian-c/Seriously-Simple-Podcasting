jQuery(document).ready(function($) {

	// Uploading files
	var file_frame, series_img_frame;

	jQuery.fn.ssp_upload_media_file = function( button, preview_media, validateImageSize = false ) {
		var button_id = button.attr('id');
		var field_id = button_id.replace( '_button', '' );
		var preview_id = button_id.replace( '_button', '_preview' );

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
		  file_frame.open();
		  return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
		  title: jQuery( this ).data( 'uploader_title' ),
		  button: {
		    text: jQuery( this ).data( 'uploader_button_text' ),
		  },
		  multiple: false
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
		  var attachment = file_frame.state().get('selection').first().toJSON();

		  if ( typeof validateImageSize === 'function' && !validateImageSize( attachment ) ) {
			return;
		  }

		  jQuery("#"+field_id).val(attachment.url);
		  if ( preview_media ) {
		  	jQuery("#"+preview_id).attr('src',attachment.url);
		  }
		});

		// Finally, open the modal
		file_frame.open();
	};

  /* Add/Edit Series Image */
	jQuery('#series_upload_image_button').click(function( event ){
		event.preventDefault();
		var send_attachment_bkp = wp.media.editor.send.attachment;
    var button = $(this);
		var button_id = button.attr('id');
		var preview_id = button_id.replace( '_upload', '' ).replace( '_button', '_preview' );
		var field_id = button_id.replace( '_upload', '' ).replace( '_button', '_id' );

		// If the media frame already exists, reopen it.
		if ( series_img_frame ) {
		  series_img_frame.open();
		  return;
		}

		// Create the media frame.
		series_img_frame = wp.media({
			title: jQuery( this ).data( 'uploader_title' ),
		  button: {
		    text: jQuery( this ).data( 'uploader_button_text' ),
		  },
			library: {
				type: [ 'image' ]
    	},
		  multiple: false
		});

		series_img_frame.on( 'select', function() {
      // Get media attachment details from the frame state
      var attachment = series_img_frame.state().get('selection').first().toJSON();

      // Send the attachment URL to our custom image input field.
      $('#' + preview_id).attr('src', attachment.url);

      // Send the attachment id to our hidden input
      $('#' + field_id).val(attachment.id);
		});

    // Finally, open the modal on click
    series_img_frame.open();
	});

	/* Remove/clear Series Image */
	jQuery('#series_remove_image_button').click(function( event ){
		event.preventDefault();
		var button = $(this);
		var button_id = button.attr('id');
		var preview_id = button_id.replace( '_remove', '' ).replace( '_button', '_preview' );
		var field_id = button_id.replace( '_remove', '' ).replace( '_button', '_id' );

		if ( confirm('Are you sure?') ) {
        var src = $('#' + preview_id).attr('data-src');
        $('#' + preview_id).attr('src', src);
        $('#' + field_id).val('');
    }
	});

	/* ADD/EDIT EPISODE */

	jQuery('#upload_audio_file_button').click(function( event ){
		event.preventDefault();
		jQuery.fn.ssp_upload_media_file( jQuery(this), false );
	});

	jQuery('#episode_embed_code').click(function() {
		jQuery(this).select();
	});

	jQuery( '.episode_embed_code_size_option' ).change(function() {

		var width = jQuery( '#episode_embed_code_width' ).val();
		var height = jQuery( '#episode_embed_code_height' ).val();
		var post_id = jQuery( '#post_ID' ).val();

		jQuery.post(
		    ajaxurl,
		    {
		        'action': 'update_episode_embed_code',
		        'width': width,
		        'height': height,
		        'post_id': post_id,
		    },
		    function( response ){
		        if( response ) {
		        	jQuery( '#episode_embed_code' ).val( response );
		        	jQuery( '#episode_embed_code' ).select();
		        }
		    }
		);
	});

	/* DATEPICKER */

	jQuery('.ssp-datepicker').datepicker({
		changeMonth: true,
      	changeYear: true,
      	showAnim: 'slideDown',
      	dateFormat: 'd MM, yy',
      	altField: '#date_recorded',
      	altFormat: 'dd-mm-yy',
      	onClose : function ( dateText, obj ) {
		    var d = $.datepicker.parseDate("d MM, yy", dateText);
		    var date = $.datepicker.formatDate("dd-mm-yy", d);
		    var save_field = $(this).attr('id').replace( '_display', '' );
		    $( '#' + save_field ).val( date );
		}
	});

	jQuery('.ssp-datepicker').change( function () {
		var value = jQuery( this ).val();
		if( !value ) {
			var id = jQuery( this ).attr( 'id' );
			var save_field = id.replace( '_display', '' );
			jQuery( '#' + save_field ).val( '' );
		}
	});

	/* SETTINGS PAGE */

	jQuery('#feed-series-toggle').click(function(e) {

		if ( jQuery(this).hasClass( 'series-open' ) ) {
			jQuery('#feed-series-list').slideUp('fast');
			jQuery(this).removeClass( 'series-open' );
			jQuery(this).addClass( 'series-closed' );

		} else if ( jQuery(this).hasClass( 'series-closed' ) ) {
			jQuery('#feed-series-list').slideDown('fast');
			jQuery(this).removeClass( 'series-closed' );
			jQuery(this).addClass( 'series-open' );

		}

	});

	jQuery('#ss_podcasting_data_image_button').click(function() {
		jQuery.fn.ssp_upload_media_file( jQuery(this), true );
	});

	jQuery('#ss_podcasting_data_image_delete').click(function() {
		jQuery( '#ss_podcasting_data_image' ).val( '' );
		jQuery( '#ss_podcasting_data_image_preview' ).remove();
		return false;
	});

	jQuery( '#cover_image_button' ).click(function() {
		var validateImageSize = function( attachment ) {
		  return attachment.width === attachment.height && attachment.width >= 300;
		}
		var description = jQuery( this ).siblings( '.description' );
		jQuery.fn.ssp_upload_media_file( jQuery(this), true, validateImageSize );
		description.css( 'color', '' );

		file_frame.on( 'select', function() {
		  var attachment = file_frame.state().get( 'selection' ).first().toJSON();
		  if ( validateImageSize( attachment ) ) {
			jQuery( '#cover_image_id' ).val( attachment.id );
		  } else {
			description.css( 'color', 'red' );
		  }
		});
	});

	jQuery('#cover_image_delete').click(function() {
		jQuery( '#cover_image, #cover_image_id' ).val( '' );
		jQuery( '#cover_image_preview' ).attr( 'src', '' );
	});
});
