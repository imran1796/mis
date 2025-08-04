(function($) {

	"use strict";

	var fullHeight = function() {

		$('.js-fullheight').css('height', $(window).height());
		$(window).resize(function(){
			$('.js-fullheight').css('height', $(window).height());
		});

	};
	fullHeight();

	$('#sidebarCollapse').on('click', function () {
      $('#sidebar').toggleClass('active');
      $('#content').toggleClass('sidebar-active');
  });

  function adjustSidebar() {
	        if ($(window).width() <= 900) {
	            $('#sidebar').addClass('active'); // Hide it
	            $('#content').addClass('sidebar-active');
	        } else {
	            $('#sidebar').removeClass('active'); // Show sidebar on large screens
	            $('#content').removeClass('sidebar-active');
	        }
	    }
	
	    // Call on page load
	    adjustSidebar();
	
	    // Call on window resize
	    $(window).resize(adjustSidebar);

})(jQuery);
