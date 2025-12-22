<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BudgetsExport implements FromCollection, WithHeadings, WithTitle, WithMapping, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $categoryReference;

    public function __construct($data, $categoryReference = [])
    {
        $this->data = $data;
        $this->categoryReference = $categoryReference;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'expense_category_id',
            'limit',
            'period',
            'flexibility',
            'start_date',
            'end_date',
            'note'
        ];
    }

    public function map($row): array
    {
        return [
            $row['expense_category_id'] ?? '',
            $row['limit'] ?? '0.00',
            $row['period'] ?? 'monthly',
            $row['flexibility'] ?? 'soft',
            $row['start_date'] ?? now()->format('Y-m-d'),
            $row['end_date'] ?? now()->addMonth()->format('Y-m-d'),
            $row['note'] ?? '',
        ];
    }

    public function title(): string
    {
        return 'Budget Template';
    }

    public function styles(Worksheet $sheet)
    {
        // Add category reference as a second sheet if available
        if (!empty($this->categoryReference)) {
            $sheet->setTitle('Template');
            
            // Add a second sheet with category reference
            $categorySheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($sheet->getParent(), 'Category Reference');
            $sheet->getParent()->addSheet($categorySheet);
            
            // Add headers for category reference
            $categorySheet->fromArray(
                [['Category ID', 'Category Name']],
                null,
                'A1'
            );
            
            // Add category data
            $row = 2;
            foreach ($this->categoryReference as $category) {
                foreach ($category as $id => $name) {
                    $categorySheet->setCellValue('A' . $row, str_replace('ID: ', '', $id));
                    $categorySheet->setCellValue('B' . $row, $name);
                    $row++;
                }
            }
            
            // Style the category reference sheet
            $categorySheet->getStyle('A1:B1')->getFont()->setBold(true);
            $categorySheet->getColumnDimension('A')->setAutoSize(true);
            $categorySheet->getColumnDimension('B')->setAutoSize(true);
            
            // Set active sheet back to the template
            $sheet->getParent()->setActiveSheetIndex(0);
        }
        
        // Style the main template sheet
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G' . ($sheet->getHighestRow()))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);
        
        // Add data validation for period and flexibility
        $lastRow = max(2, $sheet->getHighestRow() + 1);
        
        // Data validation for period
        $periodValidation = $sheet->getCell('C2')->getDataValidation();
        $periodValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $periodValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $periodValidation->setAllowBlank(false);
        $periodValidation->setShowInputMessage(true);
        $periodValidation->setShowErrorMessage(true);
        $periodValidation->setShowDropDown(true);
        $periodValidation->setErrorTitle('Input error');
        $periodValidation->setError('Value is not in list.');
        $periodValidation->setPromptTitle('Pick from list');
        $periodValidation->setPrompt('Please pick a value from the drop-down list.');
        $periodValidation->setFormula1('"monthly,yearly"');
        
        // Data validation for flexibility
        $flexValidation = $sheet->getCell('D2')->getDataValidation();
        $flexValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $flexValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $flexValidation->setAllowBlank(false);
        $flexValidation->setShowInputMessage(true);
        $flexValidation->setShowErrorMessage(true);
        $flexValidation->setShowDropDown(true);
        $flexValidation->setErrorTitle('Input error');
        $flexValidation->setError('Value is not in list.');
        $flexValidation->setPromptTitle('Pick from list');
        $flexValidation->setPrompt('Please pick a value from the drop-down list.');
        $flexValidation->setFormula1('"strict,soft"');
        
        // Add instructions
        $sheet->setCellValue('H1', 'INSTRUCTIONS:');
        $sheet->setCellValue('H2', '1. Fill in the required fields (highlighted in yellow)');
        $sheet->setCellValue('H3', '2. expense_category_id: Use the ID from the Category Reference sheet');
        $sheet->setCellValue('H4', '3. limit: Enter the budget amount (e.g., 10000.00)');
        $sheet->setCellValue('H5', '4. period: Select from dropdown (monthly/yearly)');
        $sheet->setCellValue('H6', '5. flexibility: Select from dropdown (strict/soft)');
        $sheet->setCellValue('H7', '6. start_date/end_date: Format as YYYY-MM-DD (optional)');
        $sheet->setCellValue('H8', '7. note: Any additional notes (optional)');
        
        // Style instructions
        $sheet->getStyle('H1')->getFont()->setBold(true);
        $sheet->getStyle('H2:H8')->getFont()->setItalic(true);
        
        // Highlight required fields
        $sheet->getStyle('A2:D100')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFF99'); // Light yellow
        
        // Freeze the header row
        $sheet->freezePane('A2');
        
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
