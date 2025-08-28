<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rental extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'article_in_store_id',
        'user_id',
        'rented_at',
        'returned_at',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'article_in_store_id' => 'integer',
            'user_id' => 'integer',
            'rented_at' => 'timestamp',
            'returned_at' => 'timestamp',
        ];
    }

    public function articleInStore(): BelongsTo
    {
        return $this->belongsTo(ArticleInStore::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
