<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class ProvinImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue, WithCustomCsvSettings
{

    public function chunkSize(): int
    {
        return 1000;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ";",
        ];
    }

    public function collection(Collection $rows): void
    {
        $dataToUpsert = [];

        foreach ($rows as $row) {

            $processDateRaw = $row['update_date'] ?? null;

            $processDate = null;
            if ($processDateRaw) {
                try {
                    $processDate = Carbon::createFromFormat('n/j/Y H:i', $processDateRaw)->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    $processDate = null;
                }
            }

            $dataToUpsert[] = [
                'ODP_EID'         => $row['device_id'] ?? null,
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
                'OCCUPANCY'       => ($row['used'] / $row['is_total']),
                'CREATEDDATE'     => $row['tgl_golive'],
                'PROCESS_DATE'    => $processDate,
                'ISI'             => $row['used'] ?? null,
                'ISI_DESCRIPTION' => $row['rsv'] ?? null,
                'KOSONG'          => $row['avai'] ?? null,
                'TOTAL'           => $row['is_total'] ?? null,
                'ODC'             => $this->getStringBeforeSlash($row['odp_name'] ?? ''),
            ];
        }

        DB::table('ODP_INFO')->upsert($dataToUpsert, ['ODP_EID','ODP_ID'], [
            'REGIONAL','WITEL','DATEL','STO','STO_NAME','ODP_NAME','ODP_LOCATION','LATITUDE','LONGITUDE', 'OCCUPANCY', 'CREATEDDATE','PROCESS_DATE','ISI','ISI_DESCRIPTION','KOSONG','TOTAL','ODC'
        ]);
    }

    private function getStringBeforeSlash($string): string
    {
        if (str_contains($string, '/')) {
            return explode('/', $string)[0];
        }
        return $string;
    }
}
