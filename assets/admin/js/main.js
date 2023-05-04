let dtable = null;
let select2Config = {
	placeholder: "Select",
	theme: "bootstrap",
	allowClear: true,
	templateResult: formatIcon,
	templateSelection: formatIcon,
}
function formatIcon(state) {
	let img = $(state.element).data('img-path')
	if (img) {
		return $('<span class="select2-icon"><img src="' + img + '" height="20" class="img-responsive mr-2" />' + state.text + '</span>');
	}
	return state.text;
}
var select2AjaxConfig = {
	ajax: {
		url: function (params) {
			return ADMIN_PATH + "ajax/" + $(this).data("ajax-options");
		},
		processResults: function (data, params) {
			params.page = params.page || 1;
			return {
				results: data.map((option) => {
					return {
						id: option.option_value,
						text: option.option_name,
					};
				}),
				pagination: {
					more: params.page * 30 < data.total_count,
				},
			};
		},
		delay: 250,
		cache: true,
	},
};

let validatorConfig = {
	ignore: ":hidden, [contenteditable='true']:not([name])",
	errorPlacement: function (error, element) {
		let appendAfter = element;
		let toggleBtnParent = element.parents(".btn-group-toggle");
		if (toggleBtnParent.length > 0) {
			let errorContainer = $("<div class='btn-toggle-error' />");
			errorContainer.append(error);
			error = errorContainer;
			appendAfter = toggleBtnParent;
		}
		error.insertAfter(appendAfter);
	},
};
$(function () {
	// $('.datatable-view').DataTable();
	$.validator.methods.email = function (value, element) {
		return this.optional(element) || /^.+@.+\..+$/.test(value);
	};
	if ($(".validate-form #form_ajax").val() == "true") {
		const validator = $(".validate-form").validate({
			...validatorConfig,
			submitHandler: function (form) {
				Processing.fire({
					title: "Saving",
				});
				let formData = new FormData(form);
				$.ajax({
					type: "POST",
					url: form.action,
					dataType: "json",
					data: formData,
					mimeType: "multipart/form-data",
					contentType: false,
					processData: false,
					success: function (res) {
						if (res.status) {
							Toast.fire({
								title: "Saved",
								icon: "success",
							});
							window.location.href = res.return_url;
						} else {
							validator.showErrors(res.errors);
							Toast.fire({
								title: "Form has errors",
								icon: "error",
							});
						}
					},
				});
				return false;
			},
		});
	} else {
		const validator = $(".validate-form").validate({
			...validatorConfig,
		});
	}
	$(".select-widget").select2(select2Config);
	$(".select-widget[data-ajax-options]").select2({
		...select2Config,
		...select2AjaxConfig,
	});
	$(document).on("select2:open", () => {
		document.querySelector(".select2-container--open .select2-search__field").focus();
	});
	$(".date-widget").datetimepicker({
		format: "DD-MM-YYYY",
	});
	$(".time-widget").datetimepicker({
		format: "hh:mm A",
	});
	if ($(".datepicker").length > 0) {
		$(".datepicker").datetimepicker({
			format: "DD-MM-YYYY",
			useCurrent: false,
		});
	}
	$(".wysiwyg-editor").summernote({
		toolbar: [
			["style", ["style", "bold", "italic", "underline", "clear"]],
			["font", ["strikethrough", "superscript", "subscript"]],
			["fontsize", ["fontsize"]],
			["color", ["color"]],
			["para", ["ul", "ol", "paragraph", "hr"]],
			["edit", ["fullscreen", "codeview", "undo", "redo", "help"]],
		],
		height: 250,
		callbacks: {
			onPaste: function (e) {
				var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData("text/html");
				e.preventDefault();
				var div = $("<div />");
				div.append(bufferText);
				div.find("*").removeAttr("style");
				setTimeout(function () {
					div.find("*").removeAttr("class");
					div.find("*").removeAttr("id");
					var divContent = div.html();
					document.execCommand("insertHtml", false, divContent);
				}, 30);
			},
		},
	});

	var href = window.location.origin + window.location.pathname;
	var activePage = $('a[href="' + href + '"]');
	var activeLi = activePage.parent("li");
	var activeSubnav = activePage.parents(".collapse.subnav-collapse").parent("li");
	activeLi.addClass("active");
	if (activeLi[0]) {
		activeLi[0].scrollIntoView({
			behavior: "instant",
			block: "center",
		});
		setTimeout(() => {
			activeLi[0].scrollIntoView({
				behavior: "instant",
				block: "center",
			});
		}, 200);
	}
	activePage.parents("li.nav-item").children("a").click();
	activeSubnav.children("a").click();

	$("body").on("input", ".numeric", function (e) {
		var currencyType = this.hasAttribute("data-currency");
		var input = e.target.value;

		if (currencyType) {
			input = input.replace(/[^0-9.]/gi, "");
			var ex = /^[0-9]+\.?[0-9]{0,2}$/;
			if (ex.test(input) == false) {
				input = input.substring(0, input.length - 1);
			}
		} else {
			input = input.replace(/\D/g, "");
		}

		e.target.value = input;
	});

	$("body").on("input", ".alphanumeric", function (e) {
		var input = e.target.value;

		input = input.replace(/[^0-9a-z]/gi, "");

		e.target.value = input;
	});

	$("body").on("input", ".alphabetic", function (e) {
		var input = e.target.value;

		input = input.replace(/[^a-z]/gi, "");

		e.target.value = input;
	});

	$(document).on("change", "[data-update]", function () {
		var dataUpdate = $(this).attr("data-update");
		var dataChange = $(this).attr("data-change");
		var dataChangeCb = $(this).attr("data-change-cb");
		var changeSelect = $("#" + dataUpdate);
		var currentSelectVal = $(changeSelect).val();
		$(changeSelect).parents(".select2-input").addClass("is-loading");
		var currentChangeVal = $(this).val();
		$.post({
			url: ADMIN_PATH + "ajax/" + dataChange,
			data: {
				value: currentChangeVal,
			},
			dataType: "JSON",
			success: function (res) {
				$(changeSelect).html(
					$(res)
						.map(function (index, option) {
							return '<option value="' + option.option_value + '">' + option.option_name + "</option>";
						})
						.get()
						.join("")
				);
				$(changeSelect).parents(".select2-input").addClass("is-loading");
				$(changeSelect).trigger("change.select2");
				if (dataChangeCb) {
					window[dataChangeCb](currentChangeVal, res);
				}
				$(changeSelect).parents(".select2-input").removeClass("is-loading");
			},
		});
	});

	$(document).on("click", "[data-popup-view]", function () {
		var id = $(this).data("id");
		var url = $(this).data("popup-view");
		var modalSize = $(this).data("modal-size");
		var hideBtn = $(this).data("no-btn");
		var showBtn = true;
		if (hideBtn == "1") {
			showBtn = false;
		}
		var formExtras = {};
		var popup = $(this).parents(".swal2-popup");
		if (popup) {
			$(".swal-form-input", popup).each(function (ix, ie) {
				formExtras[ie.name] = $(ie).val();
				if (ie.type == "checkbox") {
					formExtras[ie.name] = ie.checked ? "1" : "0";
				}
			});
		}
		swal.fire({
			title: "Loading...",
			html: " ",
			showConfirmButton: showBtn,
			customClass: {
				popup: modalSize,
			},
			showCloseButton: true,
			didOpen: () => {
				Swal.showLoading();
			},
		});
		$.post({
			url: ADMIN_PATH + "ajax/" + url,
			data: {
				value: id,
				extra: formExtras,
			},
			dataType: "JSON",
			success: function (res) {
				Swal.hideLoading();
				$("#swal2-title").text(res.title);
				$("#swal2-html-container").html(res.content);
				if (window.innerWidth < 760) {
					$("#swal2-html-container .collapse").removeClass("show");
					$("#swal2-html-container .collapse-title").addClass("collapsed");
				}
				if (res.reload) {
					dtable.ajax.reload();
				}
			},
		});
	});

	$(document).on("click", ".delete-record", function () {
		var dataID = $(this).attr("data-id");
		Swal.fire({
			title: "Are you sure?",
			text: "You won't be able to revert this!",
			icon: "warning",
			customClass: {
				htmlContainer: "text-center",
			},
			showCancelButton: true,
			confirmButtonColor: "#d33",
			cancelButtonColor: "#3085d6",
			confirmButtonText: "Delete!",
		}).then((result) => {
			if (result.isConfirmed) {
				$.post({
					url: ADMIN_PATH + "ajax/delete_record",
					data: {
						id: dataID,
					},
					dataType: "JSON",
					success: function (res) {
						dtable.ajax.reload();
						if (res.success) {
							Swal.fire("Deleted!", "", "success");
						} else if (res.map_view) {
							Swal.fire({
								title: "Cannot delete this item",
								html: res.map_view,
							});
						} else {
							var error_message = res.error_message;
							if (!error_message) {
								error_message = "Refresh page or try again";
							}
							Swal.fire("Could not delete!", error_message, "error");
						}
					},
					error: function (xhr, err, res) {
						Swal.fire("Could not delete!", "Refresh page or try again", "error");
					},
				});
			}
		});
	});

	$(document).on("click", ".update-status", function () {
		$(this).removeClass(disabledStatusClass, enabledStatusClass).addClass(processingStatusClass).html(processingStatusIcon);
		var dataID = $(this).data("id");
		$.post({
			url: ADMIN_PATH + "ajax/status_update_record",
			data: {
				id: dataID,
			},
			dataType: "JSON",
			success: function (res) {
				dtable.ajax.reload();
				if (!res.success) {
					var error_message = res.error_message;
					if (!error_message) {
						error_message = "Refresh page or try again";
					}
					Swal.fire("Could not update!", error_message, "error");
				}
			},
		});
	});

	var pagingType = "full_numbers";
	if (window.innerWidth < 761) {
		pagingType = "simple_numbers";
		$.fn.DataTable.ext.pager.numbers_length = 5;
	}

	var disabledStatusClass = "btn-info";
	var enabledStatusClass = "btn-success";
	var processingStatusClass = "btn-border btn-primary";
	var disabledStatusIcon = '<i class="fa fa-fw fa-ban"></i>';
	var enabledStatusIcon = '<i class="fa fa-fw fa-check"></i>';
	var processingStatusIcon = '<i class="loader loader-sm table-btn-spinner"></i>';
	dtable = $("[data-ajax-url]").DataTable({
		bProcessing: true,
		bServerSide: true,
		ordering: false,
		sAjaxSource: $(this).attr("data-ajax-url"),
		bJQueryUI: true,
		sPaginationType: pagingType,
		iDisplayLength: 10,
		oLanguage: {
			sLengthMenu: "Rows _MENU_",
			sProcessing: '<i class="loader loader-lg mx-auto"></i>',
			sLoadingRecords: "Please wait - loading...",
		},
		dom: '<"row"<"col-4 pr-1"l><"col-8 pl-0"f>><"table-responsive responsive-datatable-container"rt><"row"<"col-md-5"i><"col-md-7"p>>',
		fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
			let hideIndex = $(this[0]).data("hide-index") == "1";
			if (!hideIndex) {
				var page = this.api().page();
				var length = this.api().context[0]._iDisplayLength;
				var index = page * length + (iDisplayIndex + 1);
				$("td:eq(0)", nRow).html(index);
			}
			var statusBtn = $(".update-status", nRow);
			var currentStatus = $(statusBtn).data("status");
			if (currentStatus == "0") {
				$(statusBtn).removeClass(enabledStatusClass).addClass(disabledStatusClass).html(disabledStatusIcon);
			} else {
				$(statusBtn).removeClass(disabledStatusClass).addClass(enabledStatusClass).html(enabledStatusIcon);
			}
		},
		fnServerData: function (sSource, aoData, fnCallback) {
			var filter = null;
			if (typeof getFilter === "function") {
				filter = getFilter();
			}
			// console.log(aoData);
			var aoDataObj = {};
			aoData.forEach((aObj) => {
				aoDataObj[aObj.name] = aObj;
			});
			aoDataObj.columns.value.forEach((col, ci) => {
				aoDataObj.columns.value[ci].searchable = false;
				aoDataObj.columns.value[ci].orderable = false;
			});
			$.ajax({
				dataType: "json",
				type: "POST",
				url: $(this).attr("data-ajax-url"),
				data: {
					filter: filter,
					sEcho: "1",
					columns: aoDataObj.columns.value,
					iDisplayStart: aoDataObj.start.value,
					iDisplayLength: aoDataObj.length.value,
					search: aoDataObj.search.value,
					bRegex: "false",
					iSortCol_0: "0",
					sSortDir_0: "asc",
					iSortingCols: "1",
				},
				success: fnCallback,
			});
		},
	});

	// Input File Image

	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$(input).parents(".input-file-image").find(".img-upload-preview").attr("src", e.target.result);
			};
			reader.readAsDataURL(input.files[0]);
		}
	}

	$(document).on("change", '.input-file-image input[type="file"]', function () {
		readURL(this);
	});

	$("[data-visibility-name]").each(function () {
		var s = this;
		var vname = $(s).data("visibility-name");
		var vvalue = $(s).data("visibility-value")?.toString();
		var vvalues = vvalue.split("||");
		var vis = $("[name='" + vname + "']");
		var sgroup = $(s).parents(".form-group");
		vis.on("change", function () {
			var curVal = "";
			if ($(vis).attr("type") == "hidden" || $(vis).attr("type") == "checkbox" || $(vis).attr("type") == "radio") {
				curVal = $("[name=" + vname + "]:checked").val();
			} else {
				curVal = vis.val();
			}
			if (curVal == vvalue || vvalues.includes(curVal)) {
				$(sgroup).show();
			} else {
				// $(s).val('');
				$(s).trigger("change");
				$(sgroup).hide();
			}
		});
		$(vis).trigger("change");
	});

	$("[data-set-value]").each(function () {
		var s = this;
		var setURL = $(s).data("set-value");
		var setRefer = $(s).data("set-refer");
		var setReferInput = $("[name='" + setRefer + "']");
		$(s).parents(".form-group").addClass("is-loading");
		setReferInput.on("change", function () {
			var referVal = $(setReferInput).val();
			$.post({
				url: ADMIN_PATH + "ajax/" + setURL,
				data: {
					value: referVal,
				},
				dataType: "JSON",
				success: function (res) {
					console.log(res);
					if (res.status) {
						$(s).val(res.value);
					}
					$(s).parents(".form-group").removeClass("is-loading");
				},
			});
		});
		setReferInput.trigger("change");
	});

	$(".datatable-paginate").DataTable({
		sPaginationType: pagingType,
		bJQueryUI: true,
		ordering: false,
		iDisplayLength: 10,
		oLanguage: {
			sLengthMenu: "Rows _MENU_",
		},
		dom: '<"row"<"col-4 pr-1"l><"col-8 pl-0"f>><"table-responsive border"t><"row"<"col-md-5"i><"col-md-7"p>>',
	});

	$(document).on("click", '[data-notify="dismiss"]', function () {
		$(this).parents('[data-notify="container"]').fadeOut(250);
	});

	$(document).on("click", "[data-add-select-option]", function () {
		var selectMaster = $(this).data("add-select-option");
		var selectWidget = $(this).data("options-list");
		$.post({
			url: ADMIN_PATH + "ajax/get_add_form",
			data: {
				master: selectMaster,
			},
			dataType: "JSON",
			success: function (res) {
				if (res.success) {
					swal
						.fire({
							title: res.title,
							html: res.content,
							showCancelButton: true,
							confirmButtonText: "Add",
							customClass: {
								popup: "swal-md swal-form-popup",
							},
							preConfirm: function (e) {
								var returnVal = {};
								res.template.forEach(function (ti) {
									returnVal[ti.name] = $("#swal2-html-container [name=" + ti.name + "]").val();
								});
								return returnVal;
							},
							didOpen: function () {
								$("#swal2-html-container .select-widget").select2({
									...select2Config,
									dropdownParent: $("#swal2-html-container"),
								});
							},
						})
						.then(function (result) {
							console.log(result.isConfirmed);
							if (result.isConfirmed) {
								$.post({
									url: ADMIN_PATH + "ajax/save_add_form",
									data: {
										form: selectMaster,
										values: result.value,
									},
									dataType: "JSON",
									success: function (res) {
										if (res.success) {
											$("#" + selectWidget).html(
												res.options
													.map(function (option) {
														return "<option value=" + option.option_value + ">" + option.option_name + "</option>";
													})
													.join("")
											);
											swal.fire("Added!", "", "success");
										}
									},
								});
							}
						});
				}
			},
		});
	});

	$("body").on("click", ".input-list-add", function () {
		var inputListContainer = $(this).parents(".input-list-container");
		var inputList = $(".input-group-list", inputListContainer);
		$(".select-widget", inputListContainer).select2("destroy");
		$(".select-widget", inputListContainer).removeAttr("data-live-search").removeAttr("data-select2-id").removeAttr("aria-hidden").removeAttr("tabindex");
		var inputListItem = $(".input-group-list-item:first-child", inputList).clone();
		var resetSrc = $(".reset-src", inputListItem).val();
		$(".form-control", inputListItem).val("");
		$(".img-upload-preview", inputListItem).attr("src", resetSrc);
		inputList.append(inputListItem);
		$(".select-widget", inputListContainer).select2(select2Config);
		$(".input-group-list-item", inputListContainer).each(function (ix, ie) {
			$(".input-list-serial", ie).text(ix + 1);
			$(".input-list-serial-value", ie).val(ix);
		});
	});

	$("body").on("click", ".input-list-remove", function () {
		var inputListContainer = $(this).parents(".input-list-container");
		var inputList = $(".input-group-list", inputListContainer);
		var inputListLength = $(".input-group-list-item", inputList).length;
		if (inputListLength > 1) {
			$(this).parents(".input-group-list-item").remove();
		}
		$(".input-group-list-item", inputListContainer).each(function (ix, ie) {
			$(".input-list-serial", ie).text(ix + 1);
			$(".input-list-serial-value", ie).val(ix);
		});
	});
});

const Toast = Swal.mixin({
	toast: true,
	position: "top-end",
	showConfirmButton: false,
	showCloseButton: true,
	timer: 3000,
	timerProgressBar: true,
	didOpen: (toast) => {
		toast.addEventListener("mouseenter", Swal.stopTimer);
		toast.addEventListener("mouseleave", Swal.resumeTimer);
	},
});

const Processing = Swal.mixin({
	toast: true,
	position: "top-end",
	showConfirmButton: false,
	icon: "info",
	didOpen: (processing) => {
		Processing.showLoading();
	},
});
