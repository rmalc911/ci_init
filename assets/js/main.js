$(function (e) {
	$(".toggle-menu-btn").on("click", function (e) {
		if ($("html").hasClass("menu-active")) {
			$("html").removeClass("menu-active");
		} else {
			$("html").addClass("menu-active");
		}
	});

	var validator = $(".ajaxform").each(function (key, form) {
		$(this).validate({
			submitHandler: function (form) {
				var fd = new FormData(form);
				var postUrl = $(form).data("url");
				var postCallback = $(form).data("callback");
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
	});

	window.addEventListener("scroll", scrollCheck);

	setTimeout(() => {
		scrollCheck();
	}, 200);

	$(".scroll-link").on("click", function (e) {
		$("html").removeClass("menu-active");
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
