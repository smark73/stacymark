function builder_get_elements_by_class_name( className ) {
	var hasClassName = new RegExp( "(?:^|\\s)" + className + "(?:$|\\s)" );
	var allElements = document.getElementsByTagName( "*" );
	var results = [];
	
	var element;
	for ( var i = 0; ( element = allElements[i] ) != null; i++ ) {
		var elementClass = element.className;
		if ( elementClass && elementClass.indexOf( className ) != -1 && hasClassName.test( elementClass ) )
			results.push( element );
	}
	
	return results;
}

builder_add_hover_code = function() {
	var menus = builder_get_elements_by_class_name( "builder-module-navigation" );
	
	if ( menus == undefined )
		return;
	
	for ( var x = 0; x < menus.length; x++ ) {
		var sfEls = menus[x].getElementsByTagName( "li" );
		
		for ( var i = 0; i < sfEls.length; i++ ) {
			sfEls[i].onmouseover = function() {
				this.className += " sfhover";
			}
			sfEls[i].onmouseout = function() {
				this.className = this.className.replace( new RegExp(" sfhover\\b"), "" );
			}
		}
	}
}
if ( window.attachEvent )
	window.attachEvent( "onload", builder_add_hover_code );
