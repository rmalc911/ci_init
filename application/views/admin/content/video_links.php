<style>
	.preview-frame {
		position: relative;
		width: 320px;
		margin-inline: auto;
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

<script src="<?= base_url('assets/admin/js/yt-embed.js') ?>"></script>
<script>
	$(function() {
		let inputSelector = "[name='<?= $template_row['name'] ?>[]']";
		$(document).on("blur", inputSelector, function(e) {
			let frame = $(this).parents('.input-group-list-item').find(".preview-frame iframe");
			inputEvent(this, frame);
		});
		$(inputSelector).trigger('blur');
	});
</script>
