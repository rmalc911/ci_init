<form action="<?= ad_base_url($export ?? '') ?>" method="post">
	<div class="row">
		<?php
		foreach ($filter_columns as $fi => $filter_col) {
		?>
			<?php
			if ($filter_col['type'] == 'select') {
			?>
				<div class="col-md-4">
					<div class="form-group">
						<label for="filter-<?= $filter_col['name'] ?>" class="control-label"><?= $filter_col['label'] ?></label>
						<?= form_dropdown(['name' => $filter_col['name']], array_column($filter_col['filter_options'], 'option_name', 'option_value'), [], ['id' => "filter-" . $filter_col['name'], 'class' => "form-control select-widget"]); ?>
					</div>
				</div>
			<?php
			}
			if ($filter_col['type'] == 'radio') {
			?>
				<div class="col">
					<div class="form-group">
						<label for="" class="control-label"><?= $filter_col['label'] ?></label>
						<div class="form-check form-check-inline px-0 w-100 flex-wrap">
							<?php
							foreach ($filter_col['filter_options'] as $radio_option) {
							?>
								<div class="custom-control custom-radio">
									<input type="radio" id="<?= $filter_col['name'] ?>_<?= $radio_option['option_value'] ?>" value="<?= $radio_option['option_value'] ?>" name="<?= $filter_col['name'] ?>" class="custom-control-input radio-<?= $filter_col['name'] ?>">
									<label class="custom-control-label" for="<?= $filter_col['name'] ?>_<?= $radio_option['option_value'] ?>"><?= $radio_option['option_name'] ?></label>
								</div>
							<?php
							}
							?>
						</div>
					</div>
				</div>
			<?php
			}
			if ($filter_col['type'] == 'date') {
			?>
				<div class="col-md-4">
					<div class="form-group">
						<label for="filter-<?= $filter_col['name'] ?>" class="control-label"><?= $filter_col['label'] ?></label>
						<div class="input-group">
							<input class="form-control date-widget" type="text" name="<?= $filter_col['name'] ?>" id="filter-<?= $filter_col['name'] ?>">
							<div class="input-group-append">
								<span class="input-group-text"><i class="fa fa-calendar"></i></span>
							</div>
						</div>
					</div>
				</div>
			<?php
			}
			?>
		<?php
		}
		?>
		<div class="col-auto mt-auto">
			<div class="form-group">
				<button type="reset" class="btn btn-primary" id="filter-reset">Reset</button>
				<?php
				if (isset($export) && $export != "") {
				?>
					<button type="submit" class="btn btn-success ml-2" id="filter-reset" name="export" value="export"><i class="fa fa-file-excel"></i> Export</button>
				<?php
				}
				?>
			</div>
		</div>
	</div>
</form>

<script>
	function reloadTable() {
		dtable.ajax.reload();
	}

	<?php
	foreach ($filter_columns as $fi => $filter_col) {
	?>
		<?php
		if ($filter_col['type'] == 'date') {
		?>
			$("#filter-<?= $filter_col['name'] ?>").on('dp.change', function() {
				reloadTable();
			});
		<?php
		} elseif ($filter_col['type'] == 'radio') {
		?>
			$(".radio-<?= $filter_col['name'] ?>").on('change', function() {
				reloadTable();
			});
		<?php
		} else {
		?>
			$("#filter-<?= $filter_col['name'] ?>").on('change', function() {
				reloadTable();
			});
		<?php
		}
		?>
	<?php
	}
	?>

	function getFilter() {
		var filter = {};
		<?php
		foreach ($filter_columns as $fi => $filter_col) {
			if ($filter_col['type'] == "radio") {
		?>
				filter['<?= $filter_col['name'] ?>'] = $("[name='<?= $filter_col['name'] ?>']:checked").val();
			<?php
			} else {
			?>
				filter['<?= $filter_col['name'] ?>'] = $("#filter-<?= $filter_col['name'] ?>").val();
		<?php
			}
		}
		?>
		return filter;
	}

	$("#filter-reset").on('click', function() {
		<?php
		foreach ($filter_columns as $fi => $filter_col) {
		?>
			$("#filter-<?= $filter_col['name'] ?>").val('').trigger('change.select2');
		<?php
		}
		?>
		reloadTable();
	});
</script>
