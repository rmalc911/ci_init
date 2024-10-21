<header class="top-header">
	<div class="container">
		<div class="top-header-row">
			<div class="header-contact-links">
				<p class="header-contact-item">
					<i class="las la-envelope"></i>
					<a href="mailto:<?= DEFAULT_EMAIL_ID ?>"><?= DEFAULT_EMAIL_ID ?></a>
				</p>
				<p class="header-contact-item">
					<i class="las la-phone"></i>
					<a href="tel:<?= DEFAULT_PHONE ?>"><?= DEFAULT_PHONE ?></a>
				</p>
			</div>
			<div class="header-social-links">
				<a href="#" class="header-social-item"><i class="lab la-facebook-f"></i></a>
				<a href="#" class="header-social-item"><i class="lab la-twitter"></i></a>
				<a href="#" class="header-social-item"><i class="lab la-instagram"></i></a>
				<a href="#" class="header-social-item"><i class="lab la-linkedin-in"></i></a>
			</div>
		</div>
	</div>
</header>

/** @var \db\contact_social_links[] */ $contact_social_links = $profile_config['contact_social_links'];
?>
<header class="main-header">
	<div class="container">
		<div class="main-header-row">
			<div class="header-logo">
				<a href="<?= base_url('/') ?>">
					<img src="<?= base_url(PROFILE_LOGO_UPLOAD_PATH . $profile_config[PROFILE_LOGO_FIELD]) ?>" alt="<?= $profile_config['company_name'] ?>" height="100%">
				</a>
			</div>
			<div class="header-resp-action">
				<button class="resp-menu-btn toggle-menu-btn">
					<i class="header-menu-open-icon las la-bars"></i>
					<i class="header-menu-close-icon las la-times"></i>
				</button>
			</div>
			<nav class="header-nav">
				<ul>
					<li class="<?= $nav_active == "home" ? "nav-active" : "" ?>"><a href="<?= base_url('/') ?>">Home</a></li>
					<li class="<?= $nav_active == "" ? "nav-active" : "" ?>"><a href="<?= base_url('about') ?>">About Us</a></li>
					<li class="<?= $nav_active == "" ? "nav-active" : "" ?>"><a href="<?= base_url('solutions') ?>">Solutions</a></li>
					<li class="<?= $nav_active == "" ? "nav-active" : "" ?>"><a href="<?= base_url('services') ?>">Services</a></li>
					<li class="<?= $nav_active == "" ? "nav-active" : "" ?>"><a href="<?= base_url('testimonials') ?>">Testimonials</a></li>
					<li class="<?= $nav_active == "" ? "nav-active" : "careers" ?>"><a href="<?= base_url('careers') ?>">Job Corner</a></li>
					<li class="<?= $nav_active == "" ? "nav-active" : "" ?>"><a href="<?= base_url('blog') ?>">Blog</a></li>
					<li class="<?= $nav_active == "" ? "nav-active" : "contact" ?>"><a href="<?= base_url('contact') ?>">Contact Us</a></li>
				</ul>
				<div class="header-resp-nav">
					<div class="header-nav-section">
						<div class="header-nav-item">
							<span class="header-nav-item-icon"><i class="las la-phone"></i></span>
							<span class="header-nav-item-text"><a href="tel:<?= $profile_config['company_phone'] ?>"><?= $profile_config['company_phone'] ?></a></span>
						</div>
						<div class="header-nav-item">
							<span class="header-nav-item-icon"><i class="las la-envelope"></i></span>
							<span class="header-nav-item-text"><a href="mailto:<?= $profile_config['company_email'] ?>"><?= $profile_config['company_email'] ?></a></span>
						</div>
						<ul class="header-nav-social-links">
							<?php foreach ($contact_social_links as $link) : ?>
								<li><a href="<?= $link->social_icon_url ?>"><i class="lab la-<?= $link->social_icon_class ?>"></i></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</nav>
		</div>
	</div>
</header>
