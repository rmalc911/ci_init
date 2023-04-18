<!DOCTYPE html>
<html lang="en">

<head>
	<?= $this->load->view('site/includes/common-meta', [], true) ?>
</head>

<body>
	<?= $this->load->view('site/includes/header', [], true) ?>

	<main class="site-page contact-page">
		<section class="page-section header-banner">
			<div class="container">
				<div class="section-header">
					<h2 class="section-title">Contact Us</h2>
					<p class="section-header-crumbs">
						<span>Home</span>
						<span>Contact us</span>
					</p>
				</div>
			</div>
		</section>
		<section class="page-section section-gap">
			<div class="container">
				<div class="contact-header">
					<p class="section-title">Please feel free to fill out the form !</p>
				</div>
				<div class="contact-section">
					<div class="contact-links-column">
						<div class="contact-links-group">
							<div class="contact-links-icon color-yellow">
								<i class="las la-phone-volume"></i>
							</div>
							<div class="contact-links-info">
								<h4 class="contact-links-head">Phone</h4>
								<p class="contact-link"><a href="tel:<?= $config['contact_phone'] ?? DEFAULT_PHONE ?>"><?= $config['contact_phone'] ?? DEFAULT_PHONE ?></a></p>
							</div>
						</div>
						<div class="contact-links-group">
							<div class="contact-links-icon color-green">
								<i class="las la-envelope"></i>
							</div>
							<div class="contact-links-info">
								<h4 class="contact-links-head">Email</h4>
								<p class="contact-link"><a href="mailto:<?= $config['contact_email'] ?? DEFAULT_EMAIL_ID ?>"><?= $config['contact_email'] ?? DEFAULT_EMAIL_ID ?></a></p>
							</div>
						</div>
						<div class="contact-links-group">
							<div class="contact-links-icon color-orange">
								<i class="las la-map-marker-alt"></i>
							</div>
							<div class="contact-links-info">
								<h4 class="contact-links-head">Address</h4>
								<p class="contact-link"><?= $config['contact_address'] ?? CLIENT_ADDRESS ?></p>
								<p class="contact-link-extra"><a href="http://maps.google.com/?q=<?= $config['contact_maps_link'] ?? CLIENT_ADDRESS ?>" target="_blank">View Map</a></p>
							</div>
						</div>
					</div>
					<div class="contact-form-column">
						<form action="#" method="post" class="contact-form-card ajaxform" method="post" data-url="submit_contact">
							<div class="contact-form-row">
								<div class="contact-form-col">
									<div class="contact-form-group">
										<label for="" class="contact-form-label">Name*</label>
										<input type="text" class="contact-form-control" name="contact_name" placeholder="Enter your name here" required>
									</div>
								</div>
								<div class="contact-form-col">
									<div class="contact-form-group">
										<label for="" class="contact-form-label">Email*</label>
										<input type="email" class="contact-form-control" name="contact_email" placeholder="Enter your email here" required>
									</div>
								</div>
							</div>
							<div class="contact-form-row">
								<div class="contact-form-col">
									<div class="contact-form-group">
										<label for="" class="contact-form-label">Phone*</label>
										<input type="text" class="contact-form-control" name="contact_phone" placeholder="Enter your phone number here" required>
									</div>
								</div>
								<div class="contact-form-col">
									<div class="contact-form-group">
										<label for="" class="contact-form-label">Subject*</label>
										<input type="text" class="contact-form-control" name="contact_subject" placeholder="Enter subject here" required>
									</div>
								</div>
							</div>
							<div class="contact-form-row">
								<div class="contact-form-col">
									<div class="contact-form-group">
										<label for="" class="contact-form-label">Message*</label>
										<textarea class="contact-form-control" name="contact_message" placeholder="Enter your message here" required></textarea>
									</div>
								</div>
							</div>
							<div class="contact-form-row">
								<div class="contact-form-col">
									<div class="contact-form-action">
										<input type="hidden" name="submit_page" value="home">
										<button type="submit" class="contact-form-button">Submit</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</section>
	</main>

	<?= $this->load->view('site/includes/footer', [], true) ?>

	<?= $this->load->view('site/includes/scripts', [], true) ?>

</body>

</html>
