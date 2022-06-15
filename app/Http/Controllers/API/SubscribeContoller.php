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
