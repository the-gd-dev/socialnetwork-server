<?php

namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reaction;
use App\Models\Privacy;
use App\Models\Language;
class UtilitiesController extends Controller
{
    /**
     * @return reactions list
     */
    public function getReactions(Request $request){
        $reactions = Reaction::all();
        foreach ($reactions as  $value) {
            $value->selected = false;
        }
        return response()->json(['reactions' => $reactions], 200);
    }
    
    /**
     * @return privacies list
     */
    public function getPrivacies(Request $request){
        $privacies = Privacy::all();
        return response()->json(['privacies' => $privacies], 200);
    }

    /**
     * @param country
     * @return languages list
     */
    public function getLanguages(Request $request){
        $query = Language::orderBy('language');
        $languages = [];
        if($request->Has('country')){
            $country = $request->country;
            $query  = $query->where('country',  $country);
            $languages = $query->get();
        }
        return response()->json(['languages' =>  $languages], 200);
    }
}