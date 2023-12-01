<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
      'desc',
      'img',
    ];
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments():HasMany
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    public function likes():HasMany
    {
        return $this->hasMany(Like::class, 'post_id');
    }

    protected static function booted()
    {
        static::addGlobalScope('creator', function (Builder $builder){
            $builder->where('user_id', Auth::id());
        });
    }
}
