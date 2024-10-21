<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>Login</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="icon" type="image/x-icon" href="<?= base_url(($config[PROFILE_FAVICON_FIELD] ?? null) ? (PROFILE_LOGO_UPLOAD_PATH . $config[PROFILE_FAVICON_FIELD]) : '') ?>">

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
		<img src="<?= base_url(PROFILE_LOGO_UPLOAD_PATH . ($config[PROFILE_LOGO_FIELD] ?? '')) ?>" height="45" alt="<?= $config['company_name'] ?? '' ?>" style="display: block; margin: 0 auto 20px;">
			<!-- <h1 class="display-4 text-center mb-4"><?= '' ?></h1> -->
			<h3 class="text-center">Sign In</h3>
			<form class="login-form" action="<?= ad_base_url('login/validate') ?>" method="POST">
				<div class="form-group">
					<?php
					foreach ($roles as $r => $role) {
					?>
						<div class="custom-control custom-radio custom-control-inline">
							<input class="custom-control-input" type="radio" name="role" id="<?= $role ?>" value="<?= $role ?>" required>
							<label class="custom-control-label" for="<?= $role ?>"><?= humanize($role) ?></label>
						</div>
					<?php
					}
					?>
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
					<input type="hidden" name="username" value="<?= $username ?>">
					<input type="hidden" name="password" value="<?= $password ?>">
					<button class="btn btn-primary btn-rounded btn-login">Sign In</button>
				</div>
			</form>
		</div>
	</div>
	<script src="<?= as_base_url('plugins/jquery-3.6.3.min.js') ?>"></script>
	<script src="<?= as_base_url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
	<script src="<?= aa_base_url('theme/js/ready.js') ?>"></script>
</body>

</html>
