<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HelpdeskCategory extends Model
{
    use HasFactory, CreatedUpdatedDeletedBy, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active', // Added as per the plan
    ];

    /**
     * Set default attributes for the model.
     * Ensure 'is_active' is true by default if not set.
     * You might also consider setting this in a migration or factory.
     */
    protected $attributes = [
        'is_active' => true,
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'category_id');
    }
}
