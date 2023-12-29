<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QcInspectTemp extends Model
{
    use HasFactory;

    protected $connection = 'mysql_sb';

    protected $table = 'whs_qc_insp_dettemp';

    protected $guarded = [];

    /**
     * Get the marker details for the marker.
     */
    // public function markerDetails()
    // {
    //     return $this->hasMany(MarkerDetail::class, 'marker_id', 'id');
    // }
}