<?php

/** @var \dba\albums $album */
$images = $album->images;
$other_albums = $album->other_albums;
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?= $this->load->view('site/includes/common-meta', [], true) ?>
	<link rel="stylesheet" href="<?= as_base_url('css/pages.css?v=' . css_version()) ?>">
	<link rel="stylesheet" href="<?= as_base_url('plugins/simple-lightbox/simple-lightbox.min.css') ?>">
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
				<div class="section-header">
					<h2 class="section-title"><?= $album->gallery_title ?></h2>
				</div>
				<div class="gallery-grid">
					<?php
					foreach ($images as $ii => $image) {
						$media_url = base_url(GALLERY_UPLOAD_PATH . $image->gallery_img);
					?>
						<a data-lightbox="<?= $album->id ?>" href="<?= $media_url ?>" class="gallery-card" title="">
							<div class="gallery-card-img">
								<img src="<?= $media_url ?>" alt="<?= $image->gallery_desc ?>" title="<?= $image->gallery_desc ?>">
							</div>
							<p class="gallery-caption"><?= $image->gallery_desc ?></p>
						</a>
					<?php
					}
					?>
				</div>
			</div>
		</section>

		<?php
		if (count($other_albums) > 0) {
		?>
			<section class="page-content-section light-bg gallery-section section-gap">
				<div class="container">
					<div class="section-header">
						<h2 class="section-title">Other Albums</h2>
						<div class="section-title-divider"></div>
					</div>
					<div class="gallery-grid">
						<?php
						foreach ($other_albums as $gi => $o_album) {
						?>
							<a href="<?= base_url('gallery') . '/' . $o_album->id ?>" class="gallery-card">
								<div class="gallery-card-img">
									<img src="<?= base_url(GALLERY_UPLOAD_PATH . $o_album->gallery_img) ?>" alt="">
								</div>
								<p class="gallery-caption"><?= $o_album->gallery_title ?></p>
							</a>
						<?php
						}
						?>
					</div>
				</div>
			</section>
		<?php
		}
		?>
	</main>

	<?= $this->load->view('site/includes/footer', [], true) ?>

	<?= $this->load->view('site/includes/scripts', [], true) ?>

	<script src="<?= as_base_url('plugins/simple-lightbox/simple-lightbox.min.js') ?>"></script>
	<script>
		var lightbox = new SimpleLightbox('[data-lightbox]', {
			animationSpeed: 150
		});
	</script>

</body>

</html>
