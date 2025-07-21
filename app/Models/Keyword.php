<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Keyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword',
        'parent_id',
        'type',
    ];

    /**
     * Get the parent keyword that owns the current keyword.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Keyword::class, 'parent_id');
    }

    /**
     * Get the child keywords for the current keyword.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Keyword::class, 'parent_id');
    }

    /**
     * The studies that belong to the Keyword.
     */
    public function studies(): BelongsToMany
    {
        return $this->belongsToMany(Study::class);
    }
}
