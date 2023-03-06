<div class="card">
	<div class="card-header">
		<div class="card-head-row">
			<h4 class="card-title">Email Config</h4>
			<div class="card-tools"></div>
		</div>
	</div>
	<form method="POST" class="validate-form">
		<div class="card-body">
			<div class="row">
				<div class="col-md-5 mb-3">
					<div class="card h-100 mb-0">
						<div class="card-body">
							<h5 class="card-title mb-2">Sendmail Mode <span class="required-label">*</span></h5>
							<div class="mb-3">
								<div class="form-check form-check-inline p-0">
									<div class="custom-control custom-radio">
										<input type="radio" id="sendmail_mode_local" name="sendmail_mode" class="custom-control-input" value="local" <?= ($config['sendmail_mode'] ?? '') == "local" ? 'checked' : '' ?> required>
										<label class="custom-control-label mb-0" for="sendmail_mode_local">Local</label>
									</div>
								</div>
								<div class="form-check form-check-inline p-0">
									<div class="custom-control custom-radio">
										<input type="radio" id="sendmail_mode_sendinblue" name="sendmail_mode" class="custom-control-input" value="sendinblue" <?= ($config['sendmail_mode'] ?? '') == "sendinblue" ? 'checked' : '' ?> required>
										<label class="custom-control-label mb-0" for="sendmail_mode_sendinblue">SendinBlue</label>
									</div>
								</div>
							</div>
							<h5 class="card-title mb-1">Admin Config</h5>
							<p class="small text-muted mb-3 h6">Send Enquiries and career application notification emails to: <br> (Use from email details if empty)</p>
							<div class="form-group p-0 mb-3">
								<label for="alert_to_email_id">To Email Address <span class="required-label">*</span></label>
								<textarea rows="4" class="form-control" id="alert_to_email_id" name="alert_to_email_id" placeholder="email1@example.com: Name1<?= PHP_EOL ?>email2@example.com: Name2" required><?= $config['alert_to_email_id'] ?? '' ?></textarea>
								<span class="text-muted d-block mt-1">Separate multiple Email IDs by new lines. <br> Add optional <code class="bg-light text-dark">:To Name</code> after each email ID.</span>
							</div>
							<div class="form-group p-0 mb-3 d-none">
								<label for="alert_to_email_name">To Email Name <span class="required-label">*</span></label>
								<input type="text" class="form-control" id="alert_to_email_name" name="alert_to_email_name" placeholder="Enter Email Name" value="<?= $config['alert_to_email_name'] ?? '' ?>" required>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-7 mb-3">
					<div class="card h-100 mb-0">
						<div class="card-body">
							<h5 class="card-title mb-2">Mail Config</h5>
							<div class="form-group p-0 mb-3">
								<label for="alert_from_email_id">From Email Address <span class="required-label">*</span></label>
								<input type="email" class="form-control" id="alert_from_email_id" name="alert_from_email_id" placeholder="Enter Email Address" value="<?= $config['alert_from_email_id'] ?? '' ?>" required>
							</div>
							<div class="form-group p-0 mb-3">
								<label for="alert_from_name">From Email Name <span class="required-label">*</span></label>
								<input type="text" class="form-control" id="alert_from_name" name="alert_from_name" placeholder="Enter Email Name" value="<?= $config['alert_from_name'] ?? '' ?>" required>
							</div>
							<div class="form-group p-0 mb-3">
								<label for="sendinblue_api_key">SendinBlue API Key <span class="required-label">*</span></label>
								<input type="text" class="form-control" id="sendinblue_api_key" name="sendinblue_api_key" placeholder="Enter SendinBlue API Key" value="<?= $config['sendinblue_api_key'] ?? '' ?>" required>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card-footer">
			<button class="btn btn-primary" type="submit">Submit</button>
			<button class="btn btn-danger" type="reset">Reset</button>
		</div>
	</form>
</div>