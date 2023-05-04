<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>Site Under Maintenance</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="shortcut icon" href="<?= base_url(LOGO_IMG_MIN) ?>" type="image/x-icon">

	<!-- Fonts and icons -->
	<script src="<?= as_base_url('js/plugin/webfont/webfont.min.js') ?>"></script>
	<script>
		WebFont.load({
			google: {
				"families": ["Open+Sans:300,400,600,700"]
			},
			custom: {
				"families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands"],
				urls: ['<?= as_base_url('css/fonts.css') ?>']
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

<body class="page-not-found">
	<div class="wrapper not-found">
		<h1 class="animated fadeIn" style="font-size: 50px;">Site Under Maintenance</h1>
		<div class="desc animated fadeIn">Please check again after some time</div>
	</div>
	<script src="<?= as_base_url('plugins/jquery-3.6.3.min.js') ?>"></script>
	<script src="<?= as_base_url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
	<script src="<?= aa_base_url('theme/js/ready.js') ?>"></script>
</body>

</html>
