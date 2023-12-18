var wp_mapit_multipin_map = null;
var wp_mapit_multipin_zoom = null;
var wp_mapit_multipin_lat = null;
var wp_mapit_multipin_lng = null;
var wp_mapit_multipin_default_type = null;
var wp_mapit_multipin_type = null;
var wp_mapit_multipin_marker_image = null;
var wp_mapit_multipin_base_layer = null;
var wp_mapit_mappin_pins = {};
var wp_mapit_multipin_pins = {};
var typingTimer = null;

function set_multipin_map_center() {
	jQuery( '#wpmi_multipin_map_latitiude' ).val( wp_mapit_multipin_lat );
	jQuery( '#wpmi_multipin_map_longitude' ).val( wp_mapit_multipin_lng );
	jQuery( '#wpmi_multipin_map_zoom' ).val( wp_mapit_multipin_zoom );

	wp_mapit_multipin_map.setView( new L.LatLng( wp_mapit_multipin_lat, wp_mapit_multipin_lng ), wp_mapit_multipin_zoom );
}

function set_pin_map_base_layer( counter ) {
	_layerImage = '//{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
	_attribution = 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors';

	var _class = '';

	switch( wp_mapit_multipin_type ) {
		case 'grayscale':
			_class = 'grayscale';
			break;
		case 'topographic':
			_layerImage = '//{s}.tile.opentopomap.org/{z}/{x}/{y}.png';
			_attribution = 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)';
	}

	if( wp_mapit_mappin_pins[counter]['base_layer'] != null ){
		wp_mapit_mappin_pins[counter]['map'].removeLayer( wp_mapit_mappin_pins[counter]['base_layer'] );
	}

	wp_mapit_mappin_pins[counter]['base_layer'] = L.tileLayer( _layerImage , {
		attribution: wp_mapit.plugin_attribution + _attribution,
		className: _class
	}).addTo(wp_mapit_mappin_pins[counter]['map']);
	
}

function set_pin_map_marker( _counter, _lat, _lng ) {

	/* Remove existing marker */
	if( wp_mapit_mappin_pins[_counter]['marker'] != null ){
		wp_mapit_mappin_pins[_counter]['map'].removeLayer( wp_mapit_mappin_pins[_counter]['marker'] );
		wp_mapit_mappin_pins[_counter]['marker'] = null;
	}

	if( wp_mapit_multipin_pins[_counter] != null ) {
		wp_mapit_multipin_map.removeLayer( wp_mapit_multipin_pins[_counter] );
		wp_mapit_multipin_pins[_counter] = null;
	}

	jQuery( 'input[name="wp_mapit_pins[' + _counter + '][lat]"]' ).val( _lat );
	jQuery( 'input[name="wp_mapit_pins[' + _counter + '][lng]"]' ).val( _lng );

	_markerImage = jQuery.trim( jQuery( 'input[name="wp_mapit_pins[' + _counter + '][marker_image]"]' ).val() );
	if( _markerImage == '' ) {
		_markerImage = wp_mapit_multipin_marker_image;
	}

	_img = new Image();
	_img.src = _markerImage;
	_img.onload = function() {

		_height = ( this.height > 100 ? 100 : this.height );
		_width = ( this.width > 100 ? 100 : this.width );

		_halfWidth = _width / 2;

		tempMarker = new L.Marker( [_lat, _lng], { 
			icon: L.icon( { iconUrl: this.src, iconSize: [ _width, _height ], iconAnchor: [ _halfWidth, _height ] } ),
			draggable: true
		} ).addTo( wp_mapit_mappin_pins[_counter]['map'] );

		tempMarkerMain = new L.Marker( [_lat, _lng], { 
			icon: L.icon( { iconUrl: this.src, iconSize: [ _width, _height ], iconAnchor: [ _halfWidth, _height ] } )
		} ).addTo( wp_mapit_multipin_map );

		tempMarker._counter = _counter;

		wp_mapit_mappin_pins[_counter]['marker'] = tempMarker;
		wp_mapit_mappin_pins[_counter]['map'].setView( [ _lat, _lng ], wp_mapit_multipin_zoom );

		wp_mapit_multipin_pins[_counter] = tempMarkerMain;

		wp_mapit_mappin_pins[_counter]['marker'].on( 'moveend', function() {
			_center = wp_mapit_mappin_pins[this._counter]['marker'].getLatLng();

			jQuery( 'input[name="wp_mapit_pins[' + this._counter + '][lat]"]' ).val( _center.lat );
			jQuery( 'input[name="wp_mapit_pins[' + this._counter + '][lng]"]' ).val( _center.lng );

			wp_mapit_multipin_pins[this._counter].setLatLng( [ _center.lat, _center.lng ] );

		} );

		/* Triggered wpmi_marker_title blur event so that the popup is created if the text exists. */
		jQuery( 'input[name="wp_mapit_pins[' + _counter + '][marker_title]"]' ).trigger( 'blur' );
	}

}

jQuery( window ).on( 'load', function(){

	if( jQuery( '#wpmi_multipin_map' ).length > 0 ) {

		wp_mapit_multipin_zoom = jQuery.trim( jQuery( '#wpmi_multipin_map_zoom' ).val() );
		if( parseFloat( wp_mapit_multipin_zoom, 10 ) <= 0 ) {
			wp_mapit_multipin_zoom = jQuery( '#wpmi_multipin_map' ).data( 'zoom' );
		}

		wp_mapit_multipin_lat = jQuery.trim( jQuery( '#wpmi_multipin_map_latitiude' ).val() );
		if( parseFloat( wp_mapit_multipin_lat, 10 ) == 0 || parseFloat( wp_mapit_multipin_lat, 10 ) == '' ) {
			wp_mapit_multipin_lat = jQuery( '#wpmi_multipin_map' ).data( 'latitude' );
		}

		wp_mapit_multipin_lng = jQuery.trim( jQuery( '#wpmi_multipin_map_longitude' ).val() );
		if( parseFloat( wp_mapit_multipin_lng, 10 ) == 0 || parseFloat( wp_mapit_multipin_lng, 10 ) == '' ) {
			wp_mapit_multipin_lng = jQuery( '#wpmi_multipin_map' ).data( 'longitude' );
		}

		wp_mapit_multipin_default_type = jQuery( '#wpmi_multipin_map' ).data( 'maptype' );

		wp_mapit_multipin_marker_image = jQuery.trim( jQuery( '#wpmi_multipin_map_marker_image' ).val() );
		if( wp_mapit_multipin_marker_image == '' ) {
			wp_mapit_multipin_marker_image = jQuery( '#wpmi_multipin_map' ).data( 'marker' );
		}

		wp_mapit_multipin_map = L.map( 'wpmi_multipin_map', { fullscreenControl: true, gestureHandling: true }  ).setView( [ wp_mapit_multipin_lat, wp_mapit_multipin_lng ], wp_mapit_multipin_zoom );

		wp_mapit_multipin_map.on( 'zoomend', function() {
			wp_mapit_multipin_zoom = wp_mapit_multipin_map.getZoom();

			jQuery( '#wpmi_multipin_map_zoom' ).val( wp_mapit_multipin_zoom );
		} );

		wp_mapit_multipin_map.on( 'moveend', function() {
			var _center = wp_mapit_multipin_map.getCenter();

			wp_mapit_multipin_lat = _center.lat;
			wp_mapit_multipin_lng = _center.lng;

			jQuery( '#wpmi_multipin_map_latitiude' ).val( wp_mapit_multipin_lat );
			jQuery( '#wpmi_multipin_map_longitude' ).val( wp_mapit_multipin_lng );
		} );

		jQuery( '#wpmi_multipin_map_type' ).change( function() {
			_layerImage = '//{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
			_attribution = 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors';

			_val = jQuery.trim( jQuery( this ).val() );

			if( _val == '' ) {
				_val = wp_mapit_multipin_default_type;
			}

			wp_mapit_multipin_type = _val;

			var _class = '';

			switch( wp_mapit_multipin_type ) {
				case 'grayscale':
					_class = 'grayscale';
					break;
				case 'topographic':
					_layerImage = '//{s}.tile.opentopomap.org/{z}/{x}/{y}.png';
					_attribution = 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)';
			}

			if( wp_mapit_multipin_base_layer != null ){
				wp_mapit_multipin_map.removeLayer( wp_mapit_multipin_base_layer );
			}

			wp_mapit_multipin_base_layer = L.tileLayer( _layerImage , {
				attribution: wp_mapit.plugin_attribution + _attribution,
				className: _class
			}).addTo(wp_mapit_multipin_map);


			/* Change base layer for each pin map */
			var wp_mapit_mappin_pins_keys = Object.keys(wp_mapit_mappin_pins);
			if( wp_mapit_mappin_pins_keys.length > 0 ) {
				wp_mapit_mappin_pins_keys.forEach( function( _key ) {
					set_pin_map_base_layer( _key );
				} );
			}
		} );

		/* Search location */
		jQuery( '#wpmi_multipin_map_search_btn' ).click( function( e ){
			e.preventDefault();

			_this = jQuery( this );

			if( ! _this.hasClass('disbaled') ) {

				_this.addClass( 'disbaled' );
				_this.text( wp_mapit.please_wait_text );

				jQuery.ajax( {
					url: wp_mapit.ajax_url,
					data: 'action=wp_mapit_location_search&q=' + escape( jQuery( '#wpmi_multipin_map_search' ).val() ) +
						'&wp_mapit_ajax=' + wp_mapit.ajax_nonce,
					success: function( data ){
						data = JSON.parse( data );

						if( data.status == '1' ) {
							if( data.data['error'] != null && data.data['error']['message'] != null ) {
								alert( data.data['error']['message'] );
							} else {

								wp_mapit_multipin_lat = data.data[0].lat;
								wp_mapit_multipin_lng = data.data[0].lon;

								set_multipin_map_center();
							}

						} else if( data.message != '' ) {
							alert( data.message )
						}

						_this.removeClass( 'disbaled' );
						_this.text( wp_mapit.search_text );
					},
					error: function(){
						_this.removeClass( 'disbaled' );
						_this.text( wp_mapit.search_text );
						alert( wp_mapit.ajax_error_message );
					}
				} );
			}
		} );

		jQuery( '#wpmi_multipin_map_latitiude, #wpmi_multipin_map_longitude' ).blur( function() {

			wp_mapit_multipin_lat = jQuery.trim( jQuery( '#wpmi_multipin_map_latitiude' ).val() );
			wp_mapit_multipin_lng = jQuery.trim( jQuery( '#wpmi_multipin_map_longitude' ).val() );

			set_multipin_map_center();

		} );

		jQuery( '#wpmi_multipin_map_zoom' ).blur( function() {
			var _zoom = parseFloat( jQuery( this ).val() );

			if( isNaN( _zoom ) || _zoom < 1 || _zoom > 20 ) {
				_zoom = wp_mapit_multipin_zoom;
			}

			wp_mapit_multipin_zoom = _zoom;
			
			set_multipin_map_center();
		} );

		jQuery( document ).on( 'keyup', '#wpmi_multipin_map_latitiude, #wpmi_multipin_map_longitude, #wpmi_multipin_map_zoom, input.pin_latitude, input.pin_longitude, input.pin_title, textarea.pin_content, input.pin_url', function() {
			clearTimeout( typingTimer );
 			
 			_this = jQuery( this );

  			typingTimer = setTimeout( function() {
  				_this.trigger( 'blur' );
  			}, 1000);
		} ).on( 'keydown', '#wpmi_multipin_map_latitiude, #wpmi_multipin_map_longitude, #wpmi_multipin_map_zoom, input.pin_latitude, input.pin_longitude, input.pin_title, textarea.pin_content, input.pin_url', function() {
			clearTimeout( typingTimer );
		} );

		jQuery( '#wpmi_multipin_map_marker_image' ).change( function() {

			if( jQuery.trim( jQuery( this ).val() ) == '' )
				wp_mapit_multipin_marker_image = jQuery( '#wpmi_multipin_map' ).data( 'marker' );
			else
				wp_mapit_multipin_marker_image = jQuery( this ).val();

			jQuery( '.pin_marker_image' ).change();
		} );

		jQuery( '#add_multipin' ).click( function(e) {
			e.preventDefault();

			multipinFunc();
		} );

		jQuery( '#wpmi_mappin_container' ).on( 'click', 'a.remove_pin', function(e) {
			e.preventDefault();

			if( confirm( wp_mapit_multipin.remove_pin_confirm_text ) ) {
				_counter = jQuery( this ).data( 'counter' );

				if( wp_mapit_multipin_pins[_counter] != null ) {
					wp_mapit_multipin_map.removeLayer( wp_mapit_multipin_pins[_counter] );
					wp_mapit_multipin_pins[_counter] = null;
				}

				wp_mapit_mappin_pins[_counter]['map'] = null;
				wp_mapit_mappin_pins[_counter]['base_layer'] = null;
				wp_mapit_mappin_pins[_counter]['marker'] = null;

				jQuery( this ).parent( '.pin_container' ).fadeOut( 'fast', function() {
					jQuery( this ).remove();
				} );
			}
		} ).on( 'change', 'input.pin_marker_image', function() {
			_this = jQuery( this );

			_counter = _this.data('counter');

			if( wp_mapit_mappin_pins[_counter]['marker'] != null ) {
				_markerImage = jQuery.trim( jQuery( 'input[name="wp_mapit_pins[' + _counter + '][marker_image]"]' ).val() );

				if( _markerImage == '' ) {
					_markerImage = wp_mapit_multipin_marker_image;
				}

				var _img = new Image();
				_img.src = _markerImage;
				_img.counter = _counter;
				_img.onload = function() {

					_height = ( this.height > 100 ? 100 : this.height );
					_width = ( this.width > 100 ? 100 : this.width );

					_halfWidth = _width / 2;

					_mapMarker = wp_mapit_mappin_pins[this.counter]['marker'];

					wp_mapit_mappin_pins[this.counter]['marker'].setIcon( L.icon( { iconUrl: this.src, iconSize: [ _width, _height ], iconAnchor: [ _halfWidth, _height ] } ) );
					wp_mapit_multipin_pins[this.counter].setIcon( L.icon( { iconUrl: this.src, iconSize: [ _width, _height ], iconAnchor: [ _halfWidth, _height ] } ) );
					/* Triggered blur to reset the popup */
					jQuery( 'input[name="wp_mapit_pins[' + this.counter + '][marker_title]"]' ).trigger( 'blur' );
				}
			}
		} ).on( 'blur', 'input.pin_latitude, input.pin_longitude', function() {
			_counter = jQuery( this ).data( 'counter' );

			_lat = jQuery.trim( jQuery( 'input[name="wp_mapit_pins[' + _counter + '][lat]"]' ).val() );
			_lng = jQuery.trim( jQuery( 'input[name="wp_mapit_pins[' + _counter + '][lng]"]' ).val() );

			if( _lat != '' && _lng != '' ) {
				set_pin_map_marker(_counter, _lat, _lng);
			} else {
				/* Remove existing marker */
				if( wp_mapit_mappin_pins[_counter]['marker'] != null ){
					wp_mapit_mappin_pins[_counter]['map'].removeLayer( wp_mapit_mappin_pins[_counter]['marker'] );
					wp_mapit_mappin_pins[_counter]['marker'] = null;
				}

				if( wp_mapit_multipin_pins[_counter] != null ) {
					wp_mapit_multipin_map.removeLayer( wp_mapit_multipin_pins[_counter] );
					wp_mapit_multipin_pins[_counter] = null;
				}
			}
		} ).on( 'click', '.map-pin-search', function( e ){
			e.preventDefault();

			_this = jQuery( this );
			_counter = _this.data( 'counter' );

			if( ! _this.hasClass('disbaled') ) {

				_this.addClass( 'disbaled' );
				_this.attr( 'title', wp_mapit.please_wait_text );

				jQuery.ajax( {
					url: wp_mapit.ajax_url,
					data: 'action=wp_mapit_location_search&q=' + escape( jQuery( '#search_map_' + _counter ).val() ) +
						'&wp_mapit_ajax=' + wp_mapit.ajax_nonce,
					success: function( data ){
						data = JSON.parse( data );

						if( data.status == '1' ) {
							
							if( data.data['error'] != null && data.data['error']['message'] != null ) {
								alert( data.data['error']['message'] );
							} else {

								_lat = data.data[0].lat;
								_lng = data.data[0].lon;

								set_pin_map_marker(_counter, _lat, _lng);
							}

						} else if( data.message != '' ) {
							alert( data.message )
						}

						_this.removeClass( 'disbaled' );
						_this.attr( 'title', wp_mapit.search_text );
					},
					error: function(){
						_this.removeClass( 'disbaled' );
						_this.attr( 'title', wp_mapit.search_text );
						alert( wp_mapit.ajax_error_message );
					}
				} );
			}
		} ).on( 'blur', '.pin_title, .pin_content, .pin_url', function() {

			_counter = jQuery( this ).data( 'counter' );

			if( wp_mapit_mappin_pins[_counter]['marker'] != null ) {

				wp_mapit_mappin_pins[_counter]['marker'].closePopup();
				wp_mapit_mappin_pins[_counter]['marker'].unbindPopup();
				wp_mapit_mappin_pins[_counter]['marker'].off('click');

				wp_mapit_multipin_pins[_counter].closePopup();
				wp_mapit_multipin_pins[_counter].unbindPopup();
				wp_mapit_multipin_pins[_counter].off('click');

				_url = jQuery.trim( jQuery( 'input[name="wp_mapit_pins[' + _counter + '][marker_url]"]' ).val() );

				if( _url != '' ) {
					wp_mapit_mappin_pins[_counter]['marker']._url = _url;
					wp_mapit_mappin_pins[_counter]['marker'].on('click', function() {
						window.open( this._url );
					});

					wp_mapit_multipin_pins[_counter]._url = _url;
					wp_mapit_multipin_pins[_counter].on('click', function() {
						window.open( this._url );
					});

				} else {
					_title = jQuery( 'input[name="wp_mapit_pins[' + _counter + '][marker_title]"]' ).val();
					_content = jQuery( 'textarea[name="wp_mapit_pins[' + _counter + '][marker_content]"]' ).val();
					_html = '';

					if( jQuery.trim( _title ) != '' ) {
						_html += '<h3>' + _title + '</h3>';
					}

					if( jQuery.trim( _content ) != '' ) {

						_content = _content.split( '\n' ).join( '<br>' );

						_html += '<p>' + _content + '</p>';
					}


					if ( _html != '' ) {
						var popup = L.responsivePopup( { offset: [ 20, 20 ] } ).setContent( _html );
						wp_mapit_mappin_pins[_counter]['marker'].bindPopup( popup );
						wp_mapit_multipin_pins[_counter].bindPopup( popup );
					}	
				}				
			}
		} );

		jQuery( '#wpmi_multipin_map_type' ).trigger( 'change' );

		/* Init maps pins on load */
		if( jQuery( 'div.pin_map' ).length > 0 ) {
			jQuery( 'div.pin_map' ).each( function() {
				_counter = jQuery( this ).data( 'counter' );

				wp_mapit_mappin_pins[_counter] = {};

				_lat = jQuery( 'input[name="wp_mapit_pins[' + _counter + '][lat]"]' ).val();
				_lng = jQuery( 'input[name="wp_mapit_pins[' + _counter + '][lng]"]' ).val();
				
				tempMap = L.map( 'pin_map_' + _counter, { fullscreenControl: true, gestureHandling: true } ).setView( [ _lat, _lng ], wp_mapit_multipin_zoom );
				tempMap._counter = _counter;

				tempMap.on( 'click', function(e){
					_lat = e.latlng.lat;
					_lng = e.latlng.lng;

					set_pin_map_marker( this._counter, _lat, _lng );
				} );

				wp_mapit_mappin_pins[_counter]['map'] = tempMap;
				wp_mapit_mappin_pins[_counter]['base_layer'] = null;
				wp_mapit_mappin_pins[_counter]['marker'] = null;

				wp_mapit_multipin_pins[_counter] = null;

				set_pin_map_base_layer( _counter );

				set_pin_map_marker( _counter, _lat, _lng );
			} );
		}

		/* Upload file */
		jQuery( document ).on( 'click', 'a.upload_csv_file', function( e ){
			e.preventDefault();

			var cpimageuploaded, objCurrentUploader;

			_this = jQuery( this );

			objCurrentUploader = _this;

			if( undefined !== cpimageuploaded )
			{
				cpimageuploaded.open();
				return;
			}

			cpimageuploaded = wp.media.frames.file_frame = wp.media({
				multiple: false,
				library: {
					type: ['text/csv']
				}
			});

			cpimageuploaded.on('select', function(){
				attachment  = cpimageuploaded.state().get( 'selection' ).first().toJSON();

				if( attachment.mime == 'text/csv' ) {

				fileUrl = attachment.url;

				jQuery.ajax({
			        type: "GET",
			        url: fileUrl,
			        dataType: "text",
			        beforeSend: function() {
				        jQuery('.upload_csv_file span').addClass('loading');
				    },
			        success: function(allText) {
			        	var allTextLines = allText.split(/\r\n|\n/);
					    var headers = allTextLines[0].split(',');
					    for (var i=1; i<allTextLines.length; i++) {
					        var data = allTextLines[i].split(/,(?=(?:(?:[^"]*"){2})*[^"]*$)/);

					        jQuery.each( data, function( key, value ) {
						    	var new_value = value.replace(/['"]+/g, ''); // replace double quoation mark with blank
							  	data[key] = new_value;
							});

					        if (data.length == headers.length) {
					    		multipinFunc(data);
					        }
					    }

					    jQuery("#wpmi_mappin_container .pin_longitude").blur();
			        },complete: function() {
					    jQuery('.upload_csv_file span').removeClass('loading');
				    }
			     });

				} else {
					alert('Please select a CSV file.');
				}

			});

			cpimageuploaded.open();
		} );

		/* Delete All Pins*/
		jQuery( document ).on( 'click', 'a.delete_all_pins', function( e ){
			e.preventDefault();
			jQuery('#wpmi_mappin_container').html('');
		});

		// For multipin block
		function multipinFunc (pin_data=[]){

			_this = jQuery( '#add_multipin' );
			_counter = _this.data( 'counter' );
			_this.data( 'counter', _counter + 1 );
			_pinid = _this.data( 'pinid' );
			
			_pinContainer = jQuery( '<div>' ).attr( 'id', 'pin_container_' + _counter ).addClass( 'wp-mapit-row pin_container' );

			_pinContainer.append(
				jQuery( '<a>' ).attr( 'href', '#' ).attr( 'title', wp_mapit_multipin.remove_pin_text ).data( 'counter', _counter ).addClass( 'remove_pin' )
			).append(
				jQuery( '<div>' ).addClass( 'column-3' ).append(
					jQuery( '<div>' ).addClass( 'wp-mapit-row' ).append(
						jQuery( '<label>' ).text( wp_mapit_multipin.search_map_text )
					).append(
						jQuery( '<div>' ).addClass( 'wp-mapit-search' ).append(
							jQuery( '<input type="text">' ).attr( 'id', 'search_map_' + _counter )
						).append(
							jQuery( '<a>' ).attr( { 'href' : '#', 'title' : wp_mapit_multipin.search_map_text } ).data( 'counter', _counter ).addClass( 'button map-pin-search' )
						)
					)
				).append(
					jQuery( '<div>' ).addClass( 'wp-mapit-row' ).append(
						jQuery( '<label>' ).text( wp_mapit_multipin.latitude_text )
					).append(
						jQuery( '<input type="number">' ).attr( { 'step' : 'any', 'name' : _pinid + '[' + _counter + '][lat]', 'required' : 'required' } ).data( 'counter', _counter ).addClass( 'pin_latitude' )
					)
				).append(
					jQuery( '<div>' ).addClass( 'wp-mapit-row' ).append(
						jQuery( '<label>' ).text( wp_mapit_multipin.longitude_text )
					).append(
						jQuery( '<input type="number">' ).attr( { 'step' : 'any', 'name' : _pinid + '[' + _counter + '][lng]', 'required' : 'required' } ).data( 'counter', _counter ).addClass( 'pin_longitude' )
					)
				).append(
					jQuery( '<div>' ).addClass( 'wp-mapit-row no-margin pin-img-container' ).append(
						jQuery( '<label>' ).text( wp_mapit_multipin.marker_image_text )
					).append(
						jQuery( '<input type="hidden">' ).attr( 'name', _pinid + '[' + _counter + '][marker_image]' ).data( 'counter', _counter ).addClass( 'pin_marker_image' )
					).append(
						jQuery( '<a>' ).attr( 'href', '#' ).text( wp_mapit_multipin.choose_image_text ).addClass( 'upload_image button' )
					).append(
						jQuery( '<span>' ).html( '&nbsp;' )
					).append(
						jQuery( '<a>' ).attr( 'href', '#' ).text( wp_mapit_multipin.remove_image_text ).addClass( 'remove_image button' )
					)
				)
			).append(
				jQuery( '<div>' ).addClass( 'column-3' ).append(
					jQuery( '<div>' ).addClass( 'wp-mapit-row' ).append(
						jQuery( '<label>' ).text( wp_mapit_multipin.marker_title_text )
					).append(
						jQuery( '<input type="text">' ).attr( 'name', _pinid + '[' + _counter + '][marker_title]' ).data( 'counter', _counter ).addClass( 'pin_title' )
					)
				).append(
					jQuery( '<div>' ).addClass( 'wp-mapit-row' ).append(
						jQuery( '<label>' ).text( wp_mapit_multipin.marker_content_text )
					).append(
						jQuery( '<textarea>' ).attr( 'name', _pinid + '[' + _counter + '][marker_content]' ).data( 'counter', _counter ).addClass( 'pin_content' )
					)
				).append(
					jQuery( '<div>' ).addClass( 'wp-mapit-row no-margin' ).append(
						jQuery( '<label>' ).text( wp_mapit_multipin.marker_url_text )
					).append(
						jQuery( '<input type="text">' ).attr( 'name', _pinid + '[' + _counter + '][marker_url]' ).data( 'counter', _counter ).addClass( 'pin_url' )
					)
				)
			).append(
				jQuery( '<div>' ).addClass( 'column-3 no-margin' ).append(
					jQuery( '<div>' ).addClass( 'wp-mapit-row no-margin' ).append(
						jQuery( '<label>' ).text( wp_mapit_multipin.map_text )
					).append(
						jQuery( '<div>' ).attr( 'id', 'pin_map_' + _counter ).data( 'counter', _counter ).addClass( 'pin_map' )
					)
				)
			).append(
				jQuery( '<div>' ).addClass( 'clearfix' )
			);

			jQuery( '#wpmi_mappin_container' ).append( _pinContainer );

			wp_mapit_mappin_pins[_counter] = {};
			
			tempMap = L.map( 'pin_map_' + _counter, { fullscreenControl: true, gestureHandling: true } ).setView( [ wp_mapit_multipin_lat, wp_mapit_multipin_lng ], wp_mapit_multipin_zoom );
			tempMap._counter = _counter;


			tempMap.on( 'click', function(e){
				_lat = e.latlng.lat;
				_lng = e.latlng.lng;

				set_pin_map_marker( this._counter, _lat, _lng );
			} );

			wp_mapit_mappin_pins[_counter]['map'] = tempMap;
			wp_mapit_mappin_pins[_counter]['base_layer'] = null;
			wp_mapit_mappin_pins[_counter]['marker'] = null;

			wp_mapit_multipin_pins[_counter] = null;

			set_pin_map_base_layer( _counter );

			// set csv file data
		    if(pin_data) {
        		jQuery("#pin_container_"+ _counter +" .pin_latitude").val((pin_data[0] != undefined ? pin_data[0] : '' ));
        		jQuery("#pin_container_"+ _counter +" .pin_longitude").val((pin_data[1] != undefined ? pin_data[1] : '' ));
        		jQuery("#pin_container_"+ _counter +" .pin_title").val((pin_data[2] != undefined ? pin_data[2] : '' ));
        		jQuery("#pin_container_"+ _counter +" .pin_content").val((pin_data[3] != undefined ? pin_data[3] : '' ));
        		// jQuery("#pin_container_"+ _counter +" .pin_url").val((pin_data[4] != undefined ? pin_data[4] : '' ));
		    }

		    if( pin_data == undefined || pin_data.length == 0 ) {
				jQuery('html, body').animate({
			        scrollTop: jQuery('#pin_container_' + _counter).offset().top
			    }, 2000);
		    }
		}
	}

});