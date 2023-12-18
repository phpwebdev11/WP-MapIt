jQuery( window ).on( 'load', function(){
	if( jQuery( '.wp_mapit_map' ).length > 0 ) {
		jQuery( '.wp_mapit_map' ).each( function() {
			var _this = jQuery( this );

			var _id = _this.attr( 'id' );
			var _lat = _this.data( 'lat' );
			var _lng = _this.data( 'lng' );
			var _zoom = _this.data( 'zoom' );
			var _type = _this.data( 'type' );
			var _marker = _this.data( 'marker' );
			var _title = _this.data( 'title' );
			var _content = _this.data( 'content' );
			var _url = jQuery.trim( _this.data( 'url' ) );
			var _html = '';
			var _width = _this.data( 'width' );
			var _width_type = _this.data( 'width-type' );
			var _height = _this.data( 'height' );
			var _height_type = _this.data( 'height-type' );

			_this.css( { 'width' : _width + (_width_type == 'per' ? 'vw' : _width_type), 'height' : _height + (_height_type == 'per' ? 'vh' : _height_type), 'margin' : '0 auto', 'max-width' : '100%', 'min-width' : '300px', 'max-height' : '100%' } );

			var wp_mapit_map = L.map(_id, { fullscreenControl: true, gestureHandling: true } ).setView([ _lat, _lng], _zoom);

			var _layerImage = '//{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
			var _attribution = 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors';

			var _class = '';

			switch( _type ) {
				case 'grayscale':
					_class = 'grayscale';
					break;
				case 'topographic':
					_layerImage = '//{s}.tile.opentopomap.org/{z}/{x}/{y}.png';
					_attribution = 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)';
					break;
			}

			wp_mapit_base_layer = L.tileLayer(_layerImage, {
				attribution: wp_mapit.plugin_attribution + _attribution,
				className: _class
			}).addTo(wp_mapit_map);

			var _img = new Image();
			_img.src = _marker;
			_img.onload = function() {

				_img_height = ( this.height > 100 ? 100 : this.height );
				_img_width = ( this.width > 100 ? 100 : this.width );
				_img_halfWidth = _img_width / 2;

				var wp_mapit_map_marker = new L.Marker( [_lat, _lng], { 
					icon: L.icon( { iconUrl: this.src, iconSize: [ _img_width, _img_height ], iconAnchor: [ _img_halfWidth, _img_height ] } )
				} ).addTo( wp_mapit_map );

				wp_mapit_map_marker.closePopup();
				wp_mapit_map_marker.unbindPopup();
				wp_mapit_map_marker.off( 'click' );

				if( _url != '' ) {
					wp_mapit_map_marker._url = _url;
					wp_mapit_map_marker.on( 'click', function() {
						window.open( this._url );
					} );
				} else {
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
	}
} );