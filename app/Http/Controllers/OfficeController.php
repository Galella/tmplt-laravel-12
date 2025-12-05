<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offices = Office::with(['parent', 'children', 'users'])->paginate(10);
        $officeTypes = ['headquarters' => 'Headquarters', 'regional' => 'Regional Office', 'area' => 'Area Office'];

        return view('offices.index', compact('offices', 'officeTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $offices = Office::orderBy('name')->get();
        $officeTypes = ['headquarters' => 'Headquarters', 'regional' => 'Regional Office', 'area' => 'Area Office'];
        $users = User::orderBy('name')->get();

        return view('offices.create', compact('offices', 'officeTypes', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:offices,code',
            'type' => 'required|in:headquarters,regional,area',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'parent_office_id' => 'nullable|exists:offices,id',
            'users' => 'array',
            'users.*.id' => 'exists:users,id',
            'users.*.position' => 'string|max:255',
            'users.*.is_primary' => 'boolean',
        ]);

        DB::transaction(function () use ($request) {
            $office = Office::create([
                'name' => $request->name,
                'code' => $request->code,
                'type' => $request->type,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'parent_office_id' => $request->parent_office_id,
                'is_active' => $request->has('is_active'),
            ]);

            // Attach users to the office if provided
            if ($request->has('users') && is_array($request->users)) {
                foreach ($request->users as $userAssignment) {
                    $office->users()->attach($userAssignment['id'], [
                        'position' => $userAssignment['position'] ?? null,
                        'is_primary' => $userAssignment['is_primary'] ?? false,
                        'assigned_date' => now(),
                        'is_active' => true
                    ]);
                }
            }
        });

        return redirect()->route('offices.index')->with('success', 'Office created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $office = Office::with(['parent', 'children', 'users'])->findOrFail($id);
        $officeTypes = ['headquarters' => 'Headquarters', 'regional' => 'Regional Office', 'area' => 'Area Office'];

        return view('offices.show', compact('office', 'officeTypes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $office = Office::with(['parent', 'users'])->findOrFail($id);
        $allOffices = Office::where('id', '!=', $id)->orderBy('name')->get();
        $officeTypes = ['headquarters' => 'Headquarters', 'regional' => 'Regional Office', 'area' => 'Area Office'];
        $users = User::orderBy('name')->get();

        // Get user assignments with positions and primary status
        $userAssignments = [];
        foreach ($office->users as $user) {
            $userAssignments[] = [
                'id' => $user->id,
                'name' => $user->name,
                'pivot_position' => $user->pivot->position,
                'pivot_is_primary' => $user->pivot->is_primary,
            ];
        }

        return view('offices.edit', compact('office', 'allOffices', 'officeTypes', 'users', 'userAssignments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $office = Office::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('offices')->ignore($office->id)
            ],
            'type' => 'required|in:headquarters,regional,area',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'parent_office_id' => 'nullable|exists:offices,id',
            'is_active' => 'boolean',
            'users' => 'array',
            'users.*.id' => 'exists:users,id',
            'users.*.position' => 'string|max:255',
            'users.*.is_primary' => 'boolean',
        ]);

        DB::transaction(function () use ($request, $office) {
            $office->update([
                'name' => $request->name,
                'code' => $request->code,
                'type' => $request->type,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'parent_office_id' => $request->parent_office_id,
                'is_active' => $request->has('is_active'),
            ]);

            // Sync users with positions and primary status
            if ($request->has('users') && is_array($request->users)) {
                $userSyncData = [];
                foreach ($request->users as $userAssignment) {
                    $userSyncData[$userAssignment['id']] = [
                        'position' => $userAssignment['position'] ?? null,
                        'is_primary' => $userAssignment['is_primary'] ?? false,
                        'assigned_date' => now(),
                        'is_active' => true
                    ];
                }

                $office->users()->sync($userSyncData);
            } else {
                // If no users provided, detach all users
                $office->users()->detach();
            }
        });

        return redirect()->route('offices.index')->with('success', 'Office updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $office = Office::findOrFail($id);

        // Check if office has child offices
        if ($office->children()->count() > 0) {
            return redirect()->route('offices.index')->with('error', 'Cannot delete office that has child offices.');
        }

        $office->delete();

        return redirect()->route('offices.index')->with('success', 'Office deleted successfully.');
    }
}
