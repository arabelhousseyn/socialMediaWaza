<?php

namespace App\Http\Controllers;

use App\Models\AmanaCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Services\AmanaCategoryService;
class AmanaCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     private $amanaCategoryService;

     public function __construct()
     {
         $this->amanaCategoryService = new AmanaCategoryService();
     }
    
    public function index()
    {
        return response()->json(AmanaCategory::select('id','title','path','created_at')->get(), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:amana_categories',
            'image' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $path = '';
            $folderPath = "storage/app/amanaCategory/";
                    $image_base64 = base64_decode($request->image);
                    $path = uniqid() . '.jpg';
                    $file = $folderPath . $path;
                    file_put_contents($file, $image_base64);

            $category = AmanaCategory::create([
                'title' => $request->title,
                'path' => $path,
                'type' => 0,
            ]);
            return response()->json(['success' => true,"amana_category_id" => $category->id], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AmanaCategory  $amanaCategory
     * @return \Illuminate\Http\Response
     */
    public function show(AmanaCategory $amanaCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AmanaCategory  $amanaCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(AmanaCategory $amanaCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AmanaCategory  $amanaCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AmanaCategory $amanaCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AmanaCategory  $amanaCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(AmanaCategory $amanaCategory)
    {
        //
    }
}
