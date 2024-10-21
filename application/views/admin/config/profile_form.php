<div class="form-group row">
	<?= get_label(['label' => "Maps Embed Link", 'name' => "contact_maps_embed_link"]) ?>
	<div class="col">
		<input type="text" name="contact_maps_embed_link" id="contact_maps_embed_link" class="form-control" value="<?= ($config['contact_maps_embed_link'] ?? "") ?>">
		<div class="mt-2 overflow-hidden rounded">
			<iframe src="<?= ($config['contact_maps_embed_link'] ?? "") ?>" loading="lazy" allowfullscreen frameborder=0 class="w-100 d-block" height="400" id="contact_maps_embed"></iframe>
		</div>
	</div>
</div>
<script>
	$(function () {
		select2Config = {
			...select2Config,
			allowHtml: true,
			templateSelection: iformat,
			templateResult: iformat,
		}

		$("#contact_maps_embed_link").on("input", function (e) {
			let embed = document.createElement('div');
			embed.innerHTML = $("#contact_maps_embed_link").val();
			let src = "";
			if (embed.querySelector('iframe')) {
				src = embed.querySelector('iframe').getAttribute('src');
				$("#contact_maps_embed_link").val(src);
			} else {
				src = $("#contact_maps_embed_link").val();
			}
			$("#contact_maps_embed").attr("src", src);
		});
	});

	function iformat(icon) {
		var originalOption = icon.element;
		let iconHtml = '';
		if (originalOption) {
			iconHtml = '<i class="fa-brands fa-fw mr-1 fa-' + originalOption.value + '"></i>';
		}
		return $('<span>' + iconHtml + ' ' + icon.text + '</span>');
	}
</script>
