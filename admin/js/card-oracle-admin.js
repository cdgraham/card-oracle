(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 */

	$( document ).on( "click", ".copy-action-btn", function() { 
		let trigger = $( this );
		$( ".copy-action-btn" ).removeClass( "text-success" );
		let $tempElement = $( "<input>" );
		$( "body").append( $tempElement );
		let copyText = this.value;
		$tempElement.val( copyText ).select();
		document.execCommand( "Copy" );
		$tempElement.remove();
		$( trigger ).addClass( "text-success" );
	});
	
})( jQuery );
