<?php
$action = '';
if (isset($view_template['sort_submit'])) {
	$action = $view_template['sort_submit'];
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
	<form method="post" action="<?= $action ?>">
		<div class="row justify-content-center">
			<?php
			if ($parent_options) {
			?>
				<div class="col-md-4">
					<div class="form-group">
						<label for="filter">Filter</label>
						<div class="select2-input">
							<select id="filter" name="<?= $parent_field ?>" class="form-control select-widget">
								<?php
								foreach ($parent_options as $option) {
									$selected = '';
									if ($parent_value == $option['option_value']) {
										$selected = 'selected';
									}
								?>
									<option <?= $selected ?> value="<?= $option['option_value'] ?>"><?= $option['option_name'] ?></option>
								<?php
								}
								echo json_encode($parent_options);
								?>
							</select>
						</div>
					</div>
				</div>
			<?php
			}
			?>
			<div class="col-md-8 col-lg-7">
				<div class="kanban-board w-100 mt-4">
					<main class="kanban-drag" id="sortable">
						<?php
						foreach ($sort_list as $si => $sort_option) {
						?>
							<div class="kanban-item sort-item" data-id="<?= $sort_option['option_value'] ?>">
								<?php
								foreach ($img_fields as $img => $img_path) {
									echo img(base_url($img_path . $sort_option[$img]), false, ['width' => 150, 'class' => 'img-thumbnail mr-2 my-n2 ml-n2']);
								}
								?>
								<?= $sort_option['option_name'] ?>
							</div>
						<?php
						}
						?>
					</main>
				</div>
			</div>
		</div>

		<input type="hidden" name="sort" value="" id="sort-val">

		<div class="row justify-content-center">
			<div class="col-md-6">
				<div class="py-4 px-0">
					<button type="submit" class="btn btn-primary">Submit</button>
					<button type="reset" class="btn btn-danger">Reset</button>
				</div>
			</div>
		</div>
	</form>
</div>

<!-- Sortable-->
<script src="<?= base_url("assets/plugins/sortable/sortable.min.js") ?>"></script>

<script>
	var config = {
		appendTo: document.body,
		stop: function(event, ui) {
			var sort = [];
			$(ui.item.offsetParent())
				.find(".ui-sortable-handle")
				.each(function(i, e) {
					sort.push($(e).data("id"));
				});
			$("#sort-val").val(JSON.stringify(sort));
		},
	}
	var sortList = document.querySelector("#sortable")
	Sortable.create(sortList, {
		sort: true,
		onUpdate: function(evt) {
			var sort = [];
			$(".sort-item", sortList).each(function(i, e) {
				sort.push($(e).data("id"));
			});
			$("#sort-val").val(JSON.stringify(sort));
		},
	});

	$("#filter").on("change", function() {
		const filterName = $(this).attr("name");
		const filterValue = $(this).val();
		window.location.href = window.location.origin + window.location.pathname + "?" + filterName + "=" + filterValue;
	})
</script>
