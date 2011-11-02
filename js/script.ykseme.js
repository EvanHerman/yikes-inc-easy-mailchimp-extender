/* Custom jQuery function */
(function($){
	if(!$.fn.yksYellowFade)
		{
		$.fn.yksYellowFade = function()
			{
  	  this.animate( { backgroundColor: "#FFFFCC" }, 1 ).animate( { backgroundColor: $(this).css('background-color') }, 1500 );
  		}
  	}
})(jQuery);

/* Run on document load */
jQuery(document).ready(function($)
{


	
});