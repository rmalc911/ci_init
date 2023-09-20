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
				var postUrl = $(form).data('url');
				var postCallback = $(form).data('callback');
				$.post({
					url: BASEURL + postUrl,
					data: fd,
					dataType: 'JSON',
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
						console.log('error', r);
					}
				});
			}
		});
	});

	window.addEventListener("scroll", scrollCheck);

	setTimeout(() => {
		scrollCheck();
	}, 200);

	$(".scroll-link").on("click", function (e) {
		$("html").removeClass("menu-active");
	});
});

function scrollCheck() {
	let scrollPos = window.scrollY;
	if (scrollPos < 10) $("html").removeClass("scroll-down");
	else $("html").addClass("scroll-down");
}
