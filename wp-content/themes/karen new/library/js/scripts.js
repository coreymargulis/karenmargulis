/*
 * Bones Scripts File
 * Author: Eddie Machado
 *
 * This file should contain any js scripts you want to add to the site.
 * Instead of calling it in the header or throwing it inside wp_head()
 * this file will be called automatically in the footer so as not to
 * slow the page load.
 *
 * There are a lot of example functions and tools in here. If you don't
 * need any of it, just remove it. They are meant to be helpers and are
 * not required. It's your world baby, you can do whatever you want.
*/

//mobile menu
(function() {
	var triggerBttn = document.getElementById( 'trigger-overlay' ),
		overlay = document.querySelector( 'div.overlay' ),
		closeBttn = overlay.querySelector( 'button.overlay-close' );
		transEndEventNames = {
			'WebkitTransition': 'webkitTransitionEnd',
			'MozTransition': 'transitionend',
			'OTransition': 'oTransitionEnd',
			'msTransition': 'MSTransitionEnd',
			'transition': 'transitionend'
		},
		transEndEventName = transEndEventNames[ Modernizr.prefixed( 'transition' ) ],
		support = { transitions : Modernizr.csstransitions };

	function toggleOverlay() {
		if( classie.has( overlay, 'open' ) ) {
			classie.remove( overlay, 'open' );
			classie.add( overlay, 'close' );
			var onEndTransitionFn = function( ev ) {
				if( support.transitions ) {
					if( ev.propertyName !== 'visibility' ) return;
					this.removeEventListener( transEndEventName, onEndTransitionFn );
				}
				classie.remove( overlay, 'close' );
			};
			if( support.transitions ) {
				overlay.addEventListener( transEndEventName, onEndTransitionFn );
			}
			else {
				onEndTransitionFn();
			}
		}
		else if( !classie.has( overlay, 'close' ) ) {
			classie.add( overlay, 'open' );
		}
	}

	triggerBttn.addEventListener( 'click', toggleOverlay );
	closeBttn.addEventListener( 'click', toggleOverlay );
})();

//search modal
(function() {
	var triggerBttn = document.getElementById( 'trigger-overlay-search' ),
		overlay = document.querySelector( 'div.overlay-search' ),
		closeBttn = overlay.querySelector( 'button.overlay-close' );
		transEndEventNames = {
			'WebkitTransition': 'webkitTransitionEnd',
			'MozTransition': 'transitionend',
			'OTransition': 'oTransitionEnd',
			'msTransition': 'MSTransitionEnd',
			'transition': 'transitionend'
		},
		transEndEventName = transEndEventNames[ Modernizr.prefixed( 'transition' ) ],
		support = { transitions : Modernizr.csstransitions };

	function toggleOverlay() {
		if( classie.has( overlay, 'open' ) ) {
			classie.remove( overlay, 'open' );
			classie.add( overlay, 'close' );
			var onEndTransitionFn = function( ev ) {
				if( support.transitions ) {
					if( ev.propertyName !== 'visibility' ) return;
					this.removeEventListener( transEndEventName, onEndTransitionFn );
				}
				classie.remove( overlay, 'close' );
			};
			if( support.transitions ) {
				overlay.addEventListener( transEndEventName, onEndTransitionFn );
			}
			else {
				onEndTransitionFn();
			}
		}
		else if( !classie.has( overlay, 'close' ) ) {
			classie.add( overlay, 'open' );
		}
	}

	triggerBttn.addEventListener( 'click', toggleOverlay );
	closeBttn.addEventListener( 'click', toggleOverlay );
})();


/*
 * Put all your regular jQuery in here.
*/
jQuery(document).ready(function($) {


			$("#comments").click(function(){
					$(".comments").toggle();
			});

			$("#share").click(function(){
					$(".share-modal").toggle();
			});

			$("#share").click(function(e){
			    e.preventDefault();
			    $(".popover").fadeIn(300,function(){$(this).focus();});
			});
			$(".popover").on('blur',function(){
			    $(this).fadeOut(300);
			});



			//Share popups

			;(function($){

		  /**
		   * jQuery function to prevent default anchor event and take the href * and the title to make a share pupup
		   *
		   * @param  {[object]} e           [Mouse event]
		   * @param  {[integer]} intWidth   [Popup width defalut 500]
		   * @param  {[integer]} intHeight  [Popup height defalut 400]
		   * @param  {[boolean]} blnResize  [Is popup resizeabel default true]
		   */
		  $.fn.customerPopup = function (e, intWidth, intHeight, blnResize) {

		    // Prevent default anchor event
		    e.preventDefault();

		    // Set values for window
		    intWidth = intWidth || '500';
		    intHeight = intHeight || '400';
		    strResize = (blnResize ? 'yes' : 'no');

		    // Set title and open popup with focus on it
		    var strTitle = ((typeof this.attr('title') !== 'undefined') ? this.attr('title') : 'Social Share'),
		        strParam = 'width=' + intWidth + ',height=' + intHeight + ',resizable=' + strResize,
		        objWindow = window.open(this.attr('href'), strTitle, strParam).focus();
		  };

		  /* ================================================== */

		  $(document).ready(function ($) {
		    $('.customer.share').on("click", function(e) {
		      $(this).customerPopup(e);
		    });
		  });

		}(jQuery));


}); /* end of as page load scripts */
