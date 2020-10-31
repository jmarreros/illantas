// Para establecer la barra como Fixed

(function( $ ) {
	'use strict';

	const baseURL = window.location.origin; // window.location.protocol + '//' + window.location.host;
	const marcaTax = 'pa_marca';
	const fabricanteTax = 'pa_fabricante';
	const marcaPages = '/llantas-para-';
	const fabricantesPages = '/llantas-';
	let currentPage = dcms_vars.currentPage; // variable pasada desde WordPress

	// Validamos si exixte el contenedor del shortcode
	if ( $('.illantas-filter-container').length ){

		// Boton limpiar
		$('.btn-filter-clean').attr('href', baseURL + currentPage);

		// Evento de cambio en los selects
		$('.idropdown').on('change', function(){

			// Si estamos en el home no tiene que ir a página de marcas ni página de fabricantes
			if ( currentPage !== '/' ){
				// Se esta seleccionando el atributo de marca enviamos a una página de marca
				if ( $(this).data('filter-url') == marcaTax ){
					const basePage =  $(this).val() ? marcaPages + $(this).val() : currentPage;
					window.location = baseURL + basePage;
					return false;
				}

				// Se esta seleccionando el atributo de fabricante enviamos a una página de fabricante
				if ( $(this).data('filter-url') == fabricanteTax ){
					const basePage =  $(this).val() ? fabricantesPages + $(this).val() : currentPage;
					window.location = baseURL + basePage;
					return false;
				}
			}

			// Para otros atributos
			const currentURL = baseURL + window.location.pathname.replace(/\/page\/\d+/i,"");

			// Formamos los parámetros del filtro
			let urlParams = new Object;

			$('.idropdown').each(function(){

				// Validación si est en el home no debe haber exclusiones para marca y fabricantes
				if ( currentPage !== '/' ){
					// excluimos el filtro de marca y de fabricante
					if ( $(this).data('filter-url') == marcaTax || 
					$(this).data('filter-url') == fabricanteTax) return;
				}

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

	const options = {
		root: null,
		rootMargin: '0px 0px 800px 0px',
		threshold: 1,
	}

	const observer = new IntersectionObserver(dcmsFixedSidebar, options)
	observer.observe(document.getElementById('fixed-sidebar-aux'));

	function dcmsFixedSidebar( entries, observer ){

		if (window.innerWidth < 978) return; //movil validation

		if ( entries[0].isIntersecting ){
			$('.illantas-filter-sidebar').insertBefore('.illantas-filter-products');
		} else {
			$('.illantas-filter-sidebar').appendTo('.header-main');
		}
	}

})( jQuery );
