<?php

/** @var array<\dba\galleries> $gallery */
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?= $this->load->view('site/includes/common-meta', [], true) ?>
	<link rel="stylesheet" href="<?= as_base_url('css/pages.css?v=' . css_version()) ?>">
</head>

<body>
	<?= $this->load->view('site/includes/header', [], true) ?>

	<header class="header-banner">
		<div class="section-header">
			<h1 class="about-features-title">Gallery</h1>
			<p class="section-header-crumbs">
				<span>Home</span>
				<span>Gallery</span>
			</p>
		</div>
	</header>

	<main class="page-body">

		<section class="page-content-section gallery-section section-gap">
			<div class="container">
				<div class="gallery-grid">
					<?php
					foreach ($gallery as $gi => $album) {
					?>
						<a href="<?= base_url('gallery') . '/' . $album->id ?>" class="gallery-card">
							<div class="gallery-card-img">
								<img src="<?= base_url(GALLERY_UPLOAD_PATH . $album->gallery_img) ?>" alt="<?= $album->gallery_title ?>">
							</div>
							<p class="gallery-caption"><?= $album->gallery_title ?></p>
						</a>
					<?php
					}
					?>
				</div>
			</div>
		</section>
	</main>

	<?= $this->load->view('site/includes/footer', [], true) ?>

	<?= $this->load->view('site/includes/scripts', [], true) ?>

</body>

</html>
