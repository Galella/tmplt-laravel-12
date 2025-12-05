<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles', 'office')->paginate(10);
        $roles = Role::all();
        $offices = \App\Models\Office::orderBy('name')->get();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $users,
                'roles' => $roles,
                'offices' => $offices
            ]);
        }

        return view('users.index', compact('users', 'roles', 'offices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $offices = \App\Models\Office::orderBy('name')->get();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('users.create-modal', compact('roles', 'offices'))->render()
            ]);
        }

        return view('users.create', compact('roles', 'offices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'roles' => 'array',
                'office_id' => 'nullable|exists:offices,id',
            ]);

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ];

            // Add office_id to user if provided
            if ($request->has('office_id')) {
                $userData['office_id'] = $request->office_id;
            }

            $user = User::create($userData);

            if ($request->has('roles')) {
                $user->roles()->attach($request->roles);
            }

            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'data' => $user->load('roles', 'office')
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'array',
            'office_id' => 'nullable|exists:offices,id',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];

        // Add office_id to user if provided
        if ($request->has('office_id')) {
            $userData['office_id'] = $request->office_id;
        }

        $user = User::create($userData);

        if ($request->has('roles')) {
            $user->roles()->attach($request->roles);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::with('roles', 'office')->findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        }

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::with('roles', 'office')->findOrFail($id);
        $roles = Role::all();
        $offices = \App\Models\Office::orderBy('name')->get();
        $userRoleIds = $user->roles->pluck('id')->toArray();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('users.edit-modal', compact('user', 'roles', 'offices', 'userRoleIds'))->render()
            ]);
        }

        return view('users.edit', compact('user', 'roles', 'offices', 'userRoleIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::with('roles')->findOrFail($id);

        if ($request->ajax()) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->ignore($user->id)
                ],
                'password' => 'nullable|string|min:8|confirmed',
                'roles' => 'array',
                'office_id' => 'nullable|exists:offices,id',
            ]);

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            // Add office_id to update if provided
            if ($request->has('office_id')) {
                $updateData['office_id'] = $request->office_id;
            }

            $user->update($updateData);

            // Update password if provided
            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            // Sync roles
            if ($request->has('roles')) {
                $user->roles()->sync($request->roles);
            } else {
                $user->roles()->detach();
            }

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'data' => $user->load('roles', 'office')
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'array',
            'office_id' => 'nullable|exists:offices,id',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Add office_id to update if provided
        if ($request->has('office_id')) {
            $updateData['office_id'] = $request->office_id;
        }

        $user->update($updateData);

        // Update password if provided
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Sync roles
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        } else {
            $user->roles()->detach();
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account.'
                ]);
            }
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Get all users for AJAX requests.
     */
    public function apiIndex()
    {
        $users = User::with('roles')->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
}
