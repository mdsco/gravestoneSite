( function( $ ) { 

	$( window ).load( function() {	

		function manageCustomerId(){

			// var cookiesList = getCookies();
			var customer_id = '';

			//if the cookie is already set
			if(Cookies.get('letter_counter_user_id')){

				customer_id = Cookies.get('letter_counter_user_id');

				Cookies.remove('letter_counter_user_id', { path: '/wordpress/' }); 
				Cookies.set('letter_counter_user_id', customer_id, { expires: 3, path: '/wordpress/' });

			} else { 

				$.ajax({

					global: false,
				    type: "GET",
				    cache: false,
				    dataType: "json",
				    data: ({
				        action: 'write',
				        user_id: customer_id
				    }),
				    url: '/wordpress/wp-content/plugins/marksPlugin/manage-customer-id.php',
				    success: function(data, status){

				    	var user_id = data.user_id;
					    Cookies.set('letter_counter_user_id', user_id, { expires: 3, path: '/wordpress/' });

				    },
					error: function(error){
						console.log("Error: ", error);
					}

				});

			}

		}

		manageCustomerId();

		const pricePerChar = 5.00;
		const basePrice = 25.00;
		
		$(".price > .woocommerce-Price-amount").html('');

		$this = $(this);

		var current_item_key = 'no_key';

		// Checks for text elements added to the the FPD view, extracts non alpha-numeric
		// characters, stores a count of total characters in local storage as 'charCount'

		var getCartHash = function(){

			var name = "wp_woocommerce_session_";

			var cookies = document.cookie;
			var cookiesArray = cookies.split(';');

			cookiesArray.forEach(function(element){

				if(element.includes(name)){

					var keyValue = element.split("=");
					var char32Val = keyValue[1].split("%");

				}

			});

		};

		var getCorrectCharacterCount = function(){

			var session = sessionStorage.getItem('wc_fragments');

			if(session !== null){
				var key = session.match(/cart_item_key(.*?)\\/g);

				if(key !== null){
					
					for(var i = 0; i < key.length; i++){

						var noPrefix = key[i].replace('cart_item_key=','');
						var noSuffix = noPrefix.replace('\\','');
						key[i] = noSuffix;
				
					}

				}
			}
			
			var count = getCountOfCurrentlyDisplayedChars();

			//update count of item on server
			$.ajax({

			    global: false,
			    type: "POST",
			    cache: false,
			    dataType: "json",
			    data: ({
			        action: 'write',
			        char_count : count,
			        product_key : current_item_key,
			        user_id: Cookies.get('letter_counter_user_id')
			    }),
			    url: '/wordpress/wp-content/plugins/marksPlugin/get-letter-count.php',
			    success: function(response){
					//console.log("Response: ", response);
				},
				error: function(error){
					console.log("Error: ", error);
				}

			});
 
		}

		var getCountOfCurrentlyDisplayedChars = function(){

			if(fancyProductDesigner.currentViewInstance !== null){

				var count = 0;
				var text = '';

				// loop through object array on viewInstance stage to check for text elements
				for(var i = 0; i < fancyProductDesigner.currentViewInstance.stage._objects.length; i++){

					if(typeof fancyProductDesigner.currentViewInstance.stage._objects[i].text !== 'undefined'){

						// append text to cumulative string
						text += fancyProductDesigner.currentViewInstance.stage._objects[i].text;

						// extract whitespace characters
						text = text.replace(/[\s]/g, '');

						//get character count
						count = text.length;

					}

				}

				return count;

			}

			return 0;

		}

		var getCurrencyCode = function(locale){
			
			switch(locale) {

			    case 'fr-FR' :
			        return "EUR";
			    case 'en-GB':
			        return "GBP"
			    case 'en-HK':
			        return "HKD";
			    case 'en-US':
			        return "USD";
			    default:
			    	return "USD";
			
			}

		};

		var updateProductPagePrice = function(){

			if(fancyProductDesigner.currentViewInstance !== null){

				$.ajax({

					global: false,
					type: "GET",
					cache: false,
					url : 'http://api.fixer.io/latest?base=USD',
					success : function(data){

						var rates = data;
						var charCount = getCountOfCurrentlyDisplayedChars();

						var total = basePrice + (charCount * pricePerChar);
						console.log("Total: " + total);

						var locale = navigator.language || navigator.userLanguage;
						//locale = 'en-GB';

						currencyCode = getCurrencyCode(locale);

						fx.settings = { from: "USD" };

						fx.base = rates.base;
						fx.rates = rates.rates;
					 
						var convertVal = fx.convert(total, {to: currencyCode});

						var dollarString = convertVal.toLocaleString(locale,{style:'currency',currency:currencyCode})

						$(".price > .woocommerce-Price-amount").html(dollarString);
						console.log("Did load ");
					},
					error: function(err){
						console.log("Didn't load " + err);
					}

				});

				// var charCount = getCountOfCurrentlyDisplayedChars();

				// var total = basePrice + (charCount * pricePerChar);

				// var locale = navigator.language || navigator.userLanguage;
				// locale = 'en-GB';

				// var rates = {};


				// currencyCode = getCurrencyCode(locale);

				// fx.settings = { from: "USD" };

				// // fx.base = rates.base;
				// // fx.rates = rates.rates;
			 
				// fx.base = 'USD';
				// fx.rates = {
								
				// 	"GBP":0.81833,
				// 	"HKD":7.7655,
				// 	"EUR":0.93782,
				// 	"USD":1
				// };

				// var convertVal = fx.convert(2, {to: currencyCode});

				// var dollarString = convertVal.toLocaleString(locale,{style:'currency',currency:currencyCode})

				// $(".price > .woocommerce-Price-amount").html(dollarString);
			} 
		}

		getCartHash();

		$this.on('productCreate', function(){
			getCorrectCharacterCount();
			updateProductPagePrice();
		});

		$this.on('elementModify', function(){
			getCorrectCharacterCount();
			updateProductPagePrice();

		});

		// update character count on elementAdd
		$this.on('elementAdd', function(){
			getCorrectCharacterCount();
			updateProductPagePrice();
		});

		// update character count on elementDelete
		$this.on('elementRemove', function(){
			getCorrectCharacterCount();
			updateProductPagePrice();
		});

		$( ".single_variation_wrap" ).on( "show_variation", function() {
			// alert("WORK!");
			// clearTimeout( cart_timeout );
			// cart_timeout = setTimeout( refresh_cart_fragment, day_in_ms );
		});

		$( document ).on('mouseenter', '.cart > .single_add_to_cart_button', function( event ){

			$.ajax({

				global: false,
			    type: "POST",
			    cache: false,
			    dataType: "json",
			    data: ({
			        action: 'write',
			        user_id: Cookies.get('letter_counter_user_id')
			    }),
			    url: '/wordpress/wp-content/plugins/marksPlugin/user-id-temp-storage.php',
			    success: function(response){
					//console.log("Response: ", response);
				}

			});

		});



		$( document ).on('click', 'td.product-remove > a', function( event ){

			var a = $( event.currentTarget );
			var url = a.attr( 'href' );
			var cart_item_key = getParameterByName('remove_item', url);

			$.ajax({

				global: false,
			    type: "POST",
			    cache: false,
			    dataType: "json",
			    data: ({
			        action: 'write',
			        key : cart_item_key,
			        user_id: Cookies.get('letter_counter_user_id')
			    }),
			    url: '/wordpress/wp-content/plugins/marksPlugin/remove-letter-count-item.php',
			    success: function(response){
					//console.log("Response: ", response);
				}

			});

		});

		function getParameterByName(name, url) {

		    if (!url) {
		      url = window.location.href;
		    }
		    name = name.replace(/[\[\]]/g, "\\$&");
		    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
		        results = regex.exec(url);
		    if (!results) return null;
		    if (!results[2]) return '';
		    return decodeURIComponent(results[2].replace(/\+/g, " "));

		}

	});

})( jQuery );