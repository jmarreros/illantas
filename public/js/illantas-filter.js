(function( $ ) {
	'use strict';

	const baseURL = window.location.origin; // window.location.protocol + '//' + window.location.host;
	const marcaTax = 'pa_marca';
	const fabricanteTax = 'pa_fabricante';
	const marcaPages = '/llantas-para-';
	const fabricantesPages = '/llantas-';
	const genericPage = '/llantas';

	// Validamos si exixte el contenedor del shortcode
	if ( $('.illantas-filter-container').length ){

		// Boton limpiar
		$('.btn-filter-clean').attr('href', baseURL + genericPage);

		// Evento de cambio en los selects
		$('.idropdown').on('change', function(){

			// Se esta seleccionando el atributo de marca enviamos a una página de marca
			if ( $(this).data('filter-url') == marcaTax ){
				const basePage =  $(this).val() ? marcaPages + $(this).val() : genericPage;
				window.location = baseURL + basePage;
				return false;
			}

			// Se esta seleccionando el atributo de fabricante enviamos a una página de fabricante
			if ( $(this).data('filter-url') == fabricanteTax ){
				const basePage =  $(this).val() ? fabricantesPages + $(this).val() : genericPage;
				window.location = baseURL + basePage;
				return false;
			}


			// Para otros atributos
			const currentURL = baseURL + window.location.pathname;

			// Formamos los parámetros del filtro
			let urlParams = new Object;

			$('.idropdown').each(function(){
 				// excluimos el filtro de marca y de fabricante
				if ( $(this).data('filter-url') == marcaTax || 
					$(this).data('filter-url') == fabricanteTax) return;

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
