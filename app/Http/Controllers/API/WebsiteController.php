<?php

namespace App\Http\Controllers\API;

use App\Models\Website;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class WebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    /**
    * @OA\Get(
    *     path="/api/websites",
    *     summary="List of websites ",
    *     @OA\Response(
    *         response=200,
    *         description="OK",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 @OA\Property(
    *                     property="id",
    *                     type="string",
    *                 ),
    *                 @OA\Property(
    *                     property="hostname",
    *                     type="string",
    *                  ),
    *               ),
    *               example={"id": 10, "hostname": "https://kun.uz" }
    *             )
    *         ),
    *     ),
    * )
    */
    public function index()
    {
        //TODO: pagination
        return response()->json(Website::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    /**
    * @OA\Post(
    *     path="/api/websites",
    *     summary="Create new Website",
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 @OA\Property(
    *                     property="hostname",
    *                     type="string"
    *                 ),
    *                 example={"hostname": "https://kun.uz"}
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="OK",
    *         @OA\JsonContent(
    *             @OA\Examples(example="result", value={"hostname": "https://kun.uz"}, summary="An result object."),
    *         )
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="Website exists",
    *         @OA\JsonContent(
    *             @OA\Examples(example="result", value={"error": "website exists"}, summary="Validation error."),
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

        if (Website::where('hostname', $validated_data['hostname'])->exists()){
            return response()->json(['error'=>"website exists"], 400);
        }
        $newWebsite = new Website;
        $newWebsite->hostname = $validated_data['hostname'];
        
        if ($newWebsite->save() === false){
            return response()->json(['error'=>"Internal error: something went wrong, try again please"], 500);
        }

        return response()->json($newWebsite);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Website  $website
     * @return \Illuminate\Http\JsonResponse
     */

    /**
    * @OA\Get(
    *     path="/api/websites/{id}",
    *     summary="Get single post",
    *     @OA\Parameter(
    *         description="website id",
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
    *             @OA\Examples(example="result", value={"id": 1, "hostname": "https://kun.uz"}, summary="An result object."),
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Not found",
    *     ),
    * )
    */
    public function show(Website $website)
    {
        return response()->json($website);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Website  $website
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Website $website)
    {
        $validated_data = $this->getValidated($request);
        $website->hostname = $validated_data['hostname'];
        if($website->save() === false){
            return response()->json(['error'=>"Internal error: couldn't save changes, try again please"], 500);
        }
        return response()->json($website);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Website  $website
     * @return \Illuminate\Http\JsonResponse
     */

    /**
    * @OA\Delete(
    *     path="/api/websites/{id}",
    *     summary="Delete single website",
    *     @OA\Parameter(
    *         description="website id",
    *         in="path",
    *         name="id",
    *         required=true,
    *         @OA\Schema(type="integer"),
    *         @OA\Examples(example="int", value="1", summary="An int value."),
    *     ),
    *     @OA\Response(
    *         response=204,
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
    public function destroy(Website $website)
    {
        try{
            if ($website->delete() === false) {
                return response()->json(['error'=>"Internal error: something went wrong, try again please"], 500);
            }
            return response()->json([], 204);
        }catch(\LogicException $e){
            return response()->json(['error'=>"Internal error: something went wrong, try again please"], 500);
        }
    }

    private function getValidated(Request $request){
        return $request->validate([
            "hostname"=>"required|url"
        ]);
    }
}
