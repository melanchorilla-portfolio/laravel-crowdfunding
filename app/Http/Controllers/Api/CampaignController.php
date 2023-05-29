<?php

namespace App\Http\Controllers\Api;

use App\Models\Campaign;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campaigns = Campaign::latest()->get();

        $data['campaigns'] = $campaigns;

        return response()->json([
            'response_code' => 200,
            'response_message' => 'List Campaign',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // set validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:campaigns|max:255',
            'description' => 'required',
            'address' => 'required',
            'image' => 'mimes:jpg,jpeg,png',
            'required' => 'required|integer',
            'collected' => 'required|integer',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_extension = $image->extension();
            $image_name = time() . '.' . $image_extension;
            $image_folder = '/photo/campaign/';
            $image_location = $image_folder . $image_name;
            $request->image->move(public_path($image_folder), $image_name);
        }

        // save
        $campaign = Campaign::create([
            'title' => $request->title,
            'description' => $request->description,
            'address' => $request->address,
            'image' => $image_location ?? null,
            'required' => $request->required,
            'collected' => $request->collected,
        ]);

        if ($campaign) {
            return response()->json([
                'response_code' => 201,
                'response_message' => 'Campaign data created!'
            ], 201);
        }

        // save failed
        return response()->json([
            'response_code' => 409,
            'response_message' => 'Campaign data failed to save!'
        ], 409);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Campaign $campaign)
    {
        $campaign_data = Campaign::findOrFail($campaign->id);

        //make response JSON
        return response()->json([
            'response_code' => 200,
            'response_message' => 'Detail Data Campaign',
            'data'    => $campaign_data
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Campaign $campaign)
    {
        // set validation
        $validator = Validator::make($request->all(), [
            'title' => [
                'required',
                Rule::unique('campaigns')->ignore($campaign->id)
            ],
            'description' => 'required',
            'address' => 'required',
            'image' => 'mimes:jpg,jpeg,png',
            'required' => 'required|integer',
            'collected' => 'required|integer',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $campaign_data = Campaign::findOrFail($campaign->id);

        if ($campaign_data) {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $image_extension = $image->extension();
                $image_name = time() . '.' . $image_extension;
                $image_folder = '/photo/campaign/';
                $image_location = $image_folder . $image_name;
                $request->image->move(public_path($image_folder), $image_name);
                if ($campaign_data->image != null) {
                    // delete image after update
                    unlink(public_path($campaign_data->image));
                }
            }

            $campaign_data->update([
                'title' => $request->title,
                'description' => $request->description,
                'address' => $request->address,
                'image' => $image_location ?? $campaign_data->image,
                'required' => $request->required,
                'collected' => $request->collected,
            ]);

            return response()->json([
                'response_code' => true,
                'response_message' => 'Campaign data updated',
                'data'    => $campaign_data
            ], 200);
        }

        // update failed
        return response()->json([
            'response_code' => 409,
            'response_message' => 'Campaign data failed to update!'
        ], 409);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Campaign $campaign)
    {
        $campaign_data = Campaign::findOrFail($campaign->id);

        if ($campaign_data) {
            // delete image
            if ($campaign_data->image) {
                unlink(public_path($campaign_data->image));
            }

            // delete campaign data
            $campaign_data->delete();

            return response()->json([
                'response_code' => 200,
                'response_message' => 'Cammpaign data deleted',
            ], 200);
        }

        //data post not found
        return response()->json([
            'response_code' => 404,
            'response_message' => 'Campaign Not Found',
        ], 404);
    }
}
