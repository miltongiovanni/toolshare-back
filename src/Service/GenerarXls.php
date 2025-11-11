<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class GenerarXls
{
    public function crearXls($data, $titulo): void
    {
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        // Set document properties
        $spreadsheet->getProperties()->setCreator('Novaclean Services S.A.S.')
            ->setLastModifiedBy('Novaclean Services S.A.S.')
            ->setTitle($titulo);
        $i=1;
        foreach (array_keys($data[0]) as $encabezado) {
            $spreadsheet->getActiveSheet()->setCellValue([$i, 1], $encabezado);
            $i++;
        }
        $j = 2;
        foreach ($data as $row) {
            $values = array_values($row);
            for ($i = 0; $i < count($values); $i++) {
                $spreadsheet->getActiveSheet()->setCellValue([($i+1), $j], $values[$i]);
            }
            $j++;
        }

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $highestColumn = $sheet->getHighestDataColumn(); // e.g 'F'
        //dd($highestColumn);
        foreach (range('A',$highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$titulo.'.xlsx'.'"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}