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


//Twitter
// window.twttr = (function(d, s, id) {
//   var js, fjs = d.getElementsByTagName(s)[0],
//     t = window.twttr || {};
//   if (d.getElementById(id)) return t;
//   js = d.createElement(s);
//   js.id = id;
//   js.src = "https://platform.twitter.com/widgets.js";
//   fjs.parentNode.insertBefore(js, fjs);
//
//   t._e = [];
//   t.ready = function(f) {
//     t._e.push(f);
//   };
//
//   return t;
// }(document, "script", "twitter-wjs"));

//Facebook
// (function(d, s, id) {
//   var js, fjs = d.getElementsByTagName(s)[0];
//   if (d.getElementById(id)) return;
//   js = d.createElement(s); js.id = id;
//   js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6";
//   fjs.parentNode.insertBefore(js, fjs);
// }(document, 'script', 'facebook-jssdk'));


/*
 * Put all your regular jQuery in here.
*/
jQuery(document).ready(function($) {


			$("#comments").click(function(){
					$(".comments").toggle();
			});


}); /* end of as page load scripts */
