<style>
	.th-check-label {
		display: flex;
		align-items: center;
		margin: 0;
		cursor: pointer;
		/* font-weight: 500; */
	}

	.th-check-label input {
		margin-right: 5px;
		margin-top: 0;
	}

	.table-header {
		white-space: nowrap;
		position: sticky;
		top: 0;
		background-color: #edf0f2;
	}

	.rights-table-container {
		max-height: 500px;
		overflow: auto;
		border: 1px solid #ebedf2;
		border-left: 0;
		border-right: 0;
	}

	.rights-table-container .table tbody tr:last-child td {
		border-bottom: 0;
	}
</style>

<div id="map-user-rights">
	<div class="separator-solid"></div>
	<h2 class="">User Rights</h2>
	<div class="rights-table-container table-responsive">
		<table class="table table-sm table-bordered table-hover mb-0">
			<thead class="table-header">
				<tr>
					<th>Main</th>
					<th>Section</th>
					<th style="width: 10%;"><label class="th-check-label"><input type="checkbox" name="" id="select_view_all">View</label></th>
					<th style="width: 10%;"><label class="th-check-label"><input type="checkbox" name="" id="select_add_all">Add</label></th>
					<th style="width: 10%;"><label class="th-check-label"><input type="checkbox" name="" id="select_edit_all">Edit / Update</label></th>
					<th style="width: 10%;"><label class="th-check-label"><input type="checkbox" name="" id="select_block_all">Block</label></th>
					<th style="width: 10%;"><label class="th-check-label"><input type="checkbox" name="" id="select_delete_all">Delete</label></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$edit_access = array_column(($value ?? []), null, 'page');
				foreach ($navs as $main => $sub) {
					$rowspan = 1;
					$label = $main;
					$rowspan = count($sub);
					$sub_row = $sub[0];
					$options = $sub_row['options'];
					/** @var TemplateConfig */ $nav_config = $sub_row['config'];
					$nav_view = $this->TemplateModel->{$nav_config->view_template}(false);
					$label = $nav_view['head'];
					$name = $nav_config->access;
				?>
					<tr>
						<td class="menu-td" rowspan="<?= $rowspan ?>"><?= $main ?></td>
						<td class="menu-td">
							<?= $label ?>
						</td>
						<td>
							<?php
							if (in_array('v', $options)) {
							?>
								<input type="checkbox" class="view_checkbox" name="<?= $name . '~v' ?>" value="1" <?= (isset($edit_access[$name]) && $edit_access[$name]['view_data']) == '1' ? 'checked' : '' ?>>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if (in_array('a', $options)) {
							?>
								<input type="checkbox" class="add_checkbox" name="<?= $name . '~a' ?>" value="1" <?= (isset($edit_access[$name]) && $edit_access[$name]['add_data']) == '1' ? 'checked' : '' ?>>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if (in_array('e', $options)) {
							?>
								<input type="checkbox" class="edit_checkbox" name="<?= $name . '~e' ?>" value="1" <?= (isset($edit_access[$name]) && $edit_access[$name]['edit_data']) == '1' ? 'checked' : '' ?>>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if (in_array('b', $options)) {
							?>
								<input type="checkbox" class="block_checkbox" name="<?= $name . '~b' ?>" value="1" <?= (isset($edit_access[$name]) && $edit_access[$name]['block_data']) == '1' ? 'checked' : '' ?>>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if (in_array('d', $options)) {
							?>
								<input type="checkbox" class="delete_checkbox" name="<?= $name . '~d' ?>" value="1" <?= (isset($edit_access[$name]) && $edit_access[$name]['delete_data']) == '1' ? 'checked' : '' ?>>
							<?php
							}
							?>
						</td>
					</tr>
					<?php
					for ($si = 1; $si < $rowspan; $si++) {
						$sub_row = $sub[$si];
						$options = $sub_row['options'];
						/** @var TemplateConfig */ $nav_config = $sub_row['config'];
						$nav_view = $this->TemplateModel->{$nav_config->view_template}(false);
						$label = $nav_view['head'];
						$name = $nav_config->access;
					?>
						<tr>
							<td class="menu-td"><?= $label ?></td>
							<td>
								<?php
								if (in_array('v', $options)) {
								?>
									<input type="checkbox" class="view_checkbox" name="<?= $name . '~v' ?>" value="1" <?= (isset($edit_access[$name]) && $edit_access[$name]['view_data']) == '1' ? 'checked' : '' ?>>
								<?php
								}
								?>
							</td>
							<td>
								<?php
								if (in_array('a', $options)) {
								?>
									<input type="checkbox" class="add_checkbox" name="<?= $name . '~a' ?>" value="1" <?= (isset($edit_access[$name]) && $edit_access[$name]['add_data']) == '1' ? 'checked' : '' ?>>
								<?php
								}
								?>
							</td>
							<td>
								<?php
								if (in_array('e', $options)) {
								?>
									<input type="checkbox" class="edit_checkbox" name="<?= $name . '~e' ?>" value="1" <?= (isset($edit_access[$name]) && $edit_access[$name]['edit_data']) == '1' ? 'checked' : '' ?>>
								<?php
								}
								?>
							</td>
							<td>
								<?php
								if (in_array('b', $options)) {
								?>
									<input type="checkbox" class="block_checkbox" name="<?= $name . '~b' ?>" value="1" <?= (isset($edit_access[$name]) && $edit_access[$name]['block_data']) == '1' ? 'checked' : '' ?>>
								<?php
								}
								?>
							</td>
							<td>
								<?php
								if (in_array('d', $options)) {
								?>
									<input type="checkbox" class="delete_checkbox" name="<?= $name . '~d' ?>" value="1" <?= (isset($edit_access[$name]) && $edit_access[$name]['delete_data']) == '1' ? 'checked' : '' ?>>
								<?php
								}
								?>
							</td>
						</tr>
				<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
</div>

<script>
	$(function() {
		$("#select_add_all").click(function() {
			$(".add_checkbox").prop('checked', $(this).prop('checked'));
		});
		$("#select_view_all").click(function() {
			$(".view_checkbox").prop('checked', $(this).prop('checked'));
		});
		$("#select_edit_all").click(function() {
			$(".edit_checkbox").prop('checked', $(this).prop('checked'));
		});
		$("#select_block_all").click(function() {
			$(".block_checkbox").prop('checked', $(this).prop('checked'));
		});
		$("#select_delete_all").click(function() {
			$(".delete_checkbox").prop('checked', $(this).prop('checked'));
		});
	});
</script>
