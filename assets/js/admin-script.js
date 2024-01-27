(function($){
	$(document).ready(function($){
		$('.color_purchased').wpColorPicker();
		$('.color_return_not_confirmed').wpColorPicker();
		$('.color_return_and_confirmed').wpColorPicker();
		$('.color_marked_broken').wpColorPicker();
		
		$.each(blocker_theme_colors.item_classes, function(index, value){
			$('.' + value).wpColorPicker();
			// console.log(value);
		});

	});
})(jQuery);