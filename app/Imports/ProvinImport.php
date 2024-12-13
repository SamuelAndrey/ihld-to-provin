<?php

namespace App\Imports;

use App\Models\OdpInfo;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProvinImport implements ToCollection, WithHeadingRow, WithCustomCsvSettings, WithChunkReading
{
    private array $processedRows = [];
    public int $failCount = 0;

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ";",
            'encoding'  => 'UTF-8',
        ];
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            try {
                $this->processRow($row);
            } catch (\Exception $e) {
                $this->failCount++;
            }
        }

        $this->bulkUpsert();
    }

    private function processRow($row): void
    {
        $deviceId = $row['device_id'] ?? null;

        // Skip if no device ID or already processed
        if (!$deviceId || isset($this->processedRows[$deviceId])) {
            return;
        }

        $transformedRow = $this->transformRow($row);

        if ($transformedRow) {
            $this->processedRows[$deviceId] = $transformedRow;
        }
    }

    private function bulkUpsert(): void
    {
        if (empty($this->processedRows)) {
            return;
        }

        DB::transaction(function () {
            $chunks = array_chunk(array_values($this->processedRows), 500);
            foreach ($chunks as $chunk) {
                OdpInfo::upsert($chunk, ['ODP_EID'], [
                    'ODP_ID', 'REGIONAL', 'WITEL', 'DATEL',
                    'STO', 'STO_NAME', 'ODP_NAME',
                    'ODP_LOCATION', 'LATITUDE', 'LONGITUDE',
                    'OCCUPANCY', 'CREATEDDATE', 'PROCESS_DATE',
                    'ISI', 'ISI_DESCRIPTION', 'KOSONG',
                    'TOTAL', 'ODC'
                ]);
            }
        });

        $this->processedRows = [];
    }

    private function transformRow($row): ?array
    {
        if (!isset($row['device_id'])) {
            return null;
        }

        return [
            'ODP_EID'         => $row['device_id'],
            'ODP_ID'          => $row['id_odp'] ?? null,
            'REGIONAL'        => $row['telkom_regional'] ?? null,
            'WITEL'           => $row['telkom_datel'] ?? null,
            'DATEL'           => $row['telkom_datel'] ?? null,
            'STO'             => $row['telkom_sto'] ?? null,
            'STO_NAME'        => $row['telkom_sto_deskripsi'] ?? null,
            'ODP_NAME'        => $row['odp_name'] ?? null,
            'ODP_LOCATION'    => $row['odp_name'] ?? null,
            'LATITUDE'        => $row['latitude'] ?? null,
            'LONGITUDE'       => $row['longitude'] ?? null,
            'OCCUPANCY'       => $this->calculateOccupancy($row['used'] ?? 0, $row['is_total'] ?? 1),
            'CREATEDDATE'     => $row['tgl_golive'] ?? null,
            'PROCESS_DATE'    => $this->safeParseDate($row['update_date'] ?? null),
            'ISI'             => $row['used'] ?? null,
            'ISI_DESCRIPTION' => $row['rsv'] ?? null,
            'KOSONG'          => $row['avai'] ?? null,
            'TOTAL'           => $row['is_total'] ?? null,
            'ODC'             => $this->getStringBeforeSlash($row['odp_name'] ?? ''),
        ];
    }

    private function safeParseDate(?string $dateRaw): ?string
    {
        if (!$dateRaw) {
            return null;
        }

        $formats = [
            'n/j/Y H:i',
            'Y-m-d H:i:s',
            'Y-m-d',
            'm/d/Y H:i',
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $dateRaw)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                continue;
            }
        }

        // If no format works, log and return null
        \Log::warning('Failed to parse date', [
            'raw_date' => $dateRaw,
            'formats_tried' => $formats
        ]);

        return null;
    }

    private function calculateOccupancy($used, $total): float
    {
        return $total > 0 ? $used / $total : 0;
    }

    private function getStringBeforeSlash($string): string
    {
        return str_contains($string, '/') ? explode('/', $string)[0] : $string;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
