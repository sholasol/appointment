<?php

namespace App\Http\Controllers\Admin;

use App\Models\Settings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function settings(){
       return  Settings::pluck('value', 'key')->toArray();
    }

    // public function update(){

    //     // $settings = request()->validate([
    //     //     'app_name' => ['required', 'string'],
    //     //     'date_format' => ['required', 'string'],
    //     //     'pagination_limit' => ['required', 'int', 'min:1', 'max:100'],
    //     // ]);
        
    // $settings = request()->all();

    //    foreach($settings as $key => $value){
    //         Settings::updateOrCreate(
    //             ['key' => $key],
    //             ['value' => $value],
    //         );
    //     //  Settings::where('key', $key)->update(['value' => $value]); 
    //     }
       

    //    return response()->json(['success' => true]);
    // }


    public function update(){

        $settings = request()->validate([
             'app_name' => ['required', 'string'],
             'date_format' => ['required', 'string'],
             'pagination_limit' => ['required', 'int', 'min:1', 'max:100'],
         ]);

        if ($settings->fails()) {
            return $settings->errors();
        }

        foreach($settings as $key => $value){
            Settings::updateOrCreate(
                ['key' => $key],
                ['value' => $value],
            );
        }
       

       return response()->json(['success' => true]);
    }
}