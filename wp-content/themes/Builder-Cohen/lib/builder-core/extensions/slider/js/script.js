function builder_equalize_height(group) {
	var tallest = 0;
	jQuery(group).each(
		function() {
			var thisHeight = jQuery(this).height();
			if(thisHeight > tallest) {
				tallest = thisHeight;
			}
		}
	);
	jQuery(group).height(tallest);
}

jQuery(document).ready(
	function() {
		builder_equalize_height(".post");
	}
);
