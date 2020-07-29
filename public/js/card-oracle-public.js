( function( $ ) {
	'use strict';

	/**
	 * Card Oracle JS code for the public pages
	 */

	$( document ).ready( function() {
		let clickedButtons = new Array();
		let reverseButtons = new Array();
		let count = 0;
		let positions = $( "div.data" ).data( "positions" );
	
		console.log( 'Number of card positions is ' + positions );
		$( "#Submit" ).prop( "disabled", true );
	
		$( 'button.clicked' ).click( function() {
			count++;
	
			if ( count <= positions ) {
				$( this ).closest( '.card-oracle-card-body').toggleClass( 'is-flipped' );
				$( this ).fadeOut( 800 );

				clickedButtons.push( this.value );
				if ( $( this ).attr( 'data-value' ) ) {
					reverseButtons.push( this.value );
					console.log( this.value );
				}

				$( "#picks" ).val( clickedButtons.join() );
				$( "#reverse" ).val( reverseButtons.join() );

				if ( count == positions ) {
					$( ".btn-block" ).show();
					$( ".btn-block" ).css( 'opacity', '1' );
					$( "#Submit" ).prop( "disabled", false );
					$( 'html, body' ).animate( {
						scrollTop: ( $( "h1" ).offset().top )
					}, 500 );
				}
			}
		});

		$( "#card-oracle-question" ).on( 'submit', function( e ) {
			console.log( 'Submit button clicked' );
		});

		$( "#card-oracle-subscribe" ).on('change', function() {
			$( this ).val( this.checked ? "true" : "false" );
		});

		$( "#reading-send" ).click( function( e ){
				
			e.preventDefault(); // if the clicked element is a link
					
			$.post( $( "#ajax_url" ).val(), {
				action: 'send_reading_email',
				email: $( "#emailaddress" ).val(),
				emailcontent: $( "#emailcontent" ).val(),
				subscribe: $( "#card-oracle-subscribe" ).val(),
			}, function( response ) {
				// handle a successful response
				$( '.card-oracle-response' ).html( response.data );
				console.log( response )
			});

			// display success message
			$( '.card-oracle-response' ).html( response.data );
			});
		
	} )

})( jQuery );