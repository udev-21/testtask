<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\SendPost;
use App\Models\Post;
use App\Models\PostSubscriber;
use App\Models\Subscriber;
use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PostContoller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //TODO: pagination
        return response()->json(Post::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated_data = $this->getValidated($request);

        if(Website::where('id', $validated_data['website_id'])->exists() === false){
            return response()->json(['error'=>"Invalid input: website doesn't exists"], 400);
        }
        
        $newPost = new Post;
        $newPost->website_id = $validated_data['website_id'];
        $newPost->author = $validated_data['author'];
        $newPost->title = $validated_data['title'];
        $newPost->content = $validated_data['content'];

        if ($newPost->save() === false){
            return response()->json(['error'=>"Internal error: something went wrong, try again please"], 500);
        }
        
        $subs = Subscriber::where("website_id", $validated_data['website_id'])->get();

        foreach($subs as $s) {
            Mail::to($s->email)->queue((new SendPost($newPost)));
        }

        return response()->json($newPost);
    }

    /**
     * Display the specified resource.
     *
     * @param  Post  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Post $post)
    {
        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Post  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Post $post)
    {
        // I'll leave empty for now
        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Post  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post)
    {
        try{
            if ($post->delete() === false) {
                return response()->json(['error'=>"Internal error: something went wrong, try again please"], 500);
            }
            return response()->json([], 204);
        }catch(\LogicException $e){
            return response()->json(['error'=>"Internal error: something went wrong, try again please"], 500);
        }
    }


    private function getValidated(Request $request){
        return $request->validate([
            "website_id"=>"required|integer",
            "author"=>"required|string|max:255|min:2",
            "title"=>"required|string|max:255|min:3",
            "content"=>"required|string|max:16777215|min:3", // 16777215 -> mysql mediumtext maximum length 
        ]);
    }
}
