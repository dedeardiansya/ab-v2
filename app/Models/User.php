<?php

namespace App\Models;

use App\Helpers\AvatarHelper;
use App\Http\Resources\MinitutorsResource;
use App\Http\Resources\PostsResource;
use App\Http\Resources\UsersResource;
use App\Notifications\Auth\VerifyEmailNotification;
use App\Notifications\Auth\ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Password;
use Laravel\Passport\HasApiTokens;
use Overtrue\LaravelSubscribe\Traits\Subscriber;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasRoles, Subscriber, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email_verified_at',
        'name',
        'avatar',
        'points',
        'about',
        'website_url',
        'twitter_url',
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'username',
        'email',
        'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    /**
     * Get the minitutor relation.
     */
    public function minitutor()
    {
        return $this->hasOne(Minitutor::class);
    }

    /**
     * Return the avatar url or placeholder url.
     */
    public function avatarUrl($nulllable = false)
    {
        if($nulllable && !$this->avatar) {
            return null;
        }
        return AvatarHelper::getUrl($this->avatar);
    }

    /**
     * Get the comments relation.
     */
    public function comments() : HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Increment user point.
     */
    public function incrementPoint($point) {
        $this->points = $this->points + $point;
        return $this->save();
    }

    /**
     * Get the feedback relation.
     */
    public function feedback() : HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * Get the activities relation.
     */
    public function activities() : HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Send email verification.
     */
    public function sendMemberEmailVerify() : void
    {
        $this->notify(new VerifyEmailNotification);
    }

    /**
     * Generate remember password token code.
     */
    public function generateRemeberPasswordToken() : string
    {
        return Password::broker()->createToken($this);
    }

    /**
     * Send email forget password.
     */
    public function sendMemberEmailResetPassword() : void
    {
        $this->notify(new ResetPasswordNotification($this->generateRemeberPasswordToken()));
    }

    /**
     * Return notification broadcast route.
     */
    public function receivesBroadcastNotificationsOn(): String
    {
        return 'App.User.'.$this->id;
    }

    /**
     * Return the generated Firebase Custom Token.
     */
    public function createFirebaseCustomToken(): String
    {
        $auth = app('firebase.auth');
        $uid = 'id-' . $this->id;
        $customToken = $auth->createCustomToken($uid);
        return (string) $customToken;
    }

    /**
     * Atributes
     */
    public function getAvatarUrlAttribute()
    {
        if(!$this->avatar) {
            return null;
        }
        return AvatarHelper::getUrl($this->avatar);
    }

    public function getListActivitiesAttribute()
    {
        $playlists = $this->activities()->with(['playlist' => function($q){
            Playlist::postListQuery($q);
        }])->whereHas('playlist')->where('activitiable_type', Playlist::class)->get();
        $articles = $this->activities()->with(['article' => function($q){
            Article::postListQuery($q);
        }])->whereHas('article')->where('activitiable_type', Article::class)->get();

        return $playlists->merge($articles)->transform(function($item){
            if($item->playlist){
                $post = $item->playlist;
            } else {
                $post = $item->article;
            }
            return [
                'id' => $item->id,
                'created_at' => $item->created_at->timestamp,
                'updated_at' => $item->updated_at->timestamp,
                'post' => PostsResource::make($post),
            ];
        })->sortByDesc('updated_at')->values()->all();
    }

    public function getFollowingsAttribute()
    {
        return $this->subscriptions()
            ->withType(Minitutor::class)
            ->with(['minitutor' => function($q){
                $q->with('user');
            }])
            ->whereHas('minitutor', function($q){
                $q->where('active', true);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->transform(function($item){
                return [
                    'id' => $item->id,
                    'user' => UsersResource::make($item->minitutor->user),
                    'minitutor' => MinitutorsResource::make($item->minitutor),
                ];
            });
    }

    public function getFavoritesAttribute()
    {
        $playlists = $this->subscriptions()
            ->withType(Playlist::class)
            ->with(['playlist' => function($q){
                return Playlist::postListQuery($q);
            }])
            ->get();

        $articles = $this->subscriptions()
            ->withType(Article::class)
            ->with(['article' => function($q){
                return Article::postListQuery($q);
            }])
            ->get();

        return $playlists->merge($articles)->transform(function($item){
            if($item->playlist){
                $post = $item->playlist;
            } else {
                $post = $item->article;
            }
            return [
                'id' => $item->id,
                'created_at' => $item->created_at->timestamp,
                'updated_at' => $item->updated_at->timestamp,
                'post' => PostsResource::make($post),
            ];
        })->sortByDesc('created_at')->values()->all();
    }

    public function getActiveMinitutorAttribute()
    {
        return $this->minitutor()->where('active', true)->exists();
    }

    public function getFavoriteIdsAttribute()
    {
        $playlists = $this->subscriptions()
            ->withType(Playlist::class)
            ->get()
            ->transform(function($item) {
                return $item->subscribable_id;
            });

        $articles = $this->subscriptions()
            ->withType(Article::class)
            ->get()
            ->transform(function($item) {
                return $item->subscribable_id;
            });

        return [
            'playlists' => $playlists,
            'articles' => $articles,
        ];
    }

    public function getFollowingIdsAttribute()
    {
        return $this->subscriptions()
            ->withType(Minitutor::class)
            ->get()
            ->transform(function($item) {
                return $item->subscribable_id;
            });
    }
}
