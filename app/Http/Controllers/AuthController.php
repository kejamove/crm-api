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

        $userCount = User::count();
        if ($userCount < 1) {
            return "There are $userCount users in the database.";
        }

        
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
            return response()->json([
                'errors' => [
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors()
                ]
            ], 422);
        } catch (QueryException $e) {
            // Handle database query errors
            return response()->json([
                'message' => 'Database error occurred',
                'error' => $e->getMessage()
            ], 500);
        } catch (AuthenticationException $e) {
            // Handle authentication errors
            return response()->json([
                'message' => 'Authentication failed',
                'error' => $e->getMessage()
            ], 401);
        } catch (AuthorizationException $e) {
            // Handle authorization errors
            return response()->json([
                'message' => 'Authorization failed',
                'error' => $e->getMessage()
            ], 403);
        } catch (ModelNotFoundException $e) {
            // Handle model not found errors
            return response()->json([
                'message' => 'Resource not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (HttpException $e) {
            // Handle HTTP errors
            return response()->json([
                'message' => 'HTTP error occurred',
                'error' => $e->getMessage()
            ], $e->getStatusCode());
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json([
                'message' => 'Unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
        


    
    }


    public function register_user(Request $request) {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'message' => 'You must be logged in to perform this action'
            ], 401);
        }

        // Get the authenticated user
        $user = Auth::user();
        $requiredFields = [
            'email',
            'password',
            'user_type',
            'first_name',
            'last_name',
        ];

        // Validate request fields
        $fields = $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
        ]);

        // Check if the authenticated user is an admin
        if ($fields['user_type'] == 'admin'){
            if (!$user->tokenCan('admin')) {
                return response()->json([
                    'message' => 'Only admin users can create another admin user'
                ], 403);
            }
        }

        $phone_number_full = $request->phone_country_code . $request->phone_local_number;

        try {
        
            // Create user token with abilities of the given user type
            // Generate token
            $newUser = User::create(array_merge($request->all(), ['phone_number_full' => $phone_number_full]));

            // Generate token
            $token = $newUser->createToken('kejamovetoken', [$newUser->user_type])->plainTextToken;

            // Update user with token
            $newUser->update(['remember_token' => $token]);


            // Output
            $response = [
                'user' => $newUser,
                'token' => $token
            ];

            // Response 
            return response($response, 201);

        } catch (ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'errors' => [
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors()
                ]
            ], 422);
        } catch (QueryException $e) {
            // Handle database query errors
            return response()->json([
                'message' => 'Database error occurred',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json([
                'message' => 'Unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    

     /**
     * Display Info about the resources
     */

     public function get_user_data() 
     {
        if (Auth::check()) {
            $user = Auth::user();

            // only admin can see all stores
            if ($user->tokenCan('admin')) {
                return response()->json(['count' => count(User::all())]);
            }else {
                return response()->json(['message' => 'Unauthorized. Missing required permissions: Admin'], 403);
            }
        }else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
     }
        
}
