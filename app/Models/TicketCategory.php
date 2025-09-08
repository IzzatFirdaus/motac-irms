<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * TicketCategory Model (Helpdesk Ticket Category).
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $tickets
 * @property-read int|null $tickets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereUpdatedBy($value)
 * @mixin \Eloquent
 * @mixin IdeHelperTicketCategory
 */
class TicketCategory extends Model
{
    use HasFactory;

    protected $table = 'ticket_categories';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Tickets under this category.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }
}
