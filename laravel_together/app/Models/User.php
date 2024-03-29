<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\FriendRequest;

use App\Models\Project;
use App\Models\Comment;
use App\Models\ProjectUser;
class User extends Authenticatable // 유저
{
    use HasApiTokens, HasFactory, Notifiable;

    // 특정 사용자에게 보낸 친구 요청이 있는지 확인
    public function hasPendingFriendRequestTo(User $user)
    {
        return $this->friendRequeststo()->where('from_user_id', $user->id)->exists();
    }

    // 특정 사용자로부터 받은 친구 요청이 있는지 확인
    public function hasPendingFriendRequestFrom(User $user)
    {
        return $this->friendRequestsfrom()->where('to_user_id', $user->id)->exists();
    }

    // 유저에게 보낸 친구요청
    public function friendRequeststo()
    {
        return $this->hasMany(FriendRequest::class, 'to_user_id');
    }

     // 유저로 부터 받은 친구요청
    public function friendRequestsfrom()
    {
        return $this->hasMany(FriendRequest::class, 'from_user_id');
    }

    protected $fillable = [
        'name',
        'online_flg',
        'last_activity',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // 모델 연관 관리
    public function comments(){
        // return $this->belongsTo(Task::class,'task_id','id'); 이걸 생략하면        
        return $this->hasMany(Comment::class);
    }
    public function  projects(){      
        return $this->hasMany(Project::class);
    }
    public function  project_users(){      
        return $this->hasMany(ProjectUser::class,'id','member_id');
    }
}
