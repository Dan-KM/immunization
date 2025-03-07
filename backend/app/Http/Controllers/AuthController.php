<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * User Register
     */
    public function register(Request $request)
    {
        $user = auth()->guard()->user();

        if (!$user || !in_array($user->role, ['admin', 'nurse'])) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            $fields = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'nullable|email|unique:users,email',
                'password' => 'nullable|min:8|confirmed',
                'phone_number' => 'required|string',
                'gender' => 'required|string',
                'role' => 'required|string',
                'nationality' => 'required|string',
                'national_id' => 'required|integer|unique:users,national_id',
                'date_of_birth' => 'required|date',
                'address' => 'required|string',
                'marital_status' => 'required|string',
                'next_of_kin' => 'required|string',
                'next_of_kin_contact' => 'required|string',
                'no_of_children' => 'required|integer',
            ]);

            // Set password: If not provided, use national_id as default password
            $password = $fields['password'] ?? (string) $fields['national_id'];

            //Create a user
            $user = User::create([
                'first_name'=> $fields['first_name'],
                'last_name'=> $fields['last_name'],
                'email' => $fields['email'],
                'password'=> Hash::make($password),
                'phone_number' => $fields['phone_number'],
                'gender'=> $fields['gender'],
                'role'=> $fields['role'],
                'nationality'=> $fields['nationality'],
                'national_id'=> $fields['national_id'],
                'date_of_birth'=> $fields['date_of_birth'],
                'address'=> $fields['address'],
                'marital_status'=> $fields['marital_status'],
                'next_of_kin'=> $fields['next_of_kin'],
                'next_of_kin_contact'=> $fields['next_of_kin_contact'],
                'no_of_children'=> $fields['no_of_children'],
            ]);

            $token = $user->createToken($fields['first_name']);

            return response()->json([
                'message' => 'User registered successfully',
                'user' => (new UserResource($user))->toArray($request),
                'token'=> $token->plainTextToken,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * User Login
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'password' => 'required',
                'email_or_phone_number' => 'required_without_all:email,phone_number',
            ], [
                'email_or_phone_number.required_without_all' => 'Either email or phone number is required.'
            ]);

            // Check if email is provided
            if ($request->has('email')) {
                $user = User::where('email', $request->email)->first();
            } 
            // Otherwise check phone number
            else if ($request->has('phone_number')) {
                $user = User::where('phone_number', $request->phone_number)->first();
            }
            else {
                return response()->json([
                    'message'=> 'Please provide either email or phone number'
                ], 400);
            }

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Invalid credentials',
                    'errors' => [
                        'email_or_phone_number' => ['The provided credentials are incorrect.']
                    ],
                ], 401);
            }

            $token = $user->createToken($user->first_name);

            return response()->json([
                'message'=> 'Logged in successfully',
                'user' => (new UserResource($user))->toArray($request),
                'token'=> $token->plainTextToken,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * User Logout
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'message' => 'You are logged out.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}