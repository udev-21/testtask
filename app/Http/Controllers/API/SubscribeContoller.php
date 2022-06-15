<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostSubscriber;
use App\Models\Subscriber;
use App\Models\Website;
use Illuminate\Http\Request;

class SubscribeContoller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    /**
    * @OA\Post(
    *     path="/api/subscribe/{website_id}",
    *     summary="Create new Website",
    *     @OA\Parameter(
    *         description="website id",
    *         in="path",
    *         name="website_id",
    *         required=true,
    *         @OA\Schema(type="integer"),
    *         @OA\Examples(example="int", value="1", summary="An int value."),
    *     ),
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 @OA\Property(
    *                     property="email",
    *                     type="string"
    *                 ),
    *                 example={"email": "test@gmail.com"}
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="OK",
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Website doesn't exists",
    *         @OA\JsonContent(
    *             @OA\Examples(example="result", value={"error": "website exists"}, summary="Validation error."),
    *         )
    *     ),
    * )
    */
    public function subscribe(Request $request, Website $website)
    {
        $validated_data = $this->getValidated($request);
        
        Subscriber::insertOrIgnore(
            array_merge($validated_data, [
                'website_id'=> $website->id,
                'created_at'=> time(),
                'updated_at'=> time(),
            ]
        ));

        return response()->json();
    }
    
    public function all(Request $request)
    {
        return response()->json(Subscriber::all());
    }

    public function queue(Request $request)
    {
        return response()->json(PostSubscriber::all());
    }

    private function getValidated(Request $request){
        return $request->validate([
            "email"=>"required|email",
        ]);
    }
}
