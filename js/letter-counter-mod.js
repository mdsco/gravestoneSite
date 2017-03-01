( function( $ ) { 

	$( window ).load( function() {	

		$this = $(this);

		var current_item_key = 'no_key';

		// Checks for text elements added to the the FPD view, extracts non alpha-numeric
		// characters, stores a count of total characters in local storage as 'charCount'
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

			// verify FPD has any view instances
			if(fancyProductDesigner.currentViewInstance !== null){

				var count = 0;
				var text = '';

				// loop through object array on viewInstance stage to check for text elements
				for(var i = 0; i < fancyProductDesigner.currentViewInstance.stage._objects.length; i++){

					if(typeof fancyProductDesigner.currentViewInstance.stage._objects[i].text !== 'undefined'){

						// append text to cumulative string
						text += fancyProductDesigner.currentViewInstance.stage._objects[i].text;

						// extract non alpha-numeric characters
						text = text.replace(/[^a-zA-Z0-9]/g, '');

						//get character count
						count = text.length;

					}
				}

				$.ajax({

				    global: false,
				    type: "POST",
				    cache: false,
				    dataType: "json",
				    data: ({
				        action: 'write',
				        char_count : count,
				        product_key : current_item_key
				    }),
				    url: '/wordpress/wp-content/plugins/marksPlugin/get-letter-count.php',
				    success: function(response){
						console.log("Response: ", response);
					},
					error: function(error){
						console.log("Error: ", error);
					}
				});

				//set object in local storage (as string)
				localStorage.setItem('charCount', JSON.stringify(count + ""));

			}

		}

		// $(".single_add_to_cart_button").on('click', function(){

		// 	getCorrectCharacterCount();

		// });

		// update character count on elementAdd
		$this.on('elementAdd', function(){

			getCorrectCharacterCount();

		});

		// update character count on elementDelete
		$this.on('elementRemove', function(){

			getCorrectCharacterCount();

		});

	});

})( jQuery );