<?php

namespace App\Http\Controllers;

use App\Http\Requests\Property\CreatePropertyRequest;
use App\Http\Requests\Property\TransferPropertyRequest;
use App\Models\Property;
use App\Models\PropertyCurrentUser;
use App\Models\PropertyUserHistory;
use App\Models\User;
use App\NotifiableTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PropertyController extends Controller
{
    use NotifiableTrait;

    public function list(Request $request):JsonResponse
    {
        $page = $request->page ?? 1;
        $perPage = $request->perPage ?? 10;
        $searchKeyword = trim($request->searchKeyword ?? '');
        $user_id = $request->user_id ?? null;

        $propertyEloquent = Property::with([
                    'currentUser.user',
                    'firstUser'
                    ])
                    // ->where('status','Active')
                    ->when($user_id, function ($query) use ($user_id) {
                        $query->whereHas('currentUser', function ($query) use ($user_id) {
                            $query->where('user_id', $user_id);
                        });
                    })
                    ->when($searchKeyword, function ($query) use ($searchKeyword) {
                        $query->where('property_no', 'like', '%' . $searchKeyword . '%')
                                ->orWhere('particulars', 'like', '%' . $searchKeyword . '%');
                    })
                    ->orderBy('id','DESC');

        $total = $propertyEloquent->count();  
        $properties = $propertyEloquent->offset(($page - 1) * $perPage)->limit($perPage)->get();  

        return response()->json([
            'list' => $properties,
            'total' => $total
        ]);
    }

    public function create(CreatePropertyRequest $request):JsonResponse
    {
        $validated = $request->validated();

        $property = Property::create([
            'property_no' => $validated['property_no'],
            'measurement_unit' => $validated['measurement_unit'],
            'particulars' => $validated['particulars'],
            'unit_cost' => $validated['unit_cost'],
            'status' => 'Active',
            'remarks' => $validated['remarks'] ?? null,
            'main_category_id' => $validated['main_category_id']
        ]);

        PropertyCurrentUser::create([
            'property_id' => $property->id,
            'user_id' => $validated['end_user'],
            'issuance_date' => $validated['acquisition_date'],
        ]);

        PropertyUserHistory::create([
            'property_id' => $property->id,
            'user_id' => $validated['end_user'],
            'acquisition_date' => $validated['acquisition_date'],
            'return_date' => null,
            'remarks' => null
        ]);

        $this->createNotification([
            'notifiable_id' => $property->id,
            'notifiable_class' => Property::class,
            'path' => '/admin/properties/user',
            'marked_read' => 0,
            'message' => "New property added to your name.",
            'user_id' => $validated['end_user'],
            'section_id' => null,
            'module' => 'Property'
        ]);

        return response()->json([
            'message' => 'Property Successfully Created'
        ]);

    }

    public function findByPropertyNo(Request $request):JsonResponse
    {
        $property = Property::with(['firstUser','currentUser.user'])->where('property_no',$request->property_no)->first();

        if($property){
            return response()->json([
                'status' => true,
                'property' => $property
            ]);
        }
        else{
            return response()->json([
                'status' => false,
            ]);
        }
    }

    public function transfer(TransferPropertyRequest $request):JsonResponse
    {
        $validated = $request->validated();

        foreach($validated['properties'] as $property){
            $property = Property::find($property['id'])->currentUser()->update([
                'user_id' => $validated['end_user'],
                'issuance_date' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Properties have been transfered to User.'
        ]);
    }

    public function fetchUserProperties(Request $request):JsonResponse
    {
        $users = User::with([
                'assignment.item',
                'properties' => function($query) {
                    $query->whereHas('property', function($q) {
                        $q->where('status', 'active');
                    });
                },
                'properties.property.firstUser',
                'properties.property.measurementUnit'
            ])->findMany($request->users);

        return response()->json([
            'users' => $users
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:lims.lims_properties,id',
            'property_no' => ['required','string',Rule::unique('lims.lims_properties', 'property_no')->ignore($request->id, 'id')],
            'measurement_unit' => 'numeric|exists:lims_measurements:id',
            'particulars' => 'required|string',
            'unit_cost' => 'required|numeric',
            'status' => 'required|string',
            'remarks' => 'nullable|string',
            'main_category_id' => 'nullable|numeric'
        ]);

        Property::find($validated['id'])->update($validated);

        return response()->json('Updated Successfully');
    }




}
