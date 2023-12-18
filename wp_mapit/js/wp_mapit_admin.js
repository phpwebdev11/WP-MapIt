jQuery( document ).ready( function(){
	/* Upload default map pin */
	jQuery( '#upload_map_pin' ).click( function( e ){
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
			title: wp_mapit.choose_image_text,
			button: {
				text: wp_mapit.choose_image_text
			},
			multiple: false
		});

		cpimageuploaded.on('select', function(){
			attachment  = cpimageuploaded.state().get( 'selection' ).first().toJSON();

			imageUrl = attachment.sizes.full.url;

			_this.parent().find( 'img' ).attr( 'src', imageUrl );
			_this.parent().find( 'input[type="hidden"]' ).val( imageUrl );

		});

		cpimageuploaded.open();
	} );

	/* Reset map pin */
	jQuery( '#reset_map_pin' ).click( function(){
		_this = jQuery( this );

		_this.parent().find( 'img' ).attr( 'src', _this.data( 'default-pin' ) );
		_this.parent().find( 'input[type="hidden"]' ).val( _this.data( 'default-pin' ) );
	} );

	/* Upload image */
	jQuery( document ).on( 'click', 'a.upload_image', function( e ){
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
			title: wp_mapit.choose_image_text,
			button: {
				text: wp_mapit.choose_image_text
			},
			multiple: false
		});

		cpimageuploaded.on('select', function(){
			attachment  = cpimageuploaded.state().get( 'selection' ).first().toJSON();

			imageUrl = attachment.sizes.full.url;

			if( _this.parent().find( 'img' ).length == 0 ){
				_this.before( jQuery( '<img>' ).attr( 'src', imageUrl ) );
			} else {
				_this.parent().find( 'img' ).attr( 'src', imageUrl );
			}

			_this.parent().find( 'input[type="hidden"]' ).val( imageUrl ).trigger( 'change' );

		});

		cpimageuploaded.open();
	} );

	/* Remove image */
	jQuery( document ).on( 'click', 'a.remove_image', function( e ){
		e.preventDefault();

		_this = jQuery( this );

		_this.parent().find( 'img' ).remove();
		_this.parent().find( 'input[type="hidden"]' ).val( '' ).trigger( 'change' );

	} );

} );

var wp_mapit_map = null;
var wp_mapit_base_layer = null;
var wp_mapit_map_type = null;
var wp_mapit_map_zoom = null;
var wp_mapit_map_marker = null;
var wp_mapit_map_marker_image = null;
var wp_mapit_default_map_marker_image = null;
var typingTimer;

function wp_mapit_set_marker(_lat, _lng) {

	if( wp_mapit_map != null ){
		wp_mapit_map.setView(new L.LatLng(_lat, _lng), wp_mapit_map_zoom);

		/* Remove existing marker */
		if( wp_mapit_map_marker != null ){
			wp_mapit_map.removeLayer( wp_mapit_map_marker );
			wp_mapit_map_marker = null;
		}

		/* Set values in text fields */
		jQuery( '#wpmi_map_latitiude' ).val( _lat );
		jQuery( '#wpmi_map_longitude' ).val( _lng );
		jQuery( '#wpmi_map_zoom' ).val( wp_mapit_map_zoom );

		/* Add marker */
		_img = new Image();
		_img.src = wp_mapit_map_marker_image;
		_img.onload = function() {

			_height = ( this.height > 100 ? 100 : this.height );
			_width = ( this.width > 100 ? 100 : this.width );

			_halfWidth = _width / 2;

			wp_mapit_map_marker = new L.Marker( [_lat, _lng], { 
				icon: L.icon( { iconUrl: this.src, iconSize: [ _width, _height ], iconAnchor: [ _halfWidth, _height ] } ),
				draggable: true
			} ).addTo( wp_mapit_map );

			wp_mapit_map_marker.on( 'moveend', function() {
				_center = wp_mapit_map_marker.getLatLng();
				
				/*_lat = _center.lat;
				_lng = _center.lng;*/

				/*wp_mapit_set_marker( _lat, _lng );*/

				jQuery( '#wpmi_map_latitiude' ).val( _center.lat );
				jQuery( '#wpmi_map_longitude' ).val( _center.lng );
			} );

			/* Triggered wpmi_marker_title blur event so that the popup is created if the text exists. */
			jQuery( '#wpmi_marker_title' ).trigger( 'blur' );
		}
	}
}

jQuery( window ).on( 'load', function(){
	/* Display of map in the admin panel */
	if( jQuery( '#wp_mapit_map' ).length > 0 ) {

		wp_mapit_map_zoom = parseInt( jQuery( '#wp_mapit_map' ).data( 'zoom' ) );
		_lat = parseFloat( jQuery( '#wp_mapit_map' ).data( 'latitude' ) );
		_lng = parseFloat( jQuery( '#wp_mapit_map' ).data( 'longitude' ) );
		wp_mapit_map_type = jQuery( '#wp_mapit_map' ).data( 'maptype' );
		wp_mapit_default_map_marker_image = wp_mapit_map_marker_image = jQuery( '#wp_mapit_map' ).data( 'marker' );

		fieldLat = jQuery.trim( jQuery( '#wpmi_map_latitiude' ).val() );
		if( fieldLat != '' && fieldLat != 0 ) {
			_lat = fieldLat;
		}

		fieldLng = jQuery.trim( jQuery( '#wpmi_map_longitude' ).val() );
		if( fieldLng != '' && fieldLng != 0 ) {
			_lng = fieldLng;
		}

		fieldZoom = jQuery.trim( jQuery( '#wpmi_map_zoom' ).val() );
		if( fieldZoom != '' && fieldZoom != 0 ) {
			wp_mapit_map_zoom = fieldZoom;
		}

		fieldMarkerImage = jQuery.trim( jQuery( '#wpmi_marker_image' ).val() );
		if( fieldMarkerImage != '' ) {
			wp_mapit_map_marker_image = fieldMarkerImage;
		}		

		wp_mapit_map = L.map('wp_mapit_map', { fullscreenControl: true, gestureHandling: true } ).setView([ _lat, _lng], wp_mapit_map_zoom);

		wp_mapit_map.on( 'click', function(e){
			_lat = e.latlng.lat;
			_lng = e.latlng.lng;

			wp_mapit_set_marker(_lat, _lng);
		} );

		wp_mapit_map.on( 'zoomend', function() {
			wp_mapit_map_zoom = wp_mapit_map.getZoom();

			jQuery( '#wpmi_map_zoom' ).val( wp_mapit_map_zoom );
		} );

		wp_mapit_map.on( 'moveend', function() {
			var _center = wp_mapit_map.getCenter();

			jQuery( '#wpmi_map_latitiude' ).val( _center.lat );
			jQuery( '#wpmi_map_longitude' ).val( _center.lng );
			jQuery( '#wpmi_map_zoom' ).val( wp_mapit_map_zoom );
			
		} );

		/* Search location */
		jQuery( '#wpmi_search_btn' ).click( function( e ){
			e.preventDefault();

			_this = jQuery( this );

			if( ! _this.hasClass('disbaled') ) {

				_this.addClass( 'disbaled' );
				_this.text( wp_mapit.please_wait_text );

				jQuery.ajax( {
					url: wp_mapit.ajax_url,
					data: 'action=wp_mapit_location_search&q=' +
						escape( jQuery( '#wpmi_search' ).val() ) +
						'&wp_mapit_ajax=' + wp_mapit.ajax_nonce,
					success: function( data ){
						data = JSON.parse( data );

						if( data.status == '1' ) {
							if( data.data['error'] != null && data.data['error']['message'] != null ) {
								alert( data.data['error']['message'] );
							} else {

								_lat = data.data[0].lat;
								_lng = data.data[0].lon;

								wp_mapit_set_marker(_lat, _lng);

								jQuery( '#wpmi_map_zoom' ).val( wp_mapit_map_zoom );
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

		/* Manually change lat lng */
		jQuery( '#wpmi_map_latitiude, #wpmi_map_longitude' ).blur( function() {

			_lat = jQuery.trim( jQuery( '#wpmi_map_latitiude' ).val() );
			_lng = jQuery.trim( jQuery( '#wpmi_map_longitude' ).val() );

			if( _lat != '' && _lng != '' ) {
				wp_mapit_set_marker(_lat, _lng);
			} else {
				/* Remove existing marker */
				if( wp_mapit_map_marker != null ){
					wp_mapit_map.removeLayer( wp_mapit_map_marker );
					wp_mapit_map_marker = null;
				}
			}

		} );

		jQuery( '#wpmi_map_zoom' ).blur( function() {
			var _zoom = parseFloat( jQuery( this ).val() );

			if( isNaN( _zoom ) || _zoom < 1 || _zoom > 20 ) {
				_zoom = wp_mapit_map_zoom;
			}

			wp_mapit_map_zoom = _zoom;
			jQuery( this ).val( wp_mapit_map_zoom );
			wp_mapit_map.setZoom( wp_mapit_map_zoom )
		} );


		jQuery( '#wpmi_marker_title, #wpmi_marker_content, #wpmi_marker_url' ).blur( function(){

			if( wp_mapit_map_marker != null ) {

				wp_mapit_map_marker.closePopup();
				wp_mapit_map_marker.unbindPopup();

				wp_mapit_map_marker.off('click');

				_url = jQuery.trim( jQuery( '#wpmi_marker_url' ).val() );

				if( _url != '' ) {
					wp_mapit_map_marker._url = _url;
					wp_mapit_map_marker.on('click', function() {
						window.open( this._url );
					});
				} else {
					_title = jQuery( '#wpmi_marker_title' ).val();
					_content = jQuery( '#wpmi_marker_content' ).val();
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
						wp_mapit_map_marker.bindPopup( popup );
					}
				}				
			}

		} );

		/* Change in map marker image */
		jQuery( '#wpmi_marker_image' ).change( function() {

			_pinUrl = jQuery( this ).val();

			if( wp_mapit_map_marker != null ) {
				if( _pinUrl == '' ) {
					_pinUrl = wp_mapit_default_map_marker_image;
				}

				_img = new Image();
				_img.src = _pinUrl;
				_img.onload = function() {
					_height = ( this.height > 100 ? 100 : this.height );
					_width = ( this.width > 100 ? 100 : this.width );

					_halfWidth = _width / 2;

					wp_mapit_map_marker.setIcon( L.icon( { iconUrl: this.src, iconSize: [ _width, _height ], iconAnchor: [ _halfWidth, _height ] } ) );

					/* Triggered blur to reset the popup */
					jQuery( '#wpmi_marker_title' ).trigger( 'blur' );
				}
			}
		} );

		jQuery( '#wpmi_map_zoom, #wpmi_map_latitiude, #wpmi_map_longitude, #wpmi_marker_title, #wpmi_marker_content, #wpmi_marker_url' ).on( 'keyup', function() {
			clearTimeout( typingTimer );
 			
 			_this = jQuery( this );

  			typingTimer = setTimeout( function() {
  				_this.trigger( 'blur' );
  			}, 1000);
		} ).on( 'keydown', function() {
			clearTimeout( typingTimer );
		} );

		jQuery( '#wpmi_map_type' ).change( function() {
			_layerImage = '//{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
			_attribution = 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors';

			_val = jQuery.trim( jQuery( this ).val() );

			if( _val == '' ) {
				_val = wp_mapit_map_type;
			}

			var _class = '';

			switch( _val ) {
				case 'grayscale':
					_class = 'grayscale';
					break;
				case 'topographic':
					_layerImage = '//{s}.tile.opentopomap.org/{z}/{x}/{y}.png';
					_attribution = 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)';
			}

			if( wp_mapit_base_layer != null ){
				wp_mapit_map.removeLayer( wp_mapit_base_layer );
			}

			wp_mapit_base_layer = L.tileLayer( _layerImage , {
				attribution: wp_mapit.plugin_attribution + _attribution,
				className: _class
			}).addTo(wp_mapit_map);

		} );

		jQuery( '#wpmi_map_type' ).trigger( 'change' );

		/* At last set map marker for update */
		if( fieldLat != '' && fieldLat != 0 && fieldLng != '' && fieldLng != 0 ){
			wp_mapit_set_marker(_lat, _lng);
		}
	}
} );