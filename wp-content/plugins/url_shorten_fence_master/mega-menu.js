jQuery(document).ready(function(){ 

/* Make TOC Menu Tag */
function make_toc_menu() {
	var toc_content = "<div id='mega_menu'>" 
					+ "<span class='mega_toggle'>" + jQuery("#content h1").html() +"</span>" 
					+ "<ul class='mega_toc_list'>" + jQuery("#toc_container .toc_list").html() +  "</ul>"
					+ "</div>";
	if ((typeof(jQuery("#toc_container .toc_list").html()) !== 'undefined') && 
		(typeof(jQuery("#content h1").html()) !== 'undefined') ){
		jQuery("body").append(toc_content);
	}
}
make_toc_menu();

jQuery("#mega_menu span.mega_toggle").on("click", function(){
	jQuery("#mega_menu .mega_toc_list").toggle();
});

jQuery("#mega_menu .mega_toc_list").on("click", function(){
	jQuery(this).hide();
});

});
