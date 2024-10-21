<div class="card">
	<div class="card-header">
		<div class="card-head-row">
			<h4 class="card-title">Payment Config</h4>
			<div class="card-tools"></div>
		</div>
	</div>
	<form method="POST" class="validate-form">
		<div class="card-body">
			<div class="row align-items-start">
				<div class="col-md-5 mb-3">
					<div class="card h-100 mb-0">
						<div class="card-body">
							<h5 class="card-title mb-2">Active Payment Keys <span class="required-label">*</span></h5>
							<?= form_error('payment_key_state') ?>
							<div class="mb-3">
								<div class="form-check form-check-inline p-0">
									<div class="custom-control custom-radio">
										<input type="radio" id="payment_key_state_live" name="payment_key_state" class="custom-control-input" value="live" <?= ($edit['payment_key_state'] ?? '') == "live" ? 'checked' : '' ?> required>
										<label class="custom-control-label mb-0" for="payment_key_state_live">Live Keys</label>
									</div>
								</div>
								<div class="form-check form-check-inline p-0">
									<div class="custom-control custom-radio">
										<input type="radio" id="payment_key_state_test" name="payment_key_state" class="custom-control-input" value="test" <?= ($edit['payment_key_state'] ?? '') == "test" ? 'checked' : '' ?> required>
										<label class="custom-control-label mb-0" for="payment_key_state_test">Test Keys</label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-12 mb-3">
					<div class="card mb-0">
						<div class="row mx-0">
							<div class="col-md-6 px-0 border-right">
								<div class="card-body">
									<div class="form-group p-0 mb-3">
										<label for="razp_live_key_id">Live Key ID</label>
										<input type="text" class="form-control" id="razp_live_key_id" name="razp_live_key_id" placeholder="" value="<?= $edit['razp_live_key_id'] ?? '' ?>">
										<?= form_error('razp_live_key_id') ?>
									</div>
									<div class="form-group p-0 mb-3">
										<label for="razp_live_key_secret">Live Key Secret</label>
										<input type="text" class="form-control" id="razp_live_key_secret" name="razp_live_key_secret" placeholder="" value="<?= $edit['razp_live_key_secret'] ?? '' ?>">
										<?= form_error('razp_live_key_secret') ?>
									</div>
									<div class="form-group p-0 mb-3">
										<label for="razp_live_wh_secret">Live Webhook Secret</label>
										<input type="text" class="form-control" id="razp_live_wh_secret" name="razp_live_wh_secret" placeholder="" value="<?= $edit['razp_live_wh_secret'] ?? '' ?>">
										<?= form_error('razp_live_wh_secret') ?>
									</div>
									<div class="form-group p-0 mb-3">
										<label for="razp_live_account_no">Live Payout Account No</label>
										<input type="text" class="form-control" id="razp_live_account_no" name="razp_live_account_no" placeholder="" value="<?= $edit['razp_live_account_no'] ?? '' ?>">
										<?= form_error('razp_live_account_no') ?>
									</div>
								</div>
							</div>
							<div class="col-md-6 px-0">
								<div class="card-body">
									<div class="form-group p-0 mb-3">
										<label for="razp_test_key_id">Test Key ID</label>
										<input type="text" class="form-control" id="razp_test_key_id" name="razp_test_key_id" placeholder="" value="<?= $edit['razp_test_key_id'] ?? '' ?>">
										<?= form_error('razp_test_key_id') ?>
									</div>
									<div class="form-group p-0 mb-3">
										<label for="razp_test_key_secret">Test Key Secret</label>
										<input type="text" class="form-control" id="razp_test_key_secret" name="razp_test_key_secret" placeholder="" value="<?= $edit['razp_test_key_secret'] ?? '' ?>">
										<?= form_error('razp_test_key_secret') ?>
									</div>
									<div class="form-group p-0 mb-3">
										<label for="razp_test_wh_secret">Test Webhook Secret</label>
										<input type="text" class="form-control" id="razp_test_wh_secret" name="razp_test_wh_secret" placeholder="" value="<?= $edit['razp_test_wh_secret'] ?? '' ?>">
										<?= form_error('razp_test_wh_secret') ?>
									</div>
									<div class="form-group p-0 mb-3">
										<label for="razp_test_account_no">Test Payout Account No</label>
										<input type="text" class="form-control" id="razp_test_account_no" name="razp_test_account_no" placeholder="" value="<?= $edit['razp_test_account_no'] ?? '' ?>">
										<?= form_error('razp_test_account_no') ?>
									</div>
								</div>
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
