<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>Login</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<!-- <link rel="icon" href="../assets/img/icon.ico" type="image/x-icon"/> -->

	<!-- Fonts and icons -->
	<script src="<?= as_base_url('plugins/webfont/webfont.min.js') ?>"></script>
	<script>
		WebFont.load({
			google: {
				"families": ["Open+Sans:300,400,600,700"]
			},
			custom: {
				"families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands"],
				urls: ['<?= aa_base_url('theme/css/fonts.css') ?>']
			},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>

	<!-- CSS Files -->
	<link rel="stylesheet" href="<?= as_base_url('plugins/bootstrap/css/bootstrap.min.css') ?>">
	<link rel="stylesheet" href="<?= aa_base_url('theme/css/azzara.min.css') ?>">
</head>

<body class="login">
	<div class="wrapper wrapper-login">
		<div class="container container-login animated fadeIn">
			<img src="<?= base_url(LOGO_IMG) ?>" alt="<?= CLIENT_NAME ?>" style="width: 150px; display: block; margin: 0 auto 20px;">
			<!-- <h1 class="display-4 text-center mb-4">ADMIN</h1> -->
			<h3 class="text-center">Sign In To Admin</h3>
			<form class="login-form" action="<?= ad_base_url('login/validate') ?>" method="POST">
				<div class="form-group form-floating-label">
					<input id="username" name="username" type="text" class="form-control input-border-bottom" required>
					<label for="username" class="placeholder">Username or Mobile Number</label>
				</div>
				<div class="form-group form-floating-label">
					<input id="password" name="password" type="password" class="form-control input-border-bottom" required>
					<label for="password" class="placeholder">Password</label>
					<div class="show-password">
						<i class="flaticon-interface"></i>
					</div>
				</div>
				<?php
				if ($message != '') {
				?>
					<div class="form-group">
						<p class="text-danger"><?= $message ?></p>
					</div>
				<?php
				}
				?>
				<div class="form-action mb-3">
					<button class="btn btn-primary btn-rounded btn-login">Sign In</button>
				</div>
			</form>
		</div>
	</div>
	<script src="<?= as_base_url('plugins/jquery-3.6.0.min.js') ?>"></script>
	<script src="<?= as_base_url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
	<script src="<?= aa_base_url('theme/js/ready.js') ?>"></script>
</body>

</html>
