<?php

namespace App\Services;

use App\Models\LoanPost;
use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SpreadsheetService
{
    public function generateCirculationReport(?string $from = null, ?string $to = null): Response
    {
        $query = LoanPost::with('user')->latest();

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $posts = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Money Circulation');

        // Title row
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'Kamibantu - Money Circulation Report');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Date range subtitle
        $rangeLabel = 'All time';
        if ($from && $to) {
            $rangeLabel = "Period: {$from} to {$to}";
        } elseif ($from) {
            $rangeLabel = "From: {$from}";
        } elseif ($to) {
            $rangeLabel = "Until: {$to}";
        }

        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', $rangeLabel . ' | Generated: ' . now()->format('F j, Y H:i:s'));
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '71717A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Headers (row 4)
        $headers = [
            'A4' => 'Post ID',
            'B4' => 'Borrower',
            'C4' => 'Title',
            'D4' => 'Amount Needed (Rp)',
            'E4' => 'Interest Rate (%)',
            'F4' => 'Total Repayable (Rp)',
            'G4' => 'Repayment Period',
            'H4' => 'Status',
            'I4' => 'Posted Date',
            'J4' => 'Closed Date',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getStyle('A4:J4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Data rows
        $row = 5;
        foreach ($posts as $post) {
            $sheet->setCellValue("A{$row}", $post->id);
            $sheet->setCellValue("B{$row}", $post->user?->name ?? '-');
            $sheet->setCellValue("C{$row}", $post->title);
            $sheet->setCellValue("D{$row}", $post->amount_needed);
            $sheet->setCellValue("E{$row}", $post->interest_rate);
            $sheet->setCellValue("F{$row}", $post->total_with_interest);
            $sheet->setCellValue("G{$row}", $post->formatted_repayment);
            $sheet->setCellValue("H{$row}", ucfirst((string) $post->status));
            $sheet->setCellValue("I{$row}", $post->created_at?->format('Y-m-d H:i') ?? '-');
            $sheet->setCellValue("J{$row}", $post->closed_at?->format('Y-m-d H:i') ?? '-');
            $row++;
        }

        $lastDataRow = $row - 1;

        if ($lastDataRow >= 5) {
            $sheet->getStyle("D5:D{$lastDataRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("F5:F{$lastDataRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("E5:E{$lastDataRow}")->getNumberFormat()->setFormatCode('0.00"%"');
            $sheet->getStyle("A4:J{$lastDataRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E4E4E7']]],
            ]);
        }

        // Summary
        $summaryRow = $lastDataRow + 2;
        $sheet->setCellValue("A{$summaryRow}", 'SUMMARY');
        $sheet->getStyle("A{$summaryRow}")->getFont()->setBold(true)->setSize(12);

        $summaryRow++;
        $summaries = [
            ['Total Amount Requested', $posts->sum('amount_needed')],
            ['Total Closed Deals Value', $posts->where('status', 'closed')->sum('amount_needed')],
            ['Average Loan Size', round($posts->avg('amount_needed') ?? 0, 2)],
            ['Average Interest Rate', round($posts->avg('interest_rate') ?? 0, 2)],
            ['Total Posts', $posts->count()],
            ['Open Posts', $posts->where('status', 'open')->count()],
            ['Closed Posts', $posts->where('status', 'closed')->count()],
        ];

        foreach ($summaries as [$label, $value]) {
            $sheet->setCellValue("A{$summaryRow}", $label);
            $sheet->setCellValue("D{$summaryRow}", $value);
            $sheet->getStyle("A{$summaryRow}")->getFont()->setBold(true);
            if (is_numeric($value) && $value > 100) {
                $sheet->getStyle("D{$summaryRow}")->getNumberFormat()->setFormatCode('#,##0');
            }
            $summaryRow++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $xlsxBinary = $this->spreadsheetToXlsxBinary($spreadsheet);

        $filename = 'Kamibantu-circulation';
        if ($from) $filename .= '-from-' . $from;
        if ($to) $filename .= '-to-' . $to;
        if (!$from && !$to) $filename .= '-' . now()->format('Y-m-d');
        $filename .= '.xlsx';

        return response($xlsxBinary, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Content-Length' => strlen($xlsxBinary),
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function spreadsheetToXlsxBinary(Spreadsheet $spreadsheet): string
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheetName = $this->sanitizeSheetName($sheet->getTitle());
        $sheetXml = $this->worksheetToXml($sheet);

        $contentTypesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '</Types>';

        $relsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';

        $escapedSheetName = htmlspecialchars($sheetName, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $workbookXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . $escapedSheetName . '" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';

        $workbookRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '</Relationships>';

        return $this->createStoredZip([
            '[Content_Types].xml' => $contentTypesXml,
            '_rels/.rels' => $relsXml,
            'xl/workbook.xml' => $workbookXml,
            'xl/_rels/workbook.xml.rels' => $workbookRelsXml,
            'xl/worksheets/sheet1.xml' => $sheetXml,
        ]);
    }

    private function worksheetToXml(Worksheet $sheet): string
    {
        $highestRow = $sheet->getHighestRow();
        $highestCol = $sheet->getHighestColumn();
        $rowsXml = '';

        for ($r = 1; $r <= $highestRow; $r++) {
            $cellsXml = '';
            $col = 'A';

            while (true) {
                $coord = $col . $r;
                if ($sheet->cellExists($coord)) {
                    $cell = $sheet->getCell($coord);
                    $val = $cell->getValue();
                    $dataType = $cell->getDataType();

                    if ($val !== null && $val !== '') {
                        if ($dataType === DataType::TYPE_NUMERIC && is_numeric($val)) {
                            $cellsXml .= '<c r="' . $coord . '"><v>' . (0 + $val) . '</v></c>';
                        } else {
                            $escaped = htmlspecialchars((string) $val, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                            $cellsXml .= '<c r="' . $coord . '" t="inlineStr"><is><t xml:space="preserve">' . $escaped . '</t></is></c>';
                        }
                    }
                }

                if ($col === $highestCol) {
                    break;
                }
                $col++;
            }

            if ($cellsXml !== '') {
                $rowsXml .= '<row r="' . $r . '">' . $cellsXml . '</row>';
            }
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetData>' . $rowsXml . '</sheetData>'
            . '</worksheet>';
    }

    private function sanitizeSheetName(string $name): string
    {
        $clean = preg_replace('/[\\\\\/\?\*\[\]:]/', '', $name) ?? 'Sheet1';
        $clean = trim($clean);
        return $clean !== '' ? mb_substr($clean, 0, 31) : 'Sheet1';
    }

    private function createStoredZip(array $files): string
    {
        $localParts = '';
        $centralParts = '';
        $offset = 0;

        foreach ($files as $name => $data) {
            $nameBytes = (string) $name;
            $dataBytes = (string) $data;
            $size = strlen($dataBytes);
            $crc32 = hexdec(hash('crc32b', $dataBytes));

            $localHeader = pack(
                'VvvvvvVVVvv',
                0x04034b50,
                20,
                0x0800,
                0,
                0,
                0,
                $crc32,
                $size,
                $size,
                strlen($nameBytes),
                0
            );

            $localParts .= $localHeader . $nameBytes . $dataBytes;

            $centralHeader = pack(
                'VvvvvvvVVVvvvvvVV',
                0x02014b50,
                20,
                20,
                0x0800,
                0,
                0,
                0,
                $crc32,
                $size,
                $size,
                strlen($nameBytes),
                0,
                0,
                0,
                0,
                0,
                $offset
            );

            $centralParts .= $centralHeader . $nameBytes;
            $offset += strlen($localHeader) + strlen($nameBytes) + $size;
        }

        $centralSize = strlen($centralParts);
        $eocd = pack(
            'VvvvvVVv',
            0x06054b50,
            0,
            0,
            count($files),
            count($files),
            $centralSize,
            $offset,
            0
        );

        return $localParts . $centralParts . $eocd;
    }
}