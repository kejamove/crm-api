<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->tokenCan('admin')) {
            $branches = Branch::all();
            return response()->json($branches, 200);
        }

        abort(403, 'Unauthorized access!');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->tokenCan('store_owner')) {
            $request->validate([
                'name'=>'required|string|unique:stores|max:255',
            ]);

            $firm = $user->firm;

            // Ensure the firm exists
            if (!$firm) {
                return response()->json(['error' => 'Firm not found'], 404);
            }

            $branchData = [
                'name' => $request->get('name'),
                'firm' => $firm,
            ];

            $branch = new Branch($branchData);
            $branch->save();

            return response()->json($branch, 201);
        }else if ($user->tokenCan('admin')) {
            $request->validate([
                'name'=>'required|string|unique:stores|max:255',
                'firm'=>'required|int',
            ]);

            $branchData = [
                'name' => $request->get('name'),
                'firm' => $request->get('firm'),
            ];

            $branch = new Branch($branchData);
            $branch->save();
            return response()->json($branch, 201);
        }
        else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();

        if ($user->tokenCan('admin') || $user->tokenCan('store_owner')) {
            return Branch::Find($id);
        }

        abort(403, 'Unauthorized access!');
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
}
