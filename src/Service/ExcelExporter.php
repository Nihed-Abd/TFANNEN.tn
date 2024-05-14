<?php
namespace App\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelExporter
{
    public function exportCommandes(array $commandes): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers to the Excel file
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Adresse');
        $sheet->setCellValue('C1', 'Num_tel');
        $sheet->setCellValue('D1', 'Prix');
        $sheet->setCellValue('E1', 'Produits');

        // Add data for each commande
        $row = 2;
        foreach ($commandes as $commande) {
            $sheet->setCellValue('A' . $row, $commande->getId());
            $sheet->setCellValue('B' . $row, $commande->getAdresse());
            $sheet->setCellValue('C' . $row, $commande->getNumTel());
            $sheet->setCellValue('D' . $row, $commande->getPrix());
            $sheet->setCellValue('E' . $row, $commande->getProduits());

            $row++;
        }

        // Save the Spreadsheet to a file
        $filename = 'all_commandes_export.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        return $filename;
    }
}