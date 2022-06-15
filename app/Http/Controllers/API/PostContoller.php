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

/**
 * @OA\Info(
 *    title="Posts",
 *    version="1.0.0",
 * )
 * 
*/


class PostContoller extends Controller
{ 
 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    
    /**
    * @OA\Get(
    *     path="/api/posts",
    *     summary="List of Posts",
    *     @OA\Response(
    *         response=200,
    *         description="OK",
    
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 @OA\Property(
    *                     property="website_id",
    *                     type="integer"
    *                 ),
    *                 @OA\Property(
    *                     property="title",
    *                     type="string"
    *                 ),
    *                 @OA\Property(
    *                     property="author",
    *                     type="string"
    *                 ),
    *                 @OA\Property(
    *                     property="content",
    *                     type="string"
    *                 ),
    *                 example={"title": "This is title for post #1", "content": "Post content #1", "author": "John Doe", "website_id": 1}
    *             )
    *         )
    *     ),
    * )
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

    /**
    * @OA\Post(
    *     path="/api/posts",
    *     summary="Create new Post",
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 @OA\Property(
    *                     property="website_id",
    *                     type="integer"
    *                 ),
    *                 @OA\Property(
    *                     property="title",
    *                     type="string"
    *                 ),
    *                 @OA\Property(
    *                     property="author",
    *                     type="string"
    *                 ),
    *                 @OA\Property(
    *                     property="content",
    *                     type="string"
    *                 ),
    *                 example={"title": "This is title for post #1", "content": "Post content #1", "author": "John Doe", "website_id": 1}
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="OK",
    *         @OA\JsonContent(
    *             @OA\Examples(example="result", value={"id": 1, "title": "This is title for post #1", "content": "Post content #1", "author": "John Doe", "website_id": 1}, summary="An result object."),
    *         )
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="Invalid input",
    *         @OA\JsonContent(
    *             @OA\Examples(example="result", value={"error": "Invalid input: website doesn't exists"}, summary="Validation error."),
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Internal error",
    *         @OA\JsonContent(
    *             @OA\Examples(example="result", value={"error": "Internal error: something went wrong, try again please"}, summary="Server error."),
    *         )
    *     ),
    * )
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

    /**
    * @OA\Get(
    *     path="/api/posts/{id}",
    *     summary="Get single post",
    *     @OA\Parameter(
    *         description="post id",
    *         in="path",
    *         name="id",
    *         required=true,
    *         @OA\Schema(type="integer"),
    *         @OA\Examples(example="int", value="1", summary="An int value."),
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="OK",
    *         @OA\JsonContent(
    *             @OA\Examples(example="result", value={"id": 1, "title": "This is title for post #1", "content": "Post content #1", "author": "John Doe", "website_id": 1}, summary="An result object."),
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Not found",
    *     ),
    * )
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

    /**
    * @OA\Delete(
    *     path="/api/posts/{id}",
    *     summary="Delete single post",
    *     @OA\Parameter(
    *         description="post id",
    *         in="path",
    *         name="id",
    *         required=true,
    *         @OA\Schema(type="integer"),
    *         @OA\Examples(example="int", value="1", summary="An int value."),
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="OK",
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Not found",
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Internal error",
    *         @OA\JsonContent(
    *             @OA\Examples(example="result", value={"error": "Internal error: something went wrong, try again please"}, summary="Server error."),
    *         )
    *     ),
    * )
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
