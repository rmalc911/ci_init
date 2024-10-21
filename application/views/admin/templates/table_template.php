<table id="datatables-view" class="display table table-striped table-hover table-bordered datatable-view <?= $template['src'] == 'local' ? 'datatable-paginate' : '' ?>" cellspacing="0" width="100%" <?= $template['src'] == 'ajax' ? 'data-ajax-url="' . base_url(ADMIN_PATH . $template['data']) . '"' : '' ?>>
	<thead>
		<tr>
			<?php
			foreach (array_filter($template['heads']) as $table_head) {
			?>
				<th><?= $table_head ?></th>
			<?php
			}
			?>
		</tr>
	</thead>
	<tbody>
		<?php
		if ($template['src'] == 'local') {
			foreach ($template['data'] as $t_row) {
		?>
				<tr>
					<?php
					foreach ($t_row as $t_col) {
					?>
						<td><?= $t_col ?></td>
					<?php
					}
					?>
				</tr>
		<?php
			}
		}
		?>
	</tbody>
</table>
