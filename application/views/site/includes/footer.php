<footer class="page-footer">
	<div class="container">
		<div class="footer-row">
			<div class="footer-col">
				<h3 class="footer-col-title">Address</h3>
				<!-- <a href="<?= base_url() ?>" class="footer-brand">
					<img src="<?= base_url(LOGO_IMG) ?>" alt="<?= CLIENT_NAME ?>">
				</a> -->
				<div class="footer-item">
					<span class="footer-item-icon"><i class="las la-map-marked"></i></span>
					<span class="footer-item-text"><?= nl2br(CLIENT_ADDRESS) ?></span>
				</div>
				<div class="footer-item">
					<span class="footer-item-icon"><i class="las la-phone"></i></span>
					<span class="footer-item-text"><a href="tel:<?= DEFAULT_PHONE ?>"><?= DEFAULT_PHONE ?></a></span>
				</div>
				<div class="footer-item">
					<span class="footer-item-icon"><i class="las la-envelope"></i></span>
					<span class="footer-item-text"><a href="mailto:<?= DEFAULT_EMAIL_ID ?>"><?= DEFAULT_EMAIL_ID ?></a></span>
				</div>
			</div>
			<div class="footer-col">
				<h3 class="footer-col-title">Solutions</h3>
				<div class="footer-item"><span class="footer-item-text"><a href="<?= base_url() ?>">Academics</a></span></div>
				<div class="footer-item"><span class="footer-item-text"><a href="<?= base_url() ?>">Space & Defence</a></span></div>
				<div class="footer-item"><span class="footer-item-text"><a href="<?= base_url() ?>">Industry</a></span></div>
			</div>
			<div class="footer-col">
				<h3 class="footer-col-title">Services</h3>
				<div class="footer-item"><span class="footer-item-text"><a href="<?= base_url() ?>">Competency Development</a></span></div>
				<div class="footer-item"><span class="footer-item-text"><a href="<?= base_url() ?>">Pre Sales Support</a></span></div>
				<div class="footer-item"><span class="footer-item-text"><a href="<?= base_url() ?>">Post Sales Support</a></span></div>
				<div class="footer-item"><span class="footer-item-text"><a href="<?= base_url() ?>">HR Competency Development</a></span></div>
			</div>
			<div class="footer-col">
				<h3 class="footer-col-title">Quick Links</h3>
				<div class="footer-item"><span class="footer-item-text"><a href="<?= base_url() ?>">Home</a></span></div>
				<div class="footer-item"><span class="footer-item-text"><a href="<?= base_url() ?>">About Us</a></span></div>
				<!-- <div class="footer-item"><span class="footer-item-text"><a href="<?= base_url() ?>">Solutions</a></span></div> -->
				<!-- <div class="footer-item"><span class="footer-item-text"><a href="<?= base_url() ?>">Services</a></span></div> -->
				<div class="footer-item"><span class="footer-item-text"><a href="<?= base_url() ?>">Testimonials</a></span></div>
				<div class="footer-item"><span class="footer-item-text"><a href="<?= base_url() ?>">Job Corner</a></span></div>
				<div class="footer-item"><span class="footer-item-text"><a href="<?= base_url() ?>">Blog</a></span></div>
				<div class="footer-item"><span class="footer-item-text"><a href="<?= base_url() ?>">Contact Us</a></span></div>
			</div>
		</div>
		<div class="copy-footer">
			<p class="copy-footer-text">&copy; <?= date('Y') ?> <?= CLIENT_NAME ?></p>
			<ul class="footer-social-links">
				<li><a href="#"><i class="lab la-facebook-f"></i></a></li>
				<li><a href="#"><i class="lab la-twitter"></i></a></li>
				<li><a href="#"><i class="lab la-instagram"></i></a></li>
				<li><a href="#"><i class="lab la-linkedin-in"></i></a></li>
			</ul>
		</div>
	</div>
</footer>
