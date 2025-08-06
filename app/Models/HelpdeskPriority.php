<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy; // Assuming this is your Blameable trait
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes; // Added SoftDeletes as per plan for priorities

class HelpdeskPriority extends Model
{
    use HasFactory, CreatedUpdatedDeletedBy, SoftDeletes; // Added SoftDeletes and CreatedUpdatedDeletedBy

    protected $fillable = [
        'name',
        'level',
        'color_code',
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'priority_id');
    }
}
