<div class="form-group row">
	<style>
		.preview-frame {
			position: relative;
		}

		.preview-frame::before {
			content: "";
			padding-top: 56.5%;
			display: block;
		}

		#preview-frame {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			object-fit: contain;
		}
	</style>

	<?= get_label($template_row) ?>
	<div class="col-lg-6 col-md-9 col-sm-8">
		<div class="preview-frame">
			<iframe id="preview-frame" src="" frameborder="0"></iframe>
		</div>
	</div>

	<script src="<?= base_url('assets/admin/js/yt-embed.js') ?>"></script>
	<script>
		linkEmbed('#input-video_url', '#preview-frame');
	</script>
</div>
