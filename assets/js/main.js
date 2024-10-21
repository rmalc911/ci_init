function addFormValidator(f) {
	$(f).validate({
		submitHandler: function (form) {
			var fd = new FormData(form);
			var postUrl = $(form).data("url");
			var postCallback = $(form).data("callback");
			swal.fire({
				title: "Submitting",
				text: "Please Wait",
				icon: "info",
				didOpen: () => {
					swal.showLoading();
				},
			});
			$.post({
				url: BASEURL + postUrl,
				data: fd,
				dataType: "JSON",
				processData: false,
				contentType: false,
				success: function (res) {
					if (!res.success) {
						let errorMsg = res.message;
						swal.fire("An error occurred, please check the form or try again", errorMsg, "error");
						return;
					}
					form.reset();
					if (postCallback) {
						window[postCallback]();
					}
					swal.fire(res.message, "", "success");
				},
				error: function (r) {
					console.log("error", r);
				},
			});
		},
	});
}

$(function (e) {
	$(".toggle-menu-btn").on("click", function (e) {
		if ($("html").hasClass("menu-active")) {
			$("html").removeClass("menu-active");
		} else {
			$("html").addClass("menu-active");
		}
	});

	var validator = $(".ajaxform").each(function (key, form) {
		addFormValidator(form);
	});

	window.addEventListener("scroll", scrollCheck);

	setTimeout(() => {
		scrollCheck();
	}, 200);

	$(".scroll-link").on("click", function (e) {
		$("html").removeClass("menu-active");
	});

	$("body").on("input", ".numeric", function (e) {
		var currencyType = this.hasAttribute("data-currency");
		var input = e.target.value;

		if (currencyType) {
			input = input.replace(/[^0-9.]/gi, "");
			var ex = /^[0-9]+\.?[0-9]{0,2}$/;
			if (ex.test(input) == false) {
				input = input.substring(0, input.length - 1);
			}
		} else {
			input = input.replace(/\D/g, "");
		}

		e.target.value = input;
	});

	$("body").on("input", ".alphanumeric", function (e) {
		var input = e.target.value;

		input = input.replace(/[^0-9a-z]/gi, "");

		e.target.value = input;
	});

	$("body").on("input", ".alphabetic", function (e) {
		var input = e.target.value;

		input = input.replace(/[^a-z]/gi, "");

		e.target.value = input;
	});

	$("[data-copy-text]").on("click", function () {
		let btn = $(this);
		let copyText = btn.data("copy-text");
		let btnBody = btn.html();
		let btnCopiedText = btn.data("copied-text") ?? "Copied";
		let $temp = $("<input>");
		$("body").append($temp);
		$temp.val(copyText).select();
		document.execCommand("copy");
		$temp.remove();
		btn.html(btnCopiedText);

		setTimeout(function () {
			btn.html(btnBody);
		}, 1000);
	});

	// Call the function to start lazy loading iframes
	lazyLoadIframes();
});

function scrollCheck() {
	let scrollPos = window.scrollY;
	if (scrollPos < 10) $("html").removeClass("scroll-down");
	else $("html").addClass("scroll-down");
}

// Function to lazy load iframes
function lazyLoadIframes() {
	const lazyIframes = document.querySelectorAll("[data-lazy-load]");

	const observerOptions = {
		root: null,
		rootMargin: "0px",
		threshold: 0.1,
	};

	const iframeObserver = new IntersectionObserver((entries, observer) => {
		entries.forEach((entry) => {
			if (entry.isIntersecting) {
				const iframe = entry.target;
				const iframeSrc = iframe.getAttribute("data-lazy-load");

				// Set the src attribute to load the iframe
				iframe.setAttribute("src", iframeSrc);

				// Stop observing the iframe once it's loaded
				observer.unobserve(iframe);
			}
		});
	}, observerOptions);

	// Start observing each iframe
	lazyIframes.forEach((iframe) => {
		iframeObserver.observe(iframe);
	});
}
