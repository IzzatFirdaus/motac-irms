<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * HelpdeskCategory Model.
 *
 * Represents categories for helpdesk tickets, such as Hardware, Software, etc.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property bool $is_active
 */
class HelpdeskCategory extends Model
{
    use HasFactory, CreatedUpdatedDeletedBy, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'category_id');
    }
}
