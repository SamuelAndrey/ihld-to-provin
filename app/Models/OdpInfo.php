<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OdpInfo extends Model
{
    use HasFactory;

    protected $table = 'ODP_INFO';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'ODP_EID',
        'ODP_ID',
        'REGIONAL',
        'WITEL',
        'DATEL',
        'STO',
        'STO_NAME',
        'ODP_NAME',
        'ODP_LOCATION',
        'LATITUDE',
        'LONGITUDE',
        'OCCUPANCY',
        'CREATEDDATE',
        'PROCESS_DATE',
        'ISI',
        'ISI_DESCRIPTION',
        'KOSONG',
        'TOTAL',
        'ODC',
    ];

}
