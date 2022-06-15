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
