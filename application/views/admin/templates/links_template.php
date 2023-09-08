<?php
$current_url = base_url(uri_string());
foreach ($links as $type => $link) {
	if ($type == 'other') {
		$url = base_url(ADMIN_PATH . $link['url']);
	} else {
		$url = base_url(ADMIN_PATH . $link);
	}
	$url_match = $url == $current_url;
	if ($type == 'add') {
?>
		<a href="<?= $url ?>" class="btn btn-info <?= $url_match ? '' : 'btn-border' ?> ml-2">
			<i class="fa fa-plus"></i> Add
		</a>
	<?php
	}
	if ($type == 'view') {
	?>
		<a href="<?= $url ?>" class="btn btn-info <?= $url_match ? '' : 'btn-border' ?> ml-2">
			<i class="fa fa-eye"></i> View
		</a>
	<?php
	}
	if ($type == 'sort') {
	?>
		<a href="<?= $url ?>" class="btn btn-secondary <?= $url_match ? '' : 'btn-border' ?> ml-2">
			<i class="fa fa-sort"></i> Sort
		</a>
	<?php
	}
	if ($type == 'import') {
	?>
		<a href="<?= $url ?>" class="btn btn-success <?= $url_match ? '' : 'btn-border' ?> ml-2">
			<i class="fa fa-file-excel"></i> Import
		</a>
	<?php
	}
	if ($type == 'export') {
	?>
		<a href="<?= $url ?>" class="btn btn-success <?= $url_match ? '' : 'btn-border' ?> ml-2">
			<i class="fa fa-file-excel"></i> Download
		</a>
	<?php
	}
	if ($type == 'other') {
	?>
		<a href="<?= $url ?>" class="btn btn-primary <?= $url_match ? '' : 'btn-border' ?> ml-2">
			<?= $link['label'] ?>
		</a>
<?php
	}
}
?>

<?php
if ($filter ?? '' != '') {
?>
	<button class="btn btn-primary btn-border ml-2" type="button" data-toggle="collapse" data-target="#collapse-view-filter" aria-expanded="false" aria-controls="collapse-view-filter">
		<i class="fa fa-filter"></i> Filter
	</button>
<?php
}
?>
