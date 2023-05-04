<div class="card">
	<div class="card-header">
		<div class="card-head-row">
			<h4 class="card-title"><?= $view_template['head'] ?></h4>
			<div class="card-tools">
				<?= $this->load->view(ADMIN_VIEWS_PATH . 'templates/links_template', ['links' => $view_template['links'], 'filter' => $view_template['filter'] ?? ''], true) ?>
			</div>
		</div>
	</div>
	<?php
	if (isset($view_template['filter'])) {
	?>
		<div class="collapse" id="collapse-view-filter">
			<div class="card-body border-bottom">
				<?= $this->load->view(ADMIN_VIEWS_PATH . 'templates/filter_template', ['table' => $table_template, 'filter_columns' => $view_template['filter'] ?? '', 'export' => $view_template['export'] ?? ''], true) ?>
			</div>
		</div>
	<?php
	}
	?>
	<?php
	if (isset($view_template['info'])) {
	?>
		<?= $view_template['info']; ?>
	<?php
	}
	?>
	<div class="card-body">
		<?php
		if (isset($table_template)) {
			$this->load->view(ADMIN_VIEWS_PATH . 'templates/table_template', ['template' => $table_template]);
		}
		?>
	</div>
</div>
