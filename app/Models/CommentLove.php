<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentLove extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'comment_id'];
    protected $table = 'comment_loves'; // <-- مهم


    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
