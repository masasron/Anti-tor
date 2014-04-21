/* ANTI TOR JS */
var $ = jQuery;
var optionsDiv;

$(init_page); // Document Ready

function init_page() {
	// Page load event
	optionsDiv = $("#hidden-options");
	// Link click event to handler function
	$("#at_block").on("click", block_handler_clicked);
}

function block_handler_clicked(event) {
	if ( $(event.currentTarget).is(":checked") ) { // If Checked
		// Show options
		optionsDiv.slideDown("fast");
	} else {
		// Hide options
		optionsDiv.slideUp("fast");
	}
}
