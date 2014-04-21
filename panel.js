/* ANTI TOR JS */
var $ = jQuery;
var optionsDiv;

$(initPage); // Document Ready

function initPage() {
	// Page load event
	optionsDiv = $("#hidden-options");
	// Link click event to handler function
	$("#at_block").on("click", atBlockClickHandler);
}

function atBlockClickHandler(event) {
	if ( $(event.currentTarget).is(":checked") ) { // If Checked
		// Show options
		optionsDiv.slideDown("fast");
	} else {
		// Hide options
		optionsDiv.slideUp("fast");
	}
}