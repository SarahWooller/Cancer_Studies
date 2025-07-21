<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Study extends Model
{
    use HasFactory;

    // Mass assignable attributes
    protected $fillable = [
        'title',
        'metadata',
    ];

    // Cast the 'metadata' attribute to an array/object when retrieved from the database
    // This makes it easy to work with JSON data in PHP
    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * The keywords that belong to the Study.
     */
    public function keywords(): BelongsToMany
    {
        return $this->belongsToMany(Keyword::class);
    }
}
