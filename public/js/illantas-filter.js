(function( $ ) {
	'use strict';

	const baseURL = window.location.origin; // window.location.protocol + '//' + window.location.host;
	const principalTax = 'pa_marca';

	// Validamos si exixte el contenedor del shortcode
	if ( $('.illantas-filter-container').length ){

		$('.idropdown').on('change', function(){

			// Se esta seleccionando el atributo de marca enviamos a una página de marca
			if ( $(this).data('filter-url') == principalTax ){
				const basePage =  $(this).val() ? '/marca-' + $(this).val() : '/marca';
				window.location = baseURL + basePage;
				return false;
			}
			// Para otros atributos
			const currentURL = baseURL + window.location.pathname;

			// Formamos los parámetros del filtro
			let urlParams = new Object;

			$('.idropdown').each(function(){
 				// excluimos el filtro principal
				if ( $(this).data('filter-url') == principalTax ) return;

				// Si es diferente de todos
				if ( $(this).val() ){
					urlParams[$(this).data('filter-url')] = $(this).val()
				}
			});

			let strParams = $.param( urlParams );
			strParams = strParams ? '?' + strParams :'';

			window.location = currentURL + strParams;
			return false;
		});

	}

})( jQuery );
