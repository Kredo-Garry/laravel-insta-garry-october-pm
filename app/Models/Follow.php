<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;
    public $timestamps = false;

    /**
     * Use this method to get the info of the follower
     */
    public function follower(){
        return $this->belongsTo(User::class, 'follower_id')->withTrashed();
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
      * Use this method to get the info of the user being followed
      */
      public function following(){
        return $this->belongsTo(User::class, 'following_id')->withTrashed();
      }
}
