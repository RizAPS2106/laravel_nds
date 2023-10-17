<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\ThisYearScope;

class CutPlan extends Model
{
    use HasFactory;

    protected $table = 'cutting_plan';

    protected $guarded = [];

    /**
     * Get the form cut data.
     */
    public function formCutInput()
    {
        return $this->belongsTo(FormCutInput::class, 'no_form_cut_input', 'no_form');
    }
}
