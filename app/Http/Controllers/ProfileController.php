<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    private $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function show($user_id){
        $user = $this->user->findOrFail($user_id);
        return view('users.profile.show')->with('user', $user);
    }

    public function edit(){
        $user = $this->user->findOrFail(Auth::user()->id);
        return view('users.profile.edit')->with('user', $user);
    }

    /**
     * Method to update the user details
     */
    public function update(Request $request){
        # 1. Validate the data
        $request->validate([
            'name' => 'required|min:1|max:50',
            'email' => 'required|email|max:50|unique:users,email,' . Auth::user()->id,
            'avatar' =>'mimes:jpeg,jpg,png,gif|max:1048',
            'introduction' => 'max:100'
        ]);

        # 2. Save the new data into the Db
        $user = $this->user->findOrFail(Auth::user()->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->introduction = $request->introduction;

        # Check if avatar is uploaded
        if ($request->avatar) {
            $user->avatar = 'data:image/' . $request->avatar->extension() . ';base64,' . base64_encode(file_get_contents($request->avatar));
        }

        # Save
        $user->save();


        # 3. Redirect to profile page
        return redirect()->route('profile.show', Auth::user()->id);
    }

    /**
     * Method to get the user details
     */
    public function followers($user_id){
        $user = $this->user->findOrFail($user_id);
        return view('users.profile.followers')->with('user', $user);
    }

    /**
     * Method use to get the all the users that the User is following
     */
    public function following($user_id){
        $user = $this->user->findOrFail($user_id);
        return view('users.profile.following')->with('user', $user);
    }
}
