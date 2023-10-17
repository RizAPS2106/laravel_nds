<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\ThisYearScope;

class FormCutInput extends Model
{
    use HasFactory;

    protected $table = 'form_cut_input';

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ThisYearScope);
    }

    /**
     * Get the cutting plan for the form cut.
     */
    public function cuttingPlans()
    {
        return $this->hasMany(CutPlan::class, 'no_form_cut_input', 'no_form');
    }
}
