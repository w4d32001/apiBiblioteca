<?php

namespace App\Models;

use App\Traits\Audit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;
    use Audit;

    protected $fillable = [
        'name',
        'isbm',
        'price',
        'publication_date',
        'description',
        'image',
        'pdf',
        'category_id',
        'author_id',
        'status',
        'created_by',
        'updated_by'
    ];

    protected static function boot()
    {
        parent::boot();
        static::bootAudit();
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function category():BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author():BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function sales():HasMany
    {
        return $this->hasMany(Sale::class);
    }

}