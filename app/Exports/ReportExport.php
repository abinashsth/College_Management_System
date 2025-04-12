<?php

namespace App\Exports;

use App\Models\Reporting\GeneratedReport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $report;
    protected $data;

    public function __construct(GeneratedReport $report, array $data)
    {
        $this->report = $report;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        // Transform report data into exportable rows
        return $this->transformData();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Extract headings from report data
        if (isset($this->data['headers'])) {
            return $this->data['headers'];
        }

        // Generate headers based on data structure
        if (isset($this->data['rows']) && !empty($this->data['rows'])) {
            $firstRow = $this->data['rows'][0];
            if (is_array($firstRow)) {
                return array_keys($firstRow);
            }
        }

        // Default headers if none can be determined
        return ['No Data Available'];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->report->title;
    }

    /**
     * Apply styles to the spreadsheet
     *
     * @param Worksheet $sheet
     * @return void
     */
    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:' . $this->getLastColumn(count($this->headings())) . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
        ]);

        // Add a border to all cells with data
        $lastRow = count($this->array()) + 1; // +1 for header row
        $sheet->getStyle('A1:' . $this->getLastColumn(count($this->headings())) . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD'],
                ],
            ],
        ]);

        // Zebra striping for readability
        for ($i = 2; $i <= $lastRow; $i += 2) {
            $sheet->getStyle('A' . $i . ':' . $this->getLastColumn(count($this->headings())) . $i)->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F2F2F2'],
                ],
            ]);
        }
    }

    /**
     * Transform report data into exportable rows
     *
     * @return array
     */
    private function transformData(): array
    {
        // If there's a rows key, use it directly
        if (isset($this->data['rows'])) {
            return $this->data['rows'];
        }

        // For comparison reports, transform the comparison data
        if (isset($this->data['comparison_data'])) {
            return $this->transformComparisonData();
        }

        // Transform other data types based on the report type
        switch ($this->report->report_type) {
            case 'academic':
            case 'financial':
            case 'attendance':
            case 'examination':
            case 'staff':
            case 'student':
            case 'admission':
                return $this->transformStandardData();
            case 'custom':
                return $this->transformCustomData();
            default:
                return [['No Data Available']];
        }
    }

    /**
     * Transform comparison data into rows
     *
     * @return array
     */
    private function transformComparisonData(): array
    {
        $rows = [];
        $comparisonData = $this->data['comparison_data'] ?? [];

        foreach ($comparisonData as $key => $item) {
            $rows[] = [
                'Metric' => $key,
                'Current Period' => $item['current'] ?? null,
                'Previous Period' => $item['previous'] ?? null,
                'Change' => $item['change'] ?? null,
                'Change %' => $item['change_percentage'] ?? null,
            ];
        }

        return $rows;
    }

    /**
     * Transform standard report data into rows
     *
     * @return array
     */
    private function transformStandardData(): array
    {
        // Each report type would have a specific transformation
        // For simplicity, we'll return a placeholder
        return [['No standard data transformation implemented']];
    }

    /**
     * Transform custom report data into rows
     *
     * @return array
     */
    private function transformCustomData(): array
    {
        // Custom reports might have specific formats
        return [['No custom data transformation implemented']];
    }

    /**
     * Get the column letter for the last column
     *
     * @param int $columnCount
     * @return string
     */
    private function getLastColumn(int $columnCount): string
    {
        $columnLetters = range('A', 'Z');
        
        if ($columnCount <= 26) {
            return $columnLetters[$columnCount - 1];
        }
        
        // For more than 26 columns (AA, AB, etc.)
        $firstLetter = $columnLetters[floor($columnCount / 26) - 1];
        $secondLetter = $columnLetters[$columnCount % 26 - 1];
        
        return $firstLetter . $secondLetter;
    }
} 