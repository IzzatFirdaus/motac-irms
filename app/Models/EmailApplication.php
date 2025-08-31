<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Minimal EmailApplication model used for static analysis compatibility.
 * 
 * Real implementation may be more feature rich.
 *
 * @property int $id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication query()
 * @mixin \Eloquent
 */
class EmailApplication extends Model
{
    // Intentionally minimal: this file exists to satisfy static analysis where the class is referenced.
}
