<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function get_all_users (){
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated'
            ], 401);
        }


        if ($user->tokenCan('admin')) {
            return User::all();
        }else {
            return response()->json(['message' => 'Lacking required permissions: Admin'], 403);
        }
    }

    /**
     * Get the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_current_logged_in_user (){
        $user = Auth::user();

        if ($user) {
            $user_type = $user->user_type;

            // List allowed_urls for every role for frontend authentication
            $all_urls = [
                /**
                 * 'url' => 'allowed_role'
                 */
                'register_user'=> ['admin', 'store_owner'],
                'register_store'=> ['admin'],
                'logout'=> ['admin', 'project_manager', 'sales', 'marketing'],
                'login'=> ['admin', 'project_manager', 'sales', 'marketing'],
                'all_users'=> ['admin'],
            ];


            $my_allowed_urls = [];
            // $all_urls.foreach(u)


            return response()->json([
                'user' => $user,
                'user_type' => $user_type,
                'allowed_urls' => $my_allowed_urls
            ]);

        } else {
            return response()->json([
                'message' => 'User not authenticated'
            ], 401);
        }
    }

    public function logout(Request $request){

        $user = Auth::user();
    
        if ($user) {
            $user->tokens()->delete();
            return response()->json(['message' => 'Successfully logged out'], 200);
        } else {
            return response()->json(['message' => 'There is no logged-in user'], 404);
        }

    }

    public function login(Request $request) {

        $fields = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        
        try {
            // Check email
            $user = User::where('email', $fields['email'])->first();
        
            // Check if user exists and if password is correct
            if (!$user || !Hash::check($fields['password'], optional($user)->password)) {
                // Incorrect credentials
                return response(['message' => 'Bad Credentials'], 401);
            }
        
            // Create user token with abilities of the given user type
            $token = $user->createToken('kejamovetoken', [$user['user_type']])->plainTextToken;
        
            // Output
            $response = [
                'user' => $user,
                'token' => $token
            ];
        
            // Response 
            return response($response, 200);
        
        } catch (ValidationException $e) {
            // Handle validation errors
            return response()->json($e->errors(), 422);
        
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database query errors
            return response()->json(['message' => 'Database error occurred', 'error' => $e->getMessage()], 500);
        
        } catch (\Illuminate\Auth\AuthenticationException $e) {
            // Handle authentication errors
            return response()->json(['message' => 'Authentication failed', 'error' => $e->getMessage()], 401);
        
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['message' => 'Unexpected error occurred', 'error' => $e->getMessage()], 500);
        }
        


    
    }

   
    
    public function create_move(Request $request)
    {
        $user = Auth::user();

        // Check if the user is authorized to create a move
        // You can customize the authorization logic based on your requirements
        if ($user->can('store_owner' || 'admin' || 'sales')) {

            // Validate incoming request data   
            $fields = $request->validate([
                'lead_source' => 'required|string',
                'consumer_name' => 'nullable|string',
                'corporate_name' => 'nullable|string',
                'contact_information' => 'required|string',
                'moving_from' => 'required|string',
                'moving_to' => 'required|string',
                'sales_representative' => 'nullable|exists:users,id',
                'store' => 'required|exists:stores,id',
                'invoiced_amount' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);

            try {
                // Create the move
                $move = Move::create($fields);

                // Validate move creation
                if (!$move) {
                    return response()->json(['error' => 'Failed to create move.'], 500);
                }

                // Return successful response
                return response()->json($move, 201);

            } catch (ValidationException $e) {
                // Handle validation errors
                return response()->json(['error' => 'Validation failed.', 'details' => $e->errors()], 422);
            
            } catch (\Exception $e) {
                // Handle other exceptions
                return response()->json(['error' => 'Server error.', 'details' => $e->getMessage()], 500);
            
            }

        } else {
            return response()->json(['error' => 'Unauthorized.', 'message' => 'You are not authorized to create a move.'], 403);
        }
    }

        
}
