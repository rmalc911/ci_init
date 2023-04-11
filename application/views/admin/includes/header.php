<?php
$login = $this->TemplateModel->verify_admin();
$login_username = $login['user_name'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>Admin</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="icon" type="image/x-icon" href="<?= base_url(LOGO_IMG_MIN) ?>">

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
	<link rel="stylesheet" href="<?= as_base_url('plugins/sweetalert/sweetalert2.min.css') ?>">
	<link rel="stylesheet" href="<?= aa_base_url('theme/css/azzara.min.css') ?>">
	<link rel="stylesheet" href="<?= aa_base_url('css/main.css?v=') . css_version() ?>">
	<!--   Core JS Files   -->
	<script src="<?= as_base_url('plugins/jquery-3.6.3.min.js') ?>"></script>
	<script src="<?= as_base_url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

	<script>
		const BASEURL = '<?= base_url() ?>';
		const ADMIN_PATH = '<?= base_url(ADMIN_PATH) ?>';
	</script>
</head>

<body>
	<div class="wrapper">
		<!--
			Tip 1: You can change the background color of the main header using: data-background-color="blue | purple | light-blue | green | orange | red"
		-->
		<div class="main-header" data-background-color="light-blue">
			<!-- Logo Header -->
			<div class="logo-header">

				<a href="<?= ad_base_url() ?>" class="logo">
					<img src="<?= base_url(LOGO_IMG) ?>" width="175" alt="<?= CLIENT_NAME ?>" class="navbar-brand">
					<!-- <h1 class="navbar-brand text-light"><?= CLIENT_NAME ?></h1> -->
				</a>
				<button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon">
						<i class="fa fa-bars"></i>
					</span>
				</button>
				<!-- <button class="topbar-toggler more"><i class="fa fa-ellipsis-v"></i></button> -->
				<div class="navbar-minimize">
					<button class="btn btn-minimize btn-rounded">
						<i class="fa fa-bars"></i>
					</button>
				</div>
			</div>
			<!-- End Logo Header -->

			<!-- Navbar Header -->
			<nav class="navbar navbar-header navbar-expand-lg">

				<div class="container-fluid">
					<h2 class="m-0 text-light"><?= $login_username ?></h2>
					<ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
						<li class="nav-item dropdown hidden-caret">
							<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fa fa-user"></i>
							</a>
							<ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="userDropdown">
								<li>
									<a class="see-all" href="<?= ad_base_url('login/logout') ?>">Logout<i class="fa fa-sign-out-alt"></i> </a>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			</nav>
			<!-- End Navbar -->
		</div>

		<!-- Sidebar -->
		<?= $this->load->view(ADMIN_VIEWS_PATH . 'includes/navigation', [], true); ?>
		<!-- End Sidebar -->

		<div class="main-panel">
			<div class="content">
				<div class="page-inner">
					<?= isset($message) ? $message : '' ?>
