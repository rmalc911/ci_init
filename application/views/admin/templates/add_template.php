<?php
$action = '';
if (isset($view_template['form_action'])) {
	$action = $view_template['form_action'];
}
?>

<div class="card">
	<div class="card-header">
		<div class="card-head-row">
			<h4 class="card-title"><?= $view_template['head'] ?></h4>
			<div class="card-tools">
				<?= $this->load->view(ADMIN_VIEWS_PATH . 'templates/links_template', ['links' => $view_template['links']], true) ?>
			</div>
		</div>
	</div>
	<form action="<?= $action ?>" class="validate-form" method="POST" enctype="multipart/form-data">
		<div class="card-body">
			<?php
			if (isset($form_template)) {
				$this->load->view(ADMIN_VIEWS_PATH . 'templates/form_template', ['template' => $form_template]);
			}
			?>
		</div>
		<div class="card-footer">
			<input type="hidden" id="form_ajax" value="<?= json_encode($view_template['form_ajax'] ?? false) ?>">
			<button class="btn btn-primary" type="submit">Submit</button>
			<button class="btn btn-danger" type="reset">Reset</button>
		</div>
	</form>
</div>
