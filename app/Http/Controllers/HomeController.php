<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;

class HomeController extends Controller
{
    private $post;
    private $user;

    public function __construct(Post $post, User $user){
        $this->post = $post;
        $this->user = $user;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // $all_posts = $this->post->latest()->get();
        // return view('users.home')->with('all_posts', $all_posts); //homepage

        $home_posts = $this->getHomePosts();
        $suggested_users = $this->getSuggestedUsers();

        return view('users.home')
            ->with('home_posts', $home_posts)
            ->with('suggested_users', $suggested_users);
    }

    /**
     * Filter the posts that will be displayed in the homepage
     * Only the posts of the user that the Auth user if following should be displayed
     */

     private function getHomePosts(){
        $all_posts = $this->post->latest()->get(); //get all the posts from the posts table
        $home_posts = []; //declare an empty array. In case the $home_posts is empty, it will not return NULL, but empty instead

        foreach ($all_posts as $post) {
            if ($post->user->isFollowed() || $post->user->id === Auth::user()->id) { // this will return true if the Auth user already following that user
                $home_posts[] = $post;
            }
        }

        return $home_posts;
     }

     /**
      * Get the lists of users that the AUTH USER is not following yet
      */
      private function getSuggestedUsers(){
        $all_users = $this->user->all()->except(Auth::user()->id);
        $suggested_users = [];

        foreach ($all_users as $user) {
            if (! $user->isFollowed()) {
                $suggested_users[] = $user;
            }
        }

        //return $suggested_users; //this array contains the lists of users that are not yet being followed by the logged-in users.

        return array_slice($suggested_users, 0, 4);
        # array_slice(x,y,z)
        # array_slice(array, starting index, limit)

      }

      /**
       *  Method to search user
       */
      public function search(Request $request){
        $users = $this->user->where('name', 'like', '%' . $request->search . '%')->get();
        /**
         * Users table
         * 
         *  1.  John Smith
         *  2.  John Doe
         *  3.  Jane Merry
         *  4.  John II Mcartney
         * 5.   Tim John Davis
         */


        return view('users.search')
            ->with('users', $users)
            ->with('search', $request->search);
      }
}
