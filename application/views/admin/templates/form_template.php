<?php
function get_label($template_row) {
	$required = (isset($template_row['required']) && $template_row['required'] == true);
	$name = $template_row['name'];
	if (is_array($name)) {
		$name = $name[0];
	}
	$help_text = isset($template_row['help_text']) ? $template_row['help_text'] : null;
	$help_text_html = '';
	if ($help_text) {
		$help_text_html = '<br><small class="text-muted">' . $help_text . '</small>';
	}

	return '<label for="input-' . $name . '" class="col-md-3 mt-2 text-left">' . $template_row['label'] . ($required == true ? ' <span class="required-label">*</span>' : '') . $help_text_html . '</label>';
}
$col_class = ($popup_mode ?? false) ? 'col-md-9' : 'col-xl-5 col-lg-6 col-md-9';
foreach ($template as $template_row) {
	$readonly = (isset($template_row['readonly']) && $template_row['readonly'] == true) ? 'readonly' : '';
	$required = (isset($template_row['required']) && $template_row['required'] == true) ? 'required' : '';
	$class_list = (isset($template_row['class_list'])) ? $template_row['class_list'] : '';
	$value = (isset($edit[$template_row['name']])) ? $edit[$template_row['name']] : '';
	$value_ids = (isset($edit[$template_row['name']])) ? $edit[$template_row['name']] : [];
	$post_fill = false;
	$edit_block = false;
	if (isset($edit['post_fill']) && $edit['post_fill'] == true) {
		$post_fill = true;
		$edit_id = $this->input->get('edit');
	}
	if (isset($template_row['no_edit']) && $template_row['no_edit'] == true && $value != '' && !$post_fill) {
		$readonly = 'readonly';
	}
	$attributes = '';
	if ($template_row['rows'] ?? "" == "") {
		$template_row['rows'] = 3;
	}
	if (isset($template_row['attributes'])) {
		foreach ($template_row['attributes'] as $attr_key => $attr_value) {
			$attributes .= $attr_key . '="' . $attr_value . '" ';
		}
	}
	if ($template_row['type'] == 'input') {
?>
		<div class="form-group row">
			<?= get_label($template_row) ?>
			<div class="<?= $col_class ?>">
				<input type="text" class="form-control <?= $class_list ?>" id="input-<?= $template_row['name'] ?>" name="<?= $template_row['name'] ?>" placeholder="" <?= $required ?> <?= $readonly ?> value="<?= $value ?>" <?= $attributes ?>>
				<?= form_error($template_row['name']) ?>
			</div>
		</div>
	<?php
	}

	if ($template_row['type'] == 'email') {
	?>
		<div class="form-group row">
			<?= get_label($template_row) ?>
			<div class="<?= $col_class ?>">
				<input type="email" class="form-control <?= $class_list ?>" id="input-<?= $template_row['name'] ?>" name="<?= $template_row['name'] ?>" placeholder="" <?= $required ?> <?= $readonly ?> value="<?= $value ?>" <?= $attributes ?>>
				<?= form_error($template_row['name']) ?>
			</div>
		</div>
	<?php
	}

	if ($template_row['type'] == 'number') {
	?>
		<div class="form-group row">
			<?= get_label($template_row) ?>
			<div class="<?= $col_class ?>">
				<input type="number" step="any" class="form-control <?= $class_list ?>" id="input-<?= $template_row['name'] ?>" name="<?= $template_row['name'] ?>" placeholder="" <?= $required ?> <?= $readonly ?> value="<?= $value ?>" <?= $attributes ?>>
				<?= form_error($template_row['name']) ?>
			</div>
		</div>
	<?php
	}


	if ($template_row['type'] == 'time') {
	?>
		<div class="form-group row">
			<?= get_label($template_row) ?>
			<div class="<?= $col_class ?>">
				<div class="input-group">
					<input type="text" class="form-control time-widget <?= $class_list ?>" id="input-<?= $template_row['name'] ?>" name="<?= $template_row['name'] ?>" placeholder="" <?= $required ?> <?= $readonly ?> value="<?= $value ?>" <?= $attributes ?>>
					<div class="input-group-append">
						<label for="input-<?= $template_row['name'] ?>" class="input-group-text m-0">
							<i class="fa fa-clock"></i>
						</label>
					</div>
					<?= form_error($template_row['name']) ?>
				</div>
			</div>
		</div>
	<?php
	}

	if ($template_row['type'] == 'textarea') {
	?>
		<div class="form-group row">
			<?= get_label($template_row) ?>
			<div class="<?= $col_class ?>">
				<textarea name="<?= $template_row['name'] ?>" id="input-<?= $template_row['name'] ?>" class="form-control" rows="3" <?= $required ?> <?= $readonly ?> <?= $attributes ?>><?= $value ?></textarea>
				<?= form_error($template_row['name']) ?>
			</div>
		</div>
	<?php
	}

	if ($template_row['type'] == 'date-widget') {
		$min_date_attr = '';
		$max_date_attr = '';
		if (isset($template_row['add-min-date'])) {
			$min_date_attr = 'data-date-min-date="' . $template_row['add-min-date'] . '"';
		}
		if (isset($template_row['add-max-date'])) {
			$max_date_attr = 'data-date-max-date="' . $template_row['add-max-date'] . '"';
		}
	?>
		<div class="form-group row">
			<?= get_label($template_row) ?>
			<div class="<?= $col_class ?>">
				<div class="input-group">
					<input type="text" class="form-control date-widget <?= $class_list ?>" id="input-<?= $template_row['name'] ?>" name="<?= $template_row['name'] ?>" placeholder="" <?= $required ?> <?= $readonly ?> value="<?= $value != '' ? date('d-m-Y', strtotime($value)) : '' ?>" <?= $attributes ?> <?= $min_date_attr ?> <?= $max_date_attr ?>>
					<div class="input-group-append">
						<label for="input-<?= $template_row['name'] ?>" class="input-group-text m-0">
							<i class="fa fa-calendar"></i>
						</label>
					</div>
					<?= form_error($template_row['name']) ?>
				</div>
			</div>
		</div>
	<?php
	}

	if ($template_row['type'] == 'select-widget') {
		// echo json_encode($template_row, JSON_PRETTY_PRINT);
		$multiple = (isset($template_row['multiple']) && $template_row['multiple'] == true) ? 'multiple' : '';
		$update = (isset($template_row['update'])) ? $template_row['update'] : '';
		$change = (isset($template_row['change'])) ? $template_row['change'] : '';
	?>
		<div class="form-group row">
			<?= get_label($template_row) ?>
			<div class="<?= $col_class ?>">
				<div class="select2-input">
					<div class="add-option-wrapper <?= (($template_row['add_options'] ?? []) != []) ? 'input-group' : '' ?>">
						<select id="select-<?= $template_row['name'] ?>" name="<?= $template_row['name'] . ($multiple ? '[]' : '') ?>" class="form-control select-widget <?= $class_list ?>" <?= $required ?> <?= $readonly ?> <?= $multiple ?> <?= $update != '' ? ('data-update="select-' . $update . '"') : '' ?> <?= $change != '' ? ('data-change="' . $change . '"') : '' ?> <?= $attributes ?>>
							<?php
							if (isset($template_row['options'])) {
								foreach ($template_row['options'] as $option) {
									if ($multiple != '' && $option['option_value'] == '') continue;
									$selected = '';
									if ($value == $option['option_value']) {
										$selected = 'selected';
									}
									if ($multiple) {
										if (in_array($option['option_value'], $value_ids)) {
											$selected = 'selected';
										}
									}
									if (is_array($value)) {
										if (in_array($option['option_value'], $value)) {
											$selected = 'selected';
										}
									}
							?>
									<option <?= $selected ?> value="<?= $option['option_value'] ?>"><?= $option['option_name'] ?></option>
							<?php
								}
							}
							?>
						</select>
						<?php
						if ($template_row['add_options'] ?? [] != []) {
						?>
							<div class="input-group-append">
								<button class="btn btn-outline-primary btn-sm" data-add-select-option="<?= $template_row['add_options']['master'] ?>" data-options-list="select-<?= $template_row['name'] ?>" type="button"><i class="fa fa-plus"></i> <?= $template_row['add_options']['label'] ?></button>
							</div>
						<?php
						}
						?>
					</div>
				</div>
				<?= form_error($template_row['name']) ?>
			</div>
		</div>
	<?php
	}

	if ($template_row['type'] == 'image') {
		$size = null;
		$accept_type = 'image/*';
		$accept_types = '';
		$max_file_size = '4 MB';
		if (isset($template_row['size'])) {
			$size = $template_row['size'];
		}
		if (isset($template_row['accept'])) {
			$accept_types = join(' / ', $template_row['accept']);
			$accept = [];
			foreach ($template_row['accept'] as $accept_type) {
				$accept[] = 'image/' . $accept_type;
			}
			$accept_type = join(', ', $accept);
		}
		$src = ad_base_url('ajax/placeholder_img?size=') . ($size ? join('x', $size) : '150') . '&text=' . ($size ? join('x', $size) : '');
		if ($value) {
			$src = base_url($template_row['path'] . '/' . $value);
			$required = '';
		}

	?>
		<div class="form-group row">
			<?= get_label($template_row) ?>
			<div class="col-lg-8">
				<div class="input-file input-file-image">
					<div class="d-flex">
						<img class="img-upload-preview" height="150" src="<?= $src ?>" alt="preview">
						<div class="ml-2">
							<?= $size ? ('<p class="text-muted mb-0">Dimensions: ' . $size[0] . 'px &times; ' . $size[1] . 'px</p>') : '' ?>
							<?= ($accept_types != '') ? ('<p class="text-muted mb-0">Format: ' . $accept_types . '</p>') : '' ?>
							<p class="text-muted mb-0">Max size: <?= $max_file_size ?></p>
						</div>
					</div>
					<div class="form-file-label-group">
						<input type="file" class="form-control form-control-file" id="input-<?= $template_row['name'] ?>" name="<?= $template_row['name'] ?>" accept="<?= $accept_type ?>" <?= $required ?> <?= $readonly ?> <?= $attributes ?>>
						<label for="input-<?= $template_row['name'] ?>" class="label-input-file btn btn-default btn-round">
							<span class="btn-label">
								<i class="fa fa-file-image"></i>
							</span>
							Upload an Image
						</label>
					</div>
					<?= form_error($template_row['name']) ?>
				</div>
			</div>
		</div>
	<?php
	}

	if ($template_row['type'] == 'wysiwyg') {
	?>
		<div class="form-group row">
			<?= get_label($template_row) ?>
			<div class="col-lg-8">
				<textarea name="<?= $template_row['name'] ?>" id="editor-<?= $template_row['name'] ?>" class="wysiwyg-editor" <?= $attributes ?>><?= $value ?></textarea>
				<?= form_error($template_row['name']) ?>
			</div>
		</div>
	<?php
	}

	if ($template_row['type'] == 'checkbox') {
	?>
		<div class="form-group row">
			<?= get_label($template_row) ?>
			<div class="<?= $col_class ?>">
				<input type="hidden" name="<?= $template_row['name'] ?>" value="0">
				<input type="checkbox" <?= $value == '1' ? 'checked' : '' ?> name="<?= $template_row['name'] ?>" value="1" id="checkbox-<?= $template_row['name'] ?>" data-toggle="toggle" data-onstyle="success" data-on="<?= $template_row['on_state'] ?? 'Allowed' ?>" data-off="<?= $template_row['off_state'] ?? 'Blocked' ?>">
				<?= form_error($template_row['name']) ?>
			</div>
		</div>
	<?php
	}

	if ($template_row['type'] == 'radio') {
	?>
		<div class="form-group row">
			<?= get_label($template_row) ?>
			<div class="<?= $col_class ?>">
				<div class="form-check form-check-inline p-0 mr-0">
					<?php
					foreach ($template_row['options'] as $to => $option) {
					?>
						<div class="custom-control custom-radio">
							<input type="radio" id="radio-<?= $template_row['name'] ?>-<?= $to ?>" name="<?= $template_row['name'] ?>" class="custom-control-input" value="<?= $to ?>">
							<label class="custom-control-label" for="radio-<?= $template_row['name'] ?>-<?= $to ?>"><?= $option ?></label>
						</div>
					<?php
					}
					?>
				</div>
				<?= form_error($template_row['name']) ?>
			</div>
		</div>
	<?php
	}

	if ($template_row['type'] == 'file') {
		$multiple = (isset($template_row['multiple']) && $template_row['multiple'] == true) ? 'multiple' : '';
	?>
		<div class="form-group row">
			<?= get_label($template_row) ?>
			<div class="<?= $col_class ?>">
				<input type="file" class="form-control p-2 h-auto <?= $class_list ?>" id="input-<?= $template_row['name'] ?>" name="<?= $template_row['name'] . ($multiple ? '[]' : '') ?>" <?= $required ?> <?= $attributes ?>>
				<?php
				if (isset($template_row['download'])) {
					echo anchor(base_url($template_row['download']['path'] . $value), $template_row['download']['label'], ['target' => '_blank']);
				}
				?>
				<?= form_error($template_row['name']) ?>
			</div>
		</div>
	<?php
	}

	if ($template_row['type'] == 'list') {
	?>
		<div class="form-group row">
			<?= get_label($template_row) ?>
			<div class="<?= $col_class ?>">
				<div class="input-list-container">
					<div class="input-group-list" id="input-list-<?= $template_row['name'] ?>">
						<?php
						$input_row = 0;
						do {
							$field_value = $edit[$template_row['name']][$input_row] ?? "";
						?>
							<div class="input-group input-group-list-item mb-1">
								<div class="input-group-prepend">
									<span class="input-group-text"><?= $template_row['prepend_text'] ?><span class="input-list-serial"><?= $input_row + 1 ?></span></span>
								</div>
								<input type="text" class="form-control <?= $class_list ?>" name="<?= $template_row['name'] ?>[]" <?= $required ?> <?= $attributes ?> value="<?= $field_value ?>">
								<div class="input-group-append">
									<button class="btn btn-outline-danger btn-sm input-list-remove" type="button"><i class="fa fa-times"></i></button>
								</div>
							</div>
						<?php
							$input_row += 1;
						} while (isset($edit[$template_row['name']][$input_row]));
						?>
					</div>
					<button class="btn btn-info btn-sm input-list-add" type="button"><i class="fa fa-plus"></i> Add Row</button>
				</div>
				<?= form_error($template_row['name']) ?>
			</div>
		</div>
	<?php
	}

	if ($template_row['type'] == 'image-list') {
		$size = null;
		$accept_type = 'image/*';
		$accept_types = '';
		$max_file_size = '4 MB';
		if (isset($template_row['size'])) {
			$size = $template_row['size'];
		}
		if (isset($template_row['accept'])) {
			$accept_types = join(' / ', $template_row['accept']);
			$accept = [];
			foreach ($template_row['accept'] as $accept_type) {
				$accept[] = 'image/' . $accept_type;
			}
			$accept_type = join(', ', $accept);
		}
		$placeholder_img = ad_base_url('ajax/placeholder_img?size=') . ($size ? join('x', $size) : '150') . '&text=' . ($size ? join('x', $size) : '');
		$image_rows = [];
		if ($value) {
			$image_rows = explode(IMG_SPLIT, $value);
		}
	?>
		<div class="form-group row">
			<?= get_label($template_row) ?>
			<div class="col-md-9 table-responsive">
				<table class="input-list-container table table-bordered table-sm m-0">
					<thead>
						<tr>
							<th>Sl. No</th>
							<th>Preview</th>
							<th>Select</th>
							<th>Delete</th>
						</tr>
					</thead>
					<tbody class="input-group-list" id="input-list-<?= $template_row['name'] ?>">
						<?php
						$img_row_index = 0;
						do {
							$old_img = $image_rows[$img_row_index] ?? false;
							$src = $old_img ? base_url($template_row['path'] . $old_img) : $placeholder_img;
						?>
							<tr class="input-group-list-item input-file-image">
								<td class="">
									<span class=""><span class="input-list-serial"><?= $img_row_index + 1 ?></span></span>
								</td>
								<td>
									<img class="img-upload-preview" height="100" src="<?= $src ?>" alt="preview">
								</td>
								<td>
									<label class="input-file">
										<input type="file" class="form-control form-control-file" id="select-<?= $template_row['name'] ?>-<?= $img_row_index ?>" name="<?= $template_row['name'] ?>[]" accept="<?= $accept_type ?>;capture=camera" <?= $required ?> <?= $readonly ?> <?= $attributes ?>>
										<span for="select-<?= $template_row['name'] ?>-<?= $img_row_index ?>" class="label-input-file btn btn-default btn-round">
											<span class="btn-label">
												<i class="fa fa-file-image"></i>
											</span>
											Upload a Image
										</span>
									</label>
								</td>
								<td class="">
									<input type="hidden" class="reset-src" value="<?= $placeholder_img ?>">
									<input type="hidden" name="old_img[]" value="<?= $old_img ?>">
									<button class="btn btn-outline-danger btn-sm input-list-remove" type="button"><i class="fa fa-times"></i></button>
								</td>
							</tr>
						<?php
							$img_row_index += 1;
							$old_img = $image_rows[$img_row_index] ?? false;
						} while ($old_img)
						?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="4">
								<button class="btn btn-info btn-sm input-list-add" type="button"><i class="fa fa-plus"></i> Add Row</button>
							</td>
						</tr>
					</tfoot>
				</table>
				<?= form_error($template_row['name']) ?>
			</div>
		</div>
	<?php
	}

	if ($template_row['type'] == 'input-table') {
		$fields = $this->TemplateModel->{$template_row['fields']}();
		$table_inline = $template_row['table-inline'] ?? false;
		$table_col = $table_inline ? "col-md-9" : "col-md-12";
	?>
		<div class="form-group row">
			<?= get_label($template_row) ?>
			<div class="<?= $table_col ?> table-responsive">
				<table class="table table-sm table-bordered input-list-container" id="input-table-<?= $template_row['name'] ?>">
					<thead>
						<tr>
							<th width="50px">Sl. No</th>
							<?php
							foreach ($fields as $field) {
							?>
								<th><?= $field['label'] ?></th>
							<?php
							}
							?>
							<th width="50px">Remove</th>
						</tr>
					</thead>
					<tbody class="input-group-list">
						<?php
						$input_row = 0;
						do {
						?>
							<tr class="input-group-list-item">
								<td class="text-center"><span class="input-list-serial"><?= $input_row + 1 ?></span></td>
								<?php
								foreach ($fields as $field) {
									$field_value = $edit[$template_row['name']][$input_row][$field['name']] ?? "";
									$class_list = $field['class_list'] ?? "";
								?>
									<td style="position: relative;">
										<?php
										if ($field['type'] == 'select-widget') {
											$row_attributes = $field['attributes'] ?? [];
											$field_options = [];
											if (isset($field['options'])) {
												if (is_array($field['options'])) {
													$field_options = $field['options'];
												} elseif (is_string($field['options'])) {
													$row_attributes['data-ajax-options'] = $field['options'];
												}
											}
										?>
											<div class="select2-input">
												<?= form_dropdown($field['name'] . "[]", $field_options, $field_value, ['class' => 'form-control select-widget'] + $row_attributes); ?>
											</div>
										<?php
										} elseif ($field['type'] == 'checkbox') {
										?>
											<div class="custom-control custom-checkbox">
												<?= form_checkbox($field['name'] . "[]", '', false, ['class' => "custom-control-input $class_list", 'id' => "check-list-" . $field['name'] . $input_row] + ($field['attributes'] ?? [])); ?>
												<label class="custom-control-label" for="<?= "check-list-" . $field['name'] . $input_row ?>">1</label>
											</div>
										<?php
										} elseif ($field['type'] == 'radio') {
										?>
											<div class="custom-control custom-checkbox">
												<?= form_radio($field['name'], '', false, ['class' => "custom-control-input $class_list", 'id' => "check-list-" . $field['name'] . $input_row] + ($field['attributes'] ?? [])); ?>
												<label class="custom-control-label" for="<?= "check-list-" . $field['name'] . $input_row ?>">1</label>
											</div>
										<?php
										} elseif ($field['type'] == 'textarea') {
											echo form_textarea(['name' => $field['name'] . "[]"] + ($field['attributes'] ?? []), $field_value, ['class' => "form-control $class_list"]);
										} else {
											echo form_input($field['name'] . "[]", $field_value, ['class' => "form-control $class_list"] + ($field['attributes'] ?? []));
										}
										?>
									</td>
								<?php
								}
								?>
								<td class="text-center">
									<button class="btn btn-outline-danger btn-sm input-list-remove" type="button"><i class="fa fa-times"></i></button>
								</td>
							</tr>
						<?php
							$input_row += 1;
						} while (isset($edit[$template_row['name']][$input_row]));
						?>
					</tbody>
					<?php
					if (isset($template_row['footer'])) {
						echo $this->load->view(ADMIN_VIEWS_PATH . $template_row['footer'], null, true);
					}
					?>
					<tfoot>
						<tr>
							<td colspan="<?= count($fields) + 2 ?>"><button class="btn btn-info btn-sm input-list-add" type="button"><i class="fa fa-plus"></i> Add Row</button></td>
						</tr>
					</tfoot>
				</table>
				<?= form_error($template_row['name']) ?>
			</div>
		</div>
	<?php
	}

	if ($template_row['type'] == 'tags') {
	?>
		<div class="form-group row">
			<?= get_label($template_row) ?>
			<div class="<?= $col_class ?>">
				<div class="tags-control form-control form-control-sm <?= $class_list ?>">
					<input type="text" id="input-<?= $template_row['name'] ?>" name="<?= $template_row['name'] ?>" placeholder="" <?= $required ?> <?= $readonly ?> value="<?= $value ?>" <?= $attributes ?> data-role="tagsinput">
				</div>
				<?= form_error($template_row['name']) ?>
			</div>
		</div>
	<?php
	}

	if ($template_row['type'] == 'key' || $template_row['type'] == 'hidden') {
	?>
		<input type="hidden" name="<?= $template_row['name'] ?>" value="<?= $value ?>">
	<?php
	}

	if ($template_row['type'] == 'form-separator') {
	?>
		<div class="separator-solid"></div>
	<?php
	}

	if ($template_row['type'] == 'group-head') {
	?>
		<h2 class=""><?= $template_row['name'] ?></h2>
<?php
	}

	if ($template_row['type'] == 'custom') {
		echo $this->load->view(ADMIN_VIEWS_PATH . $template_row['view'], null, true);
	}
}
?>
