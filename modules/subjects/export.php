<?php



if(!defined('_INCODE'))
die('Access denied...');


$data = [
    'pageTitle' => 'Export dữ liệu môn học'
];



layout('header', 'admin', $data);
layout('sidebar', 'admin', $data);
layout('breadcrumb', 'admin', $data);

// $listsubjects = getRaw("SELECT * FROM subjects");


// Tạo một đối tượng PHPExcel
$objPHPExcel = new PHPExcel();

// Tạo một workheet mới
$objPHPExcel->setActiveSheetIndex(0);
$sheet = $objPHPExcel->getActiveSheet();

// Đặt giá trị cho các ô trong bảng Excel
$sheet->setCellValue('A1', 'Tên');
$sheet->setCellValue('B1', 'Email');
$sheet->setCellValue('A2', 'John Doe');
$sheet->setCellValue('B2', 'john.doe@example.com');
$sheet->setCellValue('A3', 'Jane Smith');
$sheet->setCellValue('B3', 'jane.smith@example.com');

// Đặt tiêu đề cho cột A và B
$sheet->getStyle('A1')->getFont()->setBold(true);
$sheet->getStyle('B1')->getFont()->setBold(true);

// Tạo một tệp Excel
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); // Để lưu file Excel 2003, sử dụng 'Excel5', đối với Excel 2007 trở lên, bạn có thể sử dụng 'Excel2007'

// Đặt tiêu đề và loại file cho việc tải về
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="example.xls"');

// Ghi dữ liệu vào file Excel
$objWriter->save('php://output');

exit();