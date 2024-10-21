<style>
	.file-select {
		display: block;
		position: relative;
		border: 1px dashed #dadada;
		padding: 6rem 1rem;
		border-radius: 4px;
		text-align: center;
		background-color: #fafafa;
	}

	.file-select-icon {
		font-size: 36px;
		color: #aaa;
		margin-bottom: 15px;
	}

	.file-select-label {
		font-size: 18px;
		line-height: 1.2;
		margin-bottom: 0;
		color: #495057 !important;
	}

	.file-select-input {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		opacity: 0;
		cursor: pointer;
		z-index: 1;
	}

	.file-select.is-loading .file-select-input {
		visibility: hidden;
	}

	.file-select.is-loading::after {
		z-index: 3 !important;
	}

	.file-select.is-loading::before {
		content: "";
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-color: #fff;
		opacity: 0.8;
	}

	.file-select.is-loading>* {
		opacity: 1 !important;
	}

	.album-img {
		padding-top: 66%;
		position: relative;
	}

	.album-img img {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		object-fit: cover;
	}

	/* .file-select.is-loading {} */

	.preview-frame {
		position: relative;
	}

	.preview-frame::before {
		content: "";
		padding-top: 56.5%;
		display: block;
	}

	.preview-frame iframe {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		object-fit: contain;
	}
</style>

<div class="card">
	<div class="card-header">
		<div class="card-head-row">
			<h4 class="card-title">Album</h4>
			<div class="card-tools">
				<?= $this->load->view(ADMIN_VIEWS_PATH . 'templates/links_template', ['links' => $view_template['links']], true) ?>
			</div>
		</div>
	</div>
	<form action="" class="validate-form" method="POST" enctype="multipart/form-data">
		<div class="card-body">
			<div class="file-select">
				<div class="file-select-icon">
					<i class="fa fa-file-image"></i>
				</div>
				<p class="file-select-label">Drag and drop images or click here to select images</p>
				<input type="file" name="" id="file-select-input" multiple class="file-select-input" accept=".jpg,.png,.jpeg,.webp">
			</div>
			<div class="row">
				<div class="col">
				</div>
			</div>
			<div id="album-image-list" class="mt-3">
				<?= $this->load->view(ADMIN_VIEWS_PATH . 'website/album-images', [], true); ?>
			</div>
			<input type="hidden" id="album_id" name="album_id" value="<?= $album_id ?>">
		</div>
	</form>
</div>

<script>
	$(function() {
		$("#file-select-input").on('change', function(e) {
			var input = this;
			$(input).parents('.file-select').addClass('is-loading');
			var files = e.target.files;

			let formData = new FormData();
			if (e.target.files.length == 0) return;
			for (let i = 0; i < e.target.files.length; i++) {
				formData.append("file[]", e.target.files[i]);
			}
			formData.append('album_id', $("#album_id").val());
			$.post({
				url: ADMIN_PATH + 'ajax/upload_gallery_files',
				dataType: 'JSON',
				contentType: false,
				processData: false,
				cache: false,
				data: formData,
				success: function(res) {
					input.value = '';
					$(input).parents('.file-select').removeClass('is-loading');
					$("#album-image-list").html(res.html);
				}
			});
		});

		$('body').on('click', '.edit-caption-btn', function() {
			var image_id = $(this).data('id');
			swal.fire({
				title: 'Image Caption',
				input: 'text',
				inputValue: $("#gallery-caption-" + image_id).text(),
				inputValidator: (result) => {
					return !result && 'Please enter a caption';
				}
			}).then(function(res) {
				if (!res.isConfirmed) {
					return;
				}
				$.post({
					url: ADMIN_PATH + 'ajax/set_gallery_caption',
					dataType: 'JSON',
					data: {
						image_id: image_id,
						image_caption: res.value,
					},
					success: function(res1) {
						if (res1.success) {
							$("#gallery-caption-" + image_id).text(res.value);
						}
					}
				});
			});
		});

		$('body').on('click', '.delete-image-btn', function() {
			var image_id = $(this).data('id');
			console.log($("#image-" + image_id));
			swal.fire({
				title: 'Delete Image?',
				imageUrl: $("#image-" + image_id).attr('src'),
				imageHeight: 300,
				// icon: 'question',
				confirmButtonText: 'Delete',
				showCancelButton: true,
			}).then(function(res) {
				if (!res.isConfirmed) {
					return;
				}
				$.post({
					url: ADMIN_PATH + 'ajax/delete_gallery_image',
					dataType: 'JSON',
					data: {
						image_id: image_id,
					},
					success: function(res1) {
						if (res1.success) {
							swal.fire('Image deleted', '', 'success')
							reloadGallery()
						}
					}
				});
			});
		});

		function reloadGallery() {
			$.post({
				url: ADMIN_PATH + 'ajax/view_gallery',
				dataType: 'JSON',
				data: {
					album_id: $("#album_id").val(),
				},
				success: function(res) {
					$("#album-image-list").html(res.html);
				}
			});
		}

		var disabledStatusClass = 'btn-info';
		var enabledStatusClass = 'btn-success';
		var processingStatusClass = 'btn-border btn-primary';
		var disabledStatusIcon = '<i class="fa fa-fw fa-ban"></i>';
		var enabledStatusIcon = '<i class="fa fa-fw fa-check"></i>';
		var processingStatusIcon = '<i class="loader loader-sm table-btn-spinner m-auto"></i>';
		$(document).on('click', '.image-status', function() {
			$(this).removeClass(disabledStatusClass, enabledStatusClass).addClass(processingStatusClass).html(processingStatusIcon);
			var dataID = $(this).data('id');
			$.post({
				url: ADMIN_PATH + 'ajax/status_update_record',
				data: {
					id: dataID,
				},
				dataType: 'JSON',
				success: function(res) {
					reloadGallery();
					if (!res.success) {
						var error_message = res.error_message;
						if (!error_message) {
							error_message = 'Refresh page or try again'
						}
						Swal.fire(
							'Could not update!',
							error_message,
							'error',
						)
					}
				}
			});
		})
	});
</script>
