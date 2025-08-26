<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * TicketCategory model for Helpdesk system.
 * Represents categories like Hardware, Software, etc.
 *
 * @property int                             $id
 * @property string                          $name
 * @property string|null                     $description
 * @property bool                            $is_active
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class TicketCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * Tickets under this category.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }
}
