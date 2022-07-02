<?php

namespace App\Models;

use App\Jobs\NewsSendEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;

class Blog extends Model
{
    use HasFactory;

    public function Category()
    {
        return $this->belongsTo(Category::class);
    }

    public function Author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function Comments()
    {
        return $this->hasMany(Comment::class);
    }

    protected static function booted()
    {
        static::created(function () {
            $email = new NewsSendEmail(Subscriber::with('user')->get()->pluck('user')->toArray());
            Bus::dispatch($email);
        });
    }
}
