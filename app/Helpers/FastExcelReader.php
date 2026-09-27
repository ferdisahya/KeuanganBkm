<?php

namespace App\Helpers;

use ZipArchive;
use XMLReader;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FastExcelReader
{
    /**
     * Stream rows from an Excel file (.xlsx, .csv, .xls) row by row with minimal RAM usage.
     *
     * @param string $filePath Absolute path to the excel file
     * @param callable $callback Callback receives (int $rowNum, array $cellsMap) where $cellsMap is ['A' => val, 'B' => val, ...]
     * @return int Total rows processed
     */
    public static function readRows(string $filePath, callable $callback): int
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($ext === 'csv') {
            return self::readCsv($filePath, $callback);
        }

        if ($ext === 'xlsx') {
            try {
                return self::readXlsxStream($filePath, $callback);
            } catch (\Throwable $e) {
                // Fallback to PhpSpreadsheet if XML parsing encounters unexpected structure
                return self::readPhpSpreadsheetChunked($filePath, $callback);
            }
        }

        // For .xls binary files, use PhpSpreadsheet with memory management
        return self::readPhpSpreadsheetChunked($filePath, $callback);
    }

    private static function readXlsxStream(string $filePath, callable $callback): int
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return self::readPhpSpreadsheetChunked($filePath, $callback);
        }

        // 1. Read sharedStrings.xml if present
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml) {
            $reader = new XMLReader();
            $reader->XML($sharedStringsXml);
            while ($reader->read()) {
                if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'si') {
                    $siXml = $reader->readOuterXML();
                    $siReader = new XMLReader();
                    $siReader->XML($siXml);
                    $str = '';
                    while ($siReader->read()) {
                        if ($siReader->nodeType == XMLReader::ELEMENT && $siReader->name == 't') {
                            $str .= $siReader->readString();
                        }
                    }
                    $siReader->close();
                    $sharedStrings[] = $str;
                }
            }
            $reader->close();
            unset($sharedStringsXml);
        }

        // 2. Locate first sheet XML
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (!$sheetXml) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                if ($stat && str_contains($stat['name'], 'worksheets/sheet')) {
                    $sheetXml = $zip->getFromName($stat['name']);
                    break;
                }
            }
        }

        if (!$sheetXml) {
            $zip->close();
            return self::readPhpSpreadsheetChunked($filePath, $callback);
        }

        $reader = new XMLReader();
        $reader->XML($sheetXml);
        $rowCount = 0;

        while ($reader->read()) {
            if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'row') {
                $rowNumAttr = $reader->getAttribute('r');
                $rowNum = $rowNumAttr ? (int)$rowNumAttr : ($rowCount + 1);
                $rowXml = $reader->readOuterXML();

                $cells = [];

                if (preg_match_all('/<c\b[^>]*(?:\/>|>.*?<\/c>)/s', $rowXml, $cellMatches)) {
                    foreach ($cellMatches[0] as $cellXml) {
                        // Extract column letter from r="Q4" or r="A1"
                        $colLetter = 'A';
                        if (preg_match('/r="([A-Z]+)\d+"/', $cellXml, $rMatch)) {
                            $colLetter = $rMatch[1];
                        }

                        // Extract cell type t="..."
                        $type = '';
                        if (preg_match('/t="([^"]+)"/', $cellXml, $tMatch)) {
                            $type = $tMatch[1];
                        }

                        // Check if cell has formula <f>
                        $hasFormula = str_contains($cellXml, '<f') || str_contains($cellXml, '<f>');

                        // Extract value inside <v>...</v>
                        $val = '';
                        if (preg_match('/<v[^>]*>(.*?)<\/v>/s', $cellXml, $vMatch)) {
                            $val = trim($vMatch[1]);
                        } elseif ($hasFormula && preg_match('/<f[^>]*>(.*?)<\/f>/s', $cellXml, $fMatch)) {
                            $val = trim($fMatch[1]);
                        }

                        // Resolve shared string or inline string
                        if (!$hasFormula && $type === 's' && is_numeric($val) && isset($sharedStrings[(int)$val])) {
                            $val = $sharedStrings[(int)$val];
                        } elseif ($type === 'inlineStr' || (empty($val) && str_contains($cellXml, '<t'))) {
                            if (preg_match_all('/<t[^>]*>(.*?)<\/t>/s', $cellXml, $tMatches)) {
                                $val = implode('', $tMatches[1]);
                            }
                        }

                        $cells[$colLetter] = $val;
                    }
                }

                unset($rowXml);

                if (!empty($cells)) {
                    $rowCount++;
                    $callback($rowNum, $cells);
                }
            }
        }

        $reader->close();
        $zip->close();
        unset($sharedStrings);

        return $rowCount;
    }

    private static function readCsv(string $filePath, callable $callback): int
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) return 0;

        $rowNum = 0;
        while (($data = fgetcsv($handle, 8192, ',')) !== false) {
            $rowNum++;
            $cells = [];
            foreach ($data as $idx => $val) {
                $colLetter = self::indexToColumn($idx);
                $cells[$colLetter] = $val;
            }
            $callback($rowNum, $cells);
        }
        fclose($handle);
        return $rowNum;
    }

    private static function readPhpSpreadsheetChunked(string $filePath, callable $callback): int
    {
        $reader = IOFactory::createReaderForFile($filePath);
        if (method_exists($reader, 'setReadDataOnly')) {
            $reader->setReadDataOnly(true);
        }

        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestCol = $sheet->getHighestColumn();

        for ($row = 1; $row <= $highestRow; $row++) {
            $cells = [];
            for ($col = 'A'; $col <= $highestCol; $col++) {
                if ($sheet->cellExists($col . $row)) {
                    $cells[$col] = $sheet->getCell($col . $row)->getValue();
                }
            }
            if (!empty($cells)) {
                $callback($row, $cells);
            }
        }

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet, $sheet);

        return $highestRow;
    }

    private static function indexToColumn(int $index): string
    {
        $numeric = $index % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($index / 26);
        if ($num2 > 0) {
            return self::indexToColumn($num2 - 1) . $letter;
        }
        return $letter;
    }
}
