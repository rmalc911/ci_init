<!DOCTYPE html>
<html lang="en">

<head>
	<?= $this->load->view('site/includes/common-meta', true, true) ?>
</head>

<body>
	<?= $this->load->view('site/includes/header', ['active_menu' => 'careers'], true) ?>

	<main class="site-page career-page">
		<section class="page-section header-banner">
			<div class="container">
				<div class="section-header">
					<h2 class="section-title">Careers</h1>
					<p class="section-header-crumbs">
						<span>Home</span>
						<span>Careers</span>
					</p>
				</div>
			</div>
		</section>
		<section class="page-section section-gap">
			<div class="container">
				<div class="career-listings">
					<?php
					foreach ($careers as $ci => $career) {
					?>
						<div class="career-option" id="career-option-<?= $career['id'] ?>">
							<h2 class="career-option-title"><?= $career['career_name'] ?></h2>
							<p class="career-option-desc"><?= $career['career_desc_preview'] ?></p>
							<div class="career-option-content" style="display: none;"><?= $career['career_desc'] ?></div>
							<div class="career-option-actions">
								<button class="contact-form-btn career-select-btn" type="button" data-career-option="<?= $career['id'] ?>"><span>Apply Now</span></button>
							</div>
						</div>
					<?php
					}
					?>
				</div>
			</div>
		</section>
	</main>

	<div class="career-modal">
		<div class="career-modal-bg career-dismiss-modal"></div>
		<div class="career-modal-container">
			<div class="career-modal-header">
				<h1 class="career-modal-heading">Careers at <span class="hlt"><?= CLIENT_NAME ?></span></h1>
				<button class="career-modal-btn career-dismiss-modal">×</button>
			</div>
			<div class="career-modal-body">
				<div class="career-modal-info" data-simplebar data-simplebar-auto-hide="false">
					<h2 class="career-modal-title"></h2>
					<div class="career-modal-description"></div>
				</div>
				<div class="career-modal-form">
					<form action="" class="contact-form ajaxform" method="POST" enctype="multipart/form-data" id="career-form" data-url="apply_career" data-callback="form_callback">
						<div class="contact-form-row">
							<div class="contact-form-col">
								<div class="contact-form-group">
									<label for="form-applicant-fname" class="contact-form-label">First Name</label>
									<input type="text" name="applicant-fname" id="form-applicant-fname" class="contact-form-input" required="">
								</div>
							</div>
							<div class="contact-form-col">
								<div class="contact-form-group">
									<label for="form-applicant-lname" class="contact-form-label">Last Name</label>
									<input type="text" name="applicant-lname" id="form-applicant-lname" class="contact-form-input" required="">
								</div>
							</div>
						</div>
						<div class="contact-form-row">
							<div class="contact-form-col">
								<div class="contact-form-group">
									<label for="form-applicant-email" class="contact-form-label">Email</label>
									<input type="text" name="applicant-email" id="form-applicant-email" class="contact-form-input" required="">
								</div>
							</div>
							<div class="contact-form-col">
								<div class="contact-form-group">
									<label for="form-applicant-phone" class="contact-form-label">Phone</label>
									<input type="text" name="applicant-phone" id="form-applicant-phone" class="contact-form-input numeric" required="" maxlength="10" minlength="10">
								</div>
							</div>
						</div>
						<div class="contact-form-row">
							<div class="contact-form-col contact-form-wide-col">
								<div class="contact-form-group">
									<label for="form-applicant-resume" class="contact-form-label">Resume (PDF/DOC/DOCX, < 4MB)</label>
									<input type="file" name="applicant-resume" id="form-applicant-resume" class="contact-form-input contact-form-file" required="" accept="application/msword, application/pdf, application/vnd.openxmlformats-officedocument.wordprocessingml.document">
								</div>
							</div>
						</div>
						<div class="contact-form-row">
							<div class="contact-form-col contact-form-wide-col">
								<div class="contact-form-group">
									<label for="formapplicant-about" class="contact-form-label">Message</label>
									<textarea rows="2" name="applicant-about" id="form-applicant-about" class="contact-form-input" maxlength="1000"></textarea>
								</div>
							</div>
						</div>
						<div class="career-form-actions">
							<input type="hidden" name="career-option" id="form-career-option" value="1">
							<button class="contact-form-btn" type="submit"><span>Apply</span></button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<?= $this->load->view('site/includes/footer', true, true) ?>

	<?= $this->load->view('site/includes/scripts', true, true) ?>

	<link rel="stylesheet" href="<?= as_base_url('plugins/simplebar/simplebar.css') ?>">
	<script src="<?= as_base_url('plugins/simplebar/simplebar.min.js') ?>"></script>

	<script>
		function form_callback() {
			$(".career-dismiss-modal").click();
		}

		$(function() {
			$(".career-select-btn").on('click', function() {
				// validator.resetForm();
				$("#career-form")[0].reset();

				var careerOption = $(this).data('career-option');
				var careerTitle = $("#career-option-" + careerOption + " .career-option-title").text();
				var careerDesc = $("#career-option-" + careerOption + " .career-option-content").html();

				$(".career-modal-title").text(careerTitle);
				$(".career-modal-description").html(careerDesc);
				$("#form-career-option").val(careerOption);
				$('html').addClass('career-modal-active');
				$('html').addClass('scroll-fix');
				setTimeout(() => {
					$('html').addClass('career-modal-visible');
				}, 20);
			});

			$(".career-dismiss-modal").on('click', function() {
				$('html').removeClass('career-modal-visible');
				setTimeout(() => {
					$('html').removeClass('scroll-fix');
					$('html').removeClass('career-modal-active');
				}, 250);
			});

			const urlParams = new URLSearchParams(window.location.search);
			const option = urlParams.get('option');
			if (option != '') {
				$("[data-career-option='" + option + "']").trigger('click');
			}
		});
	</script>

</body>

</html>
