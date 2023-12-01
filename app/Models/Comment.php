<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';

    protected $fillable = [
        'desc',
        'user_id',
        'post_id',
    ];

    public function post():BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    protected static function booted()
    {
        static::addGlobalScope('creator', function (Builder $builder){
            $builder->where('user_id', Auth::id());
        });
    }
}
