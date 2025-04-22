<?php

namespace App\Services;

use App\Models\ReportSource;
use App\Models\ReportFile;
use App\Models\ReportEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Ep747DataLoadService
{
    private ?ReportSource $currentReportSource = null;
    private ?ReportFile $currentReportFile = null;
    private string $currentReportType = '';
    private ?Carbon $currentProcessingDate = null;
    private ?Carbon $currentReportDate = null;
    private int $currentPage = 1;

    public function loadData(int $reportSourceId): array
    {
        $reportSource = ReportSource::findOrFail($reportSourceId);
        $this->currentReportSource = $reportSource;
        $fullPath = storage_path('app/private/' . $reportSource->file_path);

        if (!file_exists($fullPath)) {
            Log::error("Arquivo não encontrado: " . $fullPath);
            throw new \Exception("Arquivo não encontrado: " . $reportSource->file_path);
        }

        Log::info("Iniciando processamento do arquivo EP747", [
            'relatorio_id' => $reportSourceId,
            'file' => $fullPath
        ]);

        $stats = [
            'lines_total' => 0,
            'entries_created' => 0,
            'errors' => [],
            'start_time' => microtime(true),
        ];

        DB::transaction(function () use ($fullPath, &$stats) {


            $handle = fopen($fullPath, 'r');
            if (!$handle) {
                throw new \Exception("Não foi possível abrir o arquivo: {$fullPath}");
            }

            while (($line = fgets($handle)) !== false) {
                $stats['lines_total']++;
                $line = trim($line);

                try {
                    $this->processLine($line);
                } catch (\Throwable $e) {
                    $stats['errors'][] = [
                        'line' => $line,
                        'error' => $e->getMessage()
                    ];
                }
            }
            fclose($handle);
        });

        $stats['end_time'] = microtime(true);
        $stats['processing_time'] = round($stats['end_time'] - $stats['start_time'], 2);

        return $stats;
    }


    private function cleanLine(string $line): string
    {
        // Remove caracteres de controle e múltiplos espaços
        $cleaned = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $line);
        return preg_replace('/\s+/', ' ', trim($cleaned));
    }

    private function processLine(string $line): void
    {
        $line = $this->cleanLine($line);// remove caracteres de controle no início

        if (strpos($line, 'REPORT ID:') !== false) {
            Log::debug("Processando linha:", ['line' => $line]);
            // "REPORT ID:" foi encontrado, agora tentamos extrair a parte do VSS
            if (preg_match('/(VSS-\d{3}(?:-[A-Z])?)/i', $line, $matches)) {
                $this->startNewReportFile($matches[1]);
                Log::info("Novo relatório iniciado", ['report_type' => $matches[1]]);
                return;
            } else {
                // "REPORT ID:" encontrado, mas não seguiu o padrão VSS
                Log::warning("Linha com 'REPORT ID:' mas sem padrão VSS esperado.", ['linha' => $line]);
                //return;
            }
        }

        if (preg_match('/PAGE:\s*(\d+)/i', $line, $matches)) {
            $this->currentPage = (int) $matches[1];
            Log::debug("Página detectada", ['page' => $this->currentPage]);
            return;
        }

        if (preg_match('/REPORT\s+DATE\s*:\s*(\d{2}[A-Z]{3}\d{2})/', $line, $matches)) {
            $this->currentReportDate = $this->parseEp747Date($matches[1]);
            Log::debug("Data do relatório detectada", ['report_date' => $this->currentReportDate]);
        }

        if (preg_match('/PROC\s+DATE\s*:\s*(\d{2}[A-Z]{3}\d{2})/', $line, $matches)) {
            $this->currentProcessingDate = $this->parseEp747Date($matches[1]);
            Log::debug("Data de processamento detectada", ['proc_date' => $this->currentProcessingDate]);
        }

        if ($this->currentReportFile) {
            ReportEntry::create([
                'report_file_id' => $this->currentReportFile->id,
                'report_type' => $this->currentReportType,
                'entry_level' => 'raw',
                'category' => 'UNKNOWN',
                'raw_line' => $line,
                'report_date' => $this->currentReportDate,
                'proc_date' => $this->currentProcessingDate,
                'currency' => 'BRL',
                'page' => $this->currentPage,
            ]);
            Log::debug("Linha registrada em ReportEntry", [
                'report_type' => $this->currentReportType,
                'page' => $this->currentPage
            ]);
        }
    }

    private function startNewReportFile(string $reportType): void
    {
        try {
            $this->currentReportType = $reportType;
            $this->currentReportFile = ReportFile::create([
                'report_source_id' => $this->currentReportSource->id,
                'report_type' => $reportType,
                'report_date' => $this->currentReportDate,
                'proc_date' => $this->currentProcessingDate,
                'currency' => 'BRL',
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }

    }

    private function parseEp747Date(string $dateString): ?Carbon
    {
        if (strlen($dateString) !== 7) {
            return null;
        }

        $day = substr($dateString, 0, 2);
        $month = strtoupper(substr($dateString, 2, 3));
        $year = '20' . substr($dateString, 5, 2);

        $monthMap = [
            'JAN' => 1,
            'FEB' => 2,
            'MAR' => 3,
            'APR' => 4,
            'MAY' => 5,
            'JUN' => 6,
            'JUL' => 7,
            'AUG' => 8,
            'SEP' => 9,
            'OCT' => 10,
            'NOV' => 11,
            'DEC' => 12,
        ];

        if (!isset($monthMap[$month])) {
            return null;
        }

        return Carbon::createFromDate((int) $year, $monthMap[$month], (int) $day);
    }
}
