<div class="card">
	<div class="card-header">
		<div class="card-head-row">
			<h4 class="card-title">Payment Config</h4>
			<div class="card-tools"></div>
		</div>
	</div>
	<form method="POST" class="validate-form">
		<div class="card-body">
			<div class="row">
				<div class="col-md-5 mb-3">
					<div class="card h-100 mb-0">
						<div class="card-body">
							<h5 class="card-title mb-2">Active Payment Keys <span class="required-label">*</span></h5>
							<div class="mb-3">
								<div class="form-check form-check-inline p-0">
									<div class="custom-control custom-radio">
										<input type="radio" id="sendmail_mode_live" name="razorpay_key_state" class="custom-control-input" value="live" <?= ($config['razorpay_key_state'] ?? '') == "live" ? 'checked' : '' ?> required>
										<label class="custom-control-label mb-0" for="sendmail_mode_live">Live Keys</label>
									</div>
								</div>
								<div class="form-check form-check-inline p-0">
									<div class="custom-control custom-radio">
										<input type="radio" id="sendmail_mode_test" name="razorpay_key_state" class="custom-control-input" value="test" <?= ($config['razorpay_key_state'] ?? '') == "test" ? 'checked' : '' ?> required>
										<label class="custom-control-label mb-0" for="sendmail_mode_test">Test Keys</label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-7 mb-3">
					<div class="card h-100 mb-0">
						<div class="card-body">
							<div class="form-group p-0 mb-3">
								<label for="razp_live_key_id">Live Key ID <span class="required-label">*</span></label>
								<input type="text" class="form-control" id="razp_live_key_id" name="razp_live_key_id" placeholder="" value="<?= $config['razp_live_key_id'] ?? '' ?>" required>
							</div>
							<div class="form-group p-0 mb-0">
								<label for="razp_live_key_secret">Live Key Secret <span class="required-label">*</span></label>
								<input type="text" class="form-control" id="razp_live_key_secret" name="razp_live_key_secret" placeholder="" value="<?= $config['razp_live_key_secret'] ?? '' ?>" required>
							</div>
						</div>
						<hr class="m-0">
						<div class="card-body">
							<div class="form-group p-0 mb-3">
								<label for="razp_test_key_id">Test Key ID <span class="required-label">*</span></label>
								<input type="text" class="form-control" id="razp_test_key_id" name="razp_test_key_id" placeholder="" value="<?= $config['razp_test_key_id'] ?? '' ?>" required>
							</div>
							<div class="form-group p-0 mb-0">
								<label for="razp_test_key_secret">Test Key Secret <span class="required-label">*</span></label>
								<input type="text" class="form-control" id="razp_test_key_secret" name="razp_test_key_secret" placeholder="" value="<?= $config['razp_test_key_secret'] ?? '' ?>" required>
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
