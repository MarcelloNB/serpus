<?php

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    public const SPINE_COLORS = ['#2E6B4F', '#2E6E6B', '#6B4E7A', '#8C3B4A', '#A8552B', '#B98B2E'];

    protected $fillable = ['category_id', 'title', 'author', 'description', 'cover_image', 'stock'];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
        ];
    }

    public static function spineColorFor(?int $categoryId): string
    {
        if ($categoryId === null) {
            return '#8A8477';
        }

        return self::SPINE_COLORS[$categoryId % count(self::SPINE_COLORS)];
    }

    public function getSpineColorAttribute(): string
    {
        return self::spineColorFor($this->category_id);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }
}
