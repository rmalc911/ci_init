function linkEmbed(inputSelector, iframeSelector) {
	var iframe = document.querySelector(iframeSelector);
	$(inputSelector).on('blur', function () {
		inputEvent(inputSelector, iframe);
	});

	$(inputSelector).trigger('blur');
}

function inputEvent(input, iframe) {
	var link = $(input).val();
	var ytLinkPattern = /.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/;
	var patternMatch = link.match(ytLinkPattern);
	if (patternMatch && patternMatch[1] != "") {
		link = patternMatch[1];
		$(input).val(link);
	}
	$(iframe).attr('src', 'https://www.youtube-nocookie.com/embed/' + link);
}
