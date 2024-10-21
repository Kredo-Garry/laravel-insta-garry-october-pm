<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    const ADMIN_ROLE_ID = 1; //Admin
    const USER_ROLE_ID = 2; //Regular user

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Use this method to get all the posts of a user
     */
    public function posts(){
        return $this->hasMany(Post::class)->latest();
    }

    /**
     * Use this method to get all the followers of a user
     */
    public function followers(){
        return $this->hasMany(Follow::class, 'following_id');
    }

    /**
     *  Users table
     * id              name
     * 1              John Smith
     * 2              Tim Watson
     * 3              Mark Twain
     * 4              Mary
     * 5              Will
     * 
     *  Follows table
     * 
     * followers_id                 following_id
     * ------------------------------------------
     *   1                             2
     *   1                             3
     *   1                             5
     *   2                             4 
     *   2                             5
     *   5                             1
     *   5                             2
     */

     /**
      * Use this method to get all the users that the user is following
      */
      public function following(){
        return $this->hasMany(Follow::class, 'follower_id');
      }

      /**
       * Method to use in checking if the user is already following a user
       */
      public function isFollowed(){
        return $this->followers()->where('follower_id', Auth::user()->id)->exists();
        //Auth::user()->id  --> is always the follower
        //Firstly, get all teh followers of the user ($this->follower() ). Then, from that list, search for the Auth user id from the follower column (where('follower_id', Auth::user()->id))
      }
}
