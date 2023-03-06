<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once FCPATH . "/vendor/PHPExcel/Classes/PHPExcel.php";
require_once FCPATH . "/vendor/PHPExcel/Classes/PHPExcel/IOFactory.php";

final class Excel_export {
	/**
	 * exportExcelTable
	 *
	 * @param mixed $table Table data
	 * @param mixed $table_heads Table column names as key value pairs for table data
	 * @param mixed $file_name export file name
	 * @param bool $total_row ddefault false
	 * @param bool $heading_rows ddefault false
	 * 
	 * @return void
	 */
	public function exportExcelTable($table, $table_heads, $file_name, $total_row = false, $heading_rows = false) {
		$save_file = new PHPExcel();

		$save_file->setActiveSheetIndex(0);
		$styleBold = [
			'font' => [
				'bold' => true,
			]
		];
		$styleCenter = [
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
		];

		// Excel row: 1 index, column: 0 index.
		$column = 0;
		$excel_row = 1;
		$columnStartName = $save_file->getActiveSheet()->getCellByColumnAndRow(0, $excel_row)->getColumn();
		$columnEndName = $save_file->getActiveSheet()->getCellByColumnAndRow(count($table_heads) - 1, $excel_row)->getColumn();

		if (is_array($heading_rows)) {
			foreach ($heading_rows as $head_row) {
				$cellRange = "$columnStartName$excel_row:$columnEndName$excel_row";
				$save_file->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $head_row);
				$save_file->getActiveSheet()
					->mergeCells($cellRange)
					->getStyle($cellRange)
					->applyFromArray($styleBold)
					->applyFromArray($styleCenter);
				$excel_row++;
			}
		}

		foreach ($table_heads as $field) {
			$save_file->getActiveSheet()->setCellValueByColumnAndRow($column, $excel_row, $field);
			$column++;
		}
		$cellRange = "$columnStartName$excel_row:$columnEndName$excel_row";
		$save_file->getActiveSheet()
			->getStyle($cellRange)
			->applyFromArray($styleBold);

		$excel_row++;

		foreach ($table as $row) {
			$cellRange = "$columnStartName$excel_row:$columnEndName$excel_row";
			$out_col_i = 0;
			foreach ($table_heads as $head_name => $out_col) {
				$out_col_value = $row[$head_name];
				if ($head_name == 'sl_no_auto') {
					$out_col_value = $excel_row - 1;
				}
				$save_file->getActiveSheet()->setCellValueByColumnAndRow($out_col_i, $excel_row, $out_col_value);
				$out_col_i++;
			}
			$excel_row++;
		}

		if (is_array($total_row)) {
			$column = -1;
			$save_file->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, "Total")->getStyle('1:1')->getFont()->setBold(true);
			foreach ($table_heads as $head_name => $out_col) {
				$column++;
				if (!in_array($head_name, $total_row)) continue;
				$col_total = array_sum(array_column($table, $head_name));
				$save_file->getActiveSheet()->setCellValueByColumnAndRow($column, $excel_row, $col_total);
				$save_file->getActiveSheet()->getStyleByColumnAndRow($column, $excel_row)->getFont()->setBold(true);
			}
			$save_file->getActiveSheet()->getStyleByColumnAndRow(0, $excel_row)->getFont()->setBold(true);
		}

		$sheet = $save_file->getActiveSheet();
		foreach ($sheet->getColumnIterator() as $column) {
			$sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
		}

		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="' . $file_name . '.xls"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		$object_writer = IOFactory::createWriter($save_file, 'Excel5');
		$object_writer->save('php://output');
	}

	/**
	 * importExcelFile
	 *
	 * @param string $file_path File Path or Input Name
	 * @param bool $form_upload From form file upload
	 * @param array|null $get_associative_data Convert imported data to associative using provided keys
	 * 
	 * @return array
	 */
	static function importExcelFile(string $file_path, bool $form_upload = false, ?array $get_associative_data = null) {
		$path = $file_path;
		if ($form_upload) {
			$path = $_FILES[$file_path]['tmp_name'];
		}
		$objPHPExcel = IOFactory::load($path);
		$sheet = null;
		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
			$sheet = $worksheet;
			$worksheetTitle     = $worksheet->getTitle();
			$highestRow         = $worksheet->getHighestRow(); // e.g. 10
			$highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			break;
		}
		$import_data = [];
		for ($row = 2; $row <= $highestRow; ++$row) {
			$val = array();
			for ($col = 0; $col < $highestColumnIndex; ++$col) {
				$cell = $sheet->getCellByColumnAndRow($col, $row);
				$val[] = $cell->getValue();
			}
			$import_data[] = $val;
		}
		if ($get_associative_data !== null) {
			return self::convert_excel_to_assoc($import_data, $get_associative_data);
		}
		return $import_data;
	}

	static function exportTemplate($table_heads, $file_name) {
		self::exportExcelTable([], $table_heads, "$file_name-import-template");
	}

	static function convert_excel_to_assoc($upload_data, $table_heads) {
		$table_data = [];
		if (has_string_keys($table_heads)) {
			$table_heads_keys = array_keys($table_heads);
		} else {
			$table_heads_keys = $table_heads;
		}
		foreach ($upload_data as $tri => $row) {
			foreach ($row as $tdi => $col) {
				$col_name = $table_heads_keys[$tdi];
				$table_data[$tri][$col_name] = $col;
			}
		}
		return $table_data;
	}
}

function has_string_keys(array $array) {
	return count(array_filter(array_keys($array), 'is_string')) > 0;
  }
