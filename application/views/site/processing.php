<!doctype html>
<html lang="en" data-theme="light">

<head>
	<?= $this->load->view('site/includes/common-meta', true, true) ?>
</head>

<body>
	<?= $this->load->view('site/includes/header', true, true) ?>
	<div class="container">
		<h1 style="font-size: 26px; padding: 30px; text-align: center;">Processing</h1>
	</div>

	<input type="hidden" name="payment_id" value="<?= $payment_id; ?>" id="payment_id">
	<?= $this->load->view('site/includes/footer', [], true); ?>
	<?= $this->load->view('site/includes/scripts', [], true); ?>
	<script>
		$(function() {
			var payment_id = $("#payment_id").val();
			setInterval(checkPaymentStatus, 10000);

			function checkPaymentStatus() {
				$.getJSON(BASEURL + 'check_payment_status/' + payment_id, function(res) {
					if (res.status) {
						window.location.href = res.redirect;
					}
				});
			}

			checkPaymentStatus();
		});
	</script>

</body>

</html>
