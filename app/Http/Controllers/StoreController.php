<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;


class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user_type = 'super_admin';

            // only admin can see all stores
            if ($user->tokenCan($user_type)) {
                return Store::all();
            }else {
                return response()->json(['message' => 'Unauthorized. Missing required permissions: Admin'], 403);
            }
        }else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }



    }

    /**
     * Display Info about the resources
     */

     public function get_store_data()
     {
        if (Auth::check()) {
            $user = Auth::user();

            // only admin can see all stores
            if ($user->tokenCan(RoleEnum::super_admin->value)) {
                return response()->json(['count' => count(Store::all())]);
            }else {
                return response()->json(['message' => 'Unauthorized. Missing required permissions: Admin'], 403);
            }
        }else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
     }

    /**
     * Display a view of the resource.
     */
    public function my_store()
    {

        // is user authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // is user a store_owner
            if ($user->tokenCan('store_owner')) {

                $user_store_registration_number = $user->store;

                // does user own any stores
                if ($user_store_registration_number) {

                    // Perform Query then stop immediately upon find
                    $user_store_object = Store::where('registration_number', $user_store_registration_number)->first();

                    return response()->json($user_store_object, 200);
                }else
                {
                    return response()->json(['store' => null], 404);
                }
            }else {
                return response()->json(['message' => 'Unauthorized. Missing required permissions: Store Owner'], 403);
            }
        }
        else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->tokenCan('admin')) {
            $request->validate([
                'name'=>'required|string|unique:stores|max:255',
                'location'=>'required',
            ]);

            $registration_number = 'keja' . uniqid();

            // check if registration number already exists
            while (Store::where('registration_number', $registration_number)->exists()) {
                $registration_number = 'keja' . uniqid();
            }

            // check if name already exists

            $store = new Store();
            $store->name = $request['name'];
            $store->location = $request['location'];
            $store->registration_number = $registration_number;


            $store->save();

            return response()->json($store, 201);
        }else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Get all system users that belong to the passed store
     */
    public function get_employees(Resuest $request)
    {
        /**
         * is user authenticated
         */
        if (Auth::check()) {
            $user = Auth::user();

            /**
             * is user a store_owner and does he have a store attached to him
             */
            if ($user->tokenCan('store_owner') && user->store) {

                /**
                 * does user's store match the request
                 */
                if ($user->store == $request->store_registration_number) {

                    /**
                     * Get all users of that store
                     */
                    $user_store_object = User::where('store', $user_store_registration_number);

                    return response()->json($user_store_object, 200);
                }else
                {
                    /**
                     * return null if no store is associated
                     */
                    return response()->json(['store' => null], 404);
                }
            }else {
                return response()->json(['message' => 'Unauthorized. Missing required permissions: Store Owner'], 403);
            }
        }
        else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
    }
    /**
     * Search for  resource from storage.
     * @param str $name
     * @return \Illuminate\Http\Response
     */
    public function search(string $store_name)
    {
        return Store::where('name', 'like', '%'.$store_name.'%')->get();
    }
}
