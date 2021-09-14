<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\ListingImage;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'title' => 'required|max:255',
            'category_id' => 'required',
            'description' => 'required',
            'price' => 'required',
            'status' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'images' => 'required',
            'area' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['success' => false], 200);
        }

        if($validator->validated())
        {
            $listing = Listing::create([
                'category_id' => $request->category_id,
                'user_id' => Auth::user()->id,
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'status' => $request->status,
                'lat' => $request->lat,
                'long' => $request->long,
                'area' => $request->area,

            ]);

            $images = explode(';ibaa;',$request->images);
                   foreach ($images as $image) {
                    $pathImage = uniqid() . '.jpg';
                    $folderPathImage = "storage/app/ListingImages/";
                    $image_base64 = base64_decode($image);
                    $file = $folderPathImage . $pathImage;
                    file_put_contents($file, $image_base64);
                           $check = ListingImage::create([
                               'path' => $pathImage,
                               'listing_id' => $listing->id,
                           ]);
                   }

                   return response()->json(['success' => true], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Listing::with('user','images')->find($id);

         return response()->json(['success' => true,'data' => $data], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function edit(Listing $listing)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Listing $listing)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function destroy(Listing $listing)
    {
        //
    }

    public function listingByCategory($id,$pos)
    {
        $importantData = DB::table('useful_data')->where('idname','KM')->first();

        $lat = doubleval(explode(';',$pos)[0]);
        $long = doubleval(explode(';',$pos)[1]);

        if($id == 0)
        {
            $listings = Listing::select('id','title','price','lat','long','area')->orderBy('id','DESC')->paginate(20);

            foreach ($listings as $listing) {
                $images = ListingImage::where('listing_id',$listing['id'])->first();
                $listing['image'] = $images->path;
            }
            $listings = $listings->toArray();
            $i = 0;
            foreach ($listings['data'] as $listing) {
               $distance = $this->distance($lat,$long,$listing['lat'],$listing['long']);
              if(intval($distance) > intval($importantData->data))
              {
                unset($listings['data'][$i]);
              }
              $i++;   
            }
            $listings['data'] = array_values($listings['data']);
            return response()->json($listings, 200);
        }
            $listings = Listing::select('id','title','price','lat','long','area')->where('category_id',$id)->orderBy('id','DESC')->paginate(20);
            
            foreach ($listings as $listing) {
                $images = ListingImage::where('listing_id',$listing['id'])->first();
                $listing['image'] = $images->path;
            }
            $listings = $listings->toArray();
            $i = 0;
            foreach ($listings['data'] as $listing) {
               $distance = $this->distance($lat,$long,$listing['lat'],$listing['long']);
              if(intval($distance) > intval($importantData->data))
              {
                unset($listings['data'][$i]);
              }
              $i++;   
            }
            $listings['data'] = array_values($listings['data']);
            return response()->json($listings, 200);
    }

    private function distance($latitude1, $longitude1, $latitude2, $longitude2) {
        $theta = $longitude1 - $longitude2; 
        $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta))); 
        $distance = acos($distance); 
        $distance = rad2deg($distance); 
        $distance = $distance * 60 * 1.1515; 
        $distance = $distance * 1.609344; 
        return (round($distance,2)); 
      }
}
