var wp_mapit_settings_map = null;
var wp_mapit_settings_base_layer = null;
var wp_mapit_settings_map_zoom = 0;
var wp_mapit_settings_map_lat = 0;
var wp_mapit_settings_map_lng = 0;
var wp_mapit_settings_map_marker = null;
var wp_mapit_settings_map_marker_image = null;

var typingTimer;

function set_map_latlng() {
	jQuery( '#wpmi_latitude' ).val( wp_mapit_settings_map_lat );
	jQuery( '#wpmi_longitude' ).val( wp_mapit_settings_map_lng );

	wp_mapit_settings_map.setView( new L.LatLng( wp_mapit_settings_map_lat, wp_mapit_settings_map_lng ), wp_mapit_settings_map_zoom );
}

jQuery( window ).load( function(){

	if( jQuery( '#admin_setting_map' ).length > 0 ) {
		wp_mapit_settings_map_lat = jQuery( '#wpmi_latitude' ).val();
		wp_mapit_settings_map_lng = jQuery( '#wpmi_longitude' ).val();
		wp_mapit_settings_map_zoom = jQuery( '#wpmi_map_zoom' ).val();

		wp_mapit_settings_map = L.map( 'admin_setting_map' ).setView([ wp_mapit_settings_map_lat, wp_mapit_settings_map_lng], wp_mapit_settings_map_zoom);

		wp_mapit_settings_map.on( 'zoomend', function() {
			wp_mapit_settings_map_zoom = wp_mapit_settings_map.getZoom();

			jQuery( '#wpmi_map_zoom' ).val( wp_mapit_settings_map_zoom );
		} );

		wp_mapit_settings_map.on( 'moveend', function() {
			var _center = wp_mapit_settings_map.getCenter();

			wp_mapit_settings_map_lat = _center.lat;
			wp_mapit_settings_map_lng = _center.lng;

			jQuery( '#wpmi_latitude' ).val( wp_mapit_settings_map_lat );
			jQuery( '#wpmi_longitude' ).val( wp_mapit_settings_map_lng );

		} );

		jQuery( '#wpmi_map_type' ).change( function() {
			_layerImage = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
			_attribution = 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors';

			_isGray = false;

			switch( jQuery( this ).val() ) {
				case 'normal':
					_layerImage = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
					break;
				case 'grayscale':
					_isGray = true;
					_layerImage = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
					//_layerImage = 'https://tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png';
					_attribution = 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';
					break;
				case 'topographic':
					_layerImage = 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png';
					_attribution = 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)';
			}

			if( wp_mapit_settings_base_layer != null ){
				wp_mapit_settings_map.removeLayer( wp_mapit_settings_base_layer );
			}

			if( _isGray ) {
				wp_mapit_settings_base_layer = L.tileLayer.grayscale( _layerImage , {
					attribution: wp_mapit.plugin_attribution + _attribution
				}).addTo(wp_mapit_settings_map);
			} else {
				wp_mapit_settings_base_layer = L.tileLayer( _layerImage , {
					attribution: wp_mapit.plugin_attribution + _attribution
				}).addTo(wp_mapit_settings_map);
			}

		} );

		jQuery( '#wpmi_map_zoom' ).blur( function() {
			var _zoom = parseFloat( jQuery( this ).val() );

			if( isNaN( _zoom ) || _zoom < 1 || _zoom > 20 ) {
				_zoom = wp_mapit_settings_map_zoom;
			}

			wp_mapit_settings_map_zoom = _zoom;
			jQuery( this ).val( wp_mapit_settings_map_zoom );
			wp_mapit_settings_map.setZoom( wp_mapit_settings_map_zoom )
		} );

		jQuery( '#wpmi_latitude, #wpmi_longitude' ).blur( function() {

			_lat = jQuery.trim( jQuery( '#wpmi_latitude' ).val() );
			_lng = jQuery.trim( jQuery( '#wpmi_longitude' ).val() );

			if( _lat != '' && ! isNaN( _lat ) ) {
				wp_mapit_settings_map_lat = _lat;
			} else 

			if( _lng != '' && ! isNaN( _lng ) ) {
				wp_mapit_settings_map_lng = _lng;
			}

			set_map_latlng()

		} );

		jQuery( '#search_map_btn' ).click( function( e ){
			e.preventDefault();

			_this = jQuery( this );

			if( ! _this.hasClass('disbaled') ) {

				_this.addClass( 'disbaled' ).attr( 'disbaled', 'disbaled' );
				_this.val( wp_mapit.please_wait_text );

				jQuery.ajax( {
					url: wp_mapit.ajax_url,
					data: 'action=wp_mapit_location_search&q=' + escape( jQuery( '#search_map' ).val() ),
					success: function( data ){
						data = JSON.parse( data );

						if( data.status == '1' ) {

							if( data.data['error'] != null && data.data['error']['message'] != null ) {
								alert( data.data['error']['message'] );
							} else {

								wp_mapit_settings_map_lat = data.data[0].lat;
								wp_mapit_settings_map_lng = data.data[0].lon;

								set_map_latlng();
							}

						} else if( data.message != '' ) {
							alert( data.message )
						}

						_this.removeClass( 'disbaled' ).removeAttr( 'disbaled' );
						_this.val( wp_mapit.search_text );
					},
					error: function(){
						_this.removeClass( 'disbaled' ).removeAttr( 'disbaled' );
						_this.val( wp_mapit.search_text );
						alert( wp_mapit.ajax_error_message );
					}
				} );
			}

		} );

		jQuery( '#wpmi_map_zoom, #wpmi_latitude, #wpmi_longitude' ).on( 'keyup', function() {
			clearTimeout( typingTimer );
 			
 			_this = jQuery( this );

  			typingTimer = setTimeout( function() {
  				_this.trigger( 'blur' );
  			}, 1000);
		} ).on( 'keydown', function() {
			clearTimeout( typingTimer );
		} );

		jQuery( '#wpmi_map_type' ).trigger( 'change' );
	}

} );