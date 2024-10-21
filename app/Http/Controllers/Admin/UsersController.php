<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    private $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function index(){
        /**
         * The withTrashed() -> will include the soft deleted users records in the query result
         */
        $all_users = $this->user->withTrashed()->latest()->paginate(4);
        return view('admin.users.index')->with('all_users', $all_users);
    }

    /**
     * Create a method to softdelete a user
     */
    public function deactivate($user_id){
        $this->user->destroy($user_id);
        return redirect()->back();
    }

    /**
     * Method to restore the soft deleted users
     */
    public function activate($user_id){
        $this->user->onlyTrashed()->findOrFail($user_id)->restore();
        /**
         * The onlyTrashed() ---> retrieves soft delete records only
         * restore() --> This will un-delete a soft deleted model/record. This will set the "deleted_at" column to null
         */


        return redirect()->back();
    }
}
