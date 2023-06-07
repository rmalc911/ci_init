<!DOCTYPE html>
<html lang="en">

<head>
	<?= $this->load->view('site/includes/common-meta', [], true) ?>
</head>

<body>
	<?= $this->load->view('site/includes/header', [], true) ?>

	<main class="home-page">
		<section class="home-banners">
			<div class="container">
				<div class="swiper banner-swiper" id="banner-swiper">
					<div class="swiper-wrapper">
						<div class="swiper-slide banner-slide scale-img">
							<img src="<?= base_url() ?>" alt="">
						</div>
					</div>
					<div class="swiper-nav-btn swiper-button-prev"></div>
					<div class="swiper-nav-btn swiper-button-next"></div>
				</div>
			</div>
		</section>
	</main>

	<?= $this->load->view('site/includes/footer', [], true) ?>

	<?= $this->load->view('site/includes/scripts', [], true) ?>

	<script>
	</script>
</body>

</html>
