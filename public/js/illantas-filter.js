(function( $ ) {
	'use strict';

	const baseURL = window.location.origin; // window.location.protocol + '//' + window.location.host;
	const marcaTax = 'pa_marca';
	const fabricanteTax = 'pa_fabricante';

	// Validamos si exixte el contenedor del shortcode
	if ( $('.illantas-filter-container').length ){

		$('.idropdown').on('change', function(){

			// Se esta seleccionando el atributo de marca enviamos a una página de marca
			if ( $(this).data('filter-url') == marcaTax ){
				const basePage =  $(this).val() ? '/llantas-para-' + $(this).val() : '/llantas';
				window.location = baseURL + basePage;
				return false;
			}

			// Se esta seleccionando el atributo de fabricante enviamos a una página de fabricante
			if ( $(this).data('filter-url') == fabricanteTax ){
				const basePage =  $(this).val() ? '/llantas-' + $(this).val() : '/llantas';
				window.location = baseURL + basePage;
				return false;
			}


			// Para otros atributos
			const currentURL = baseURL + window.location.pathname;

			// Formamos los parámetros del filtro
			let urlParams = new Object;

			$('.idropdown').each(function(){
 				// excluimos el filtro principal
				if ( $(this).data('filter-url') == marcaTax ) return;

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
