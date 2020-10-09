( function( $ ) {
	function loftloader_finished() {
		$('body').addClass( 'loaded' );
	}
	if ( $( '#loftloader-wrapper' ).length ) {
		$(window).load( function(){ 
			loftloader_finished(); 
		} );

		$( document ).ready( function() {
			var $loader_wrapper = $( '#loftloader-wrapper' ),
				show_close_time = '';
			if ( $loader_wrapper.data( 'show-close-time' ) ) {
				show_close_time = parseInt( $loader_wrapper.data( 'show-close-time' ) );
				if( show_close_time ) {
					setTimeout( function(){ 
						$loader_wrapper.find( '.loader-close-button' ).css('display', '' ); 
					}, show_close_time );
					$( '.loader-close-button' ).on( 'click', function() {
						loftloader_finished();
					} );
				}
			}
		} );
	}
} )( jQuery );