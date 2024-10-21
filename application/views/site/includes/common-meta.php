<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="<?= base_url(($profile_config[PROFILE_FAVICON_FIELD] ?? null) ? (PROFILE_LOGO_UPLOAD_PATH . $profile_config[PROFILE_FAVICON_FIELD]) : LOGO_IMG_MIN) ?>" type="image/x-icon">
<title><?= $profile_config['company_name'] ?></title>

<meta name="msapplication-TileColor" content="">
<meta name="theme-color" media="(prefers-color-scheme: light)" content="">
<meta name="theme-color" media="(prefers-color-scheme: dark)" content="black">

<!-- Libs -->
<link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.css" integrity="sha512-rd0qOHVMOcez6pLWPVFIv7EfSdGKLt+eafXh4RO/12Fgr41hDQxfGvoi1Vy55QIVcQEujUE1LQrATCLl2Fs+ag==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">

<!-- Icons -->
<link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css" integrity="sha512-vebUliqxrVkBy3gucMhClmyQP9On/HAWQdKDXRaAlb/FKuTbxkjPKUyqVOxAcGwFDka79eTF+YXwfke1h3/wfg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Custom -->
<link rel="stylesheet" href="<?= as_base_url('css/main.css?v=' . css_version()) ?>">

<script>
	const BASEURL = '<?= base_url() ?>';
	const ADMIN_PATH = '<?= base_url(ADMIN_PATH) ?>';
</script>

<style>
	:root {
		--font-family: "Poppins", sans-serif;
		--font-family-alt: "Poppins", sans-serif;
		--icon-font: 900 1.2em "Line Awesome Free";
	}
</style>
