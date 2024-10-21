<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\category;
use Illuminate\Http\Request;

class PostController extends Controller
{
    private $post;
    private $category;

    public function __construct(Post $post, Category $category){
        $this->post = $post;
        $this->category = $category;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $all_categories = $this->category->all();
        return view('users.posts.create')
            ->with('all_categories', $all_categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        # 1. Validate the data first
        $request->validate([
            'category' => 'required|array|between:1,3',
            'description' => 'required|min:1|max:1000',
            'image' => 'required|mimes:jpeg,jpg,png,gif|max:1048'
        ]);

        # 2. Save the post details
        $this->post->user_id        = Auth::user()->id;
        $this->post->image          = 'data:image/' . $request->image->extension() . ';base64,' . base64_encode(file_get_contents($request->image)); //travel.jpeg --> #$^193j7^^%%923-==
        # data:image/jpeg;base64,#$^193j7^^%%9230987&&(*&%$$$$***(())))((&^%YY-==
        $this->post->description    = $request->description;
        $this->post->save(); //post id 1

        # Save the categories to the category_post (PIVOT table)
        foreach ($request->category as $category_id) {
            # $request->category[1,3,5]
            $category_post[] = ['category_id' => $category_id]; // 5
        }

        # Insert the post id and the category id's to the pivot table (category_post)
        # $this->post->categoryPost()
        # $category_post = [
        #  ['category_id => 1, 'post_id' => 1],
        #  ['category_id => 3, 'post_id' => 1],
        #  ['category_id => 5, 'post_id' => 1],

        # createMany() -- accepts a 2d Array ($category_post)
        #]
        $this->post->categoryPost()->createMany($category_post);
        

        # "$request->category" coming from form. If this have 3 categories, then the foreach will loop 3 times
        # $category_post[1, 3, 5];

        # first loop --> category id 1 (travel)
        # second loop --> category id 3 (lifestyle)
        # third loop --> category id 5 (career)

        # 4. Go back to the homepage
        return redirect()->route('index');
    }

    /**
     * Display the specified resource.
     */
    public function show($post_id)
    {
        $post = $this->post->findOrFail($post_id);
        return view('users.posts.show')->with('post', $post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
         $post = $this->post->findOrFail($id);
        
        /**
         * Prevent the user from modifying the id in the
         * url bar to edit the post that they do not own
         */
         if (Auth::user()->id != $post->user->id) {
             return redirect()->route('index');
         }
 
         /**
          * get all the lists og category from the categories table
          */
         $all_categories =  $this->category->all();
         $selected_categories = [];

        foreach ($post->categoryPost as $category_post) {
             $selected_categories[] = $category_post->category_id;
         }
 
         return view('users.posts.edit')
             ->with('post', $post)
             ->with('all_categories', $all_categories)
             ->with('selected_categories', $selected_categories);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'category' => 'required|array|between:1,3',
            'description' => 'required|min:1|max:1000',
            'image' => 'mimes:jpeg,jpg,png,gif|max:1048'
        ]);

        $post   = $this->post->findOrFail($id); // post 1, owned by john smith
        $post->description = $request->description; //get the new post description from the form

        /**
         * Check if the user uploaded a new image
         */
        if ($request->image) {
            $post->image = 'data:image/' . $request->image->extension() . ';base64,' . base64_encode(file_get_contents($request->image));
        }
        $post->save();

        /**
         * Delete all the category ids of this specific posts from category_post table
         */
        $post->categoryPost()->delete();

        foreach ($request->category as $category_id) {
            $category_post[] = ['category_id' => $category_id];
        }
        $post->categoryPost()->createMany($category_post);

        return redirect()->route('post.show', $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($post_id)
    {
        $this->post->findOrFail($post_id)->forceDelete();
        return redirect()->route('index');
    }
}
