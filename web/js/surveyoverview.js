$(document).ready(function(){

	$('body').on('click', 'a.display-section', function(e) {
		e.preventDefault();
		
		var id = $(this).attr("id").replace("show", "section");
		if ( $(this).html() == '<i class="fas fa-angle-down" aria-hidden="true"></i>' ){
			$(this).html('<i class="fas fa-angle-up" aria-hidden="true"></i>');
		}else{
			$(this).html('<i class="fas fa-angle-down" aria-hidden="true"></i>');
		}
		$("." + id ).toggle();
	});

});