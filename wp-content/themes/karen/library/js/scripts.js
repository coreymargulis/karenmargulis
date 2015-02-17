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


  
  

var container = document.querySelector('.products');
var msnry = new Masonry( container, {
  // options
  itemSelector: '.product',
  columnWidth: container.querySelector('.products')


/*
var container = document.querySelector('.products');
var msnry;
// initialize Masonry after all images have loaded
imagesLoaded( container, function() {
  msnry = new Masonry( container );
*/
 




}); /* end of as page load scripts */