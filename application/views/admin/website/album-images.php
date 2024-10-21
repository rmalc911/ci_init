<div class="row">
	<div class="col-12">
		<?php
		foreach ($errors ?? [] as $error) {
		?>
			<p class="text-danger mb-2"><?= join("<br>", $error) ?></p>
		<?php
		}
		?>
	</div>
	<?php
	foreach ($images as $i => $image) {
	?>
		<div class="col-xl-3 col-lg-4 col-md-6">
			<div class="card card-sm">
				<div class="album-img">
					<img src="<?= base_url(GALLERY_UPLOAD_PATH . $image['image_url']) ?>" alt="" class="card-img-top" id="image-<?= $image['id'] ?>">
				</div>
				<div class="card-body py-2 px-3">
					<p class="mb-0" id="gallery-caption-<?= $image['id'] ?>"><?= $image['image_caption'] ?></p>
				</div>
				<div class="card-footer text-center py-2 px-3">
					<div class="btn-group w-100">
						<button type="button" class="btn btn-sm btn-warning btn-icon edit-caption-btn" data-id="<?= $image['id'] ?>"><i class="fa fa-fw fa-pencil-alt"></i></button>
						<button type="button" class="btn btn-sm btn-danger btn-icon delete-image-btn" data-id="<?= $image['id'] ?>"><i class="fa fa-fw fa-trash"></i></button>
						<button type="button" class="btn btn-sm btn-<?= $image['image_status'] == '1' ? 'success' : 'info' ?> btn-icon image-status" data-id="<?= $image['id'] ?>,media_images,id,<?= $image['image_status'] ?>,image_status"><i class="fa fa-fw fa-<?= $image['image_status'] == '1' ? 'check' : 'ban' ?>"></i></button>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	?>
</div>
