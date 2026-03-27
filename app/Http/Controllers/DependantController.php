<?php

namespace App\Http\Controllers;

use App\Models\Dependant;
// use Illuminate\Container\Attributes\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
class DependantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dependants = auth()->user()->dependants()->get();
        return response()->json($dependants);
    }

    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|min:0',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 
            'gender' => 'required|string|max:255',
            'school_name' => 'required|string|max:255',
            'grade' => 'required|string|max:255',
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validatedData['avatar'] = $avatarPath;
        }

        $dependant = auth()->user()->dependants()->create($validatedData);

        return response()->json($dependant, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Dependant $dependant)
    {
        if ($dependant->user_id !== auth()->id()) {
            return response()->json(['message'=>'unauthorized'], 403);
        }
        
        return response()->json($dependant);
        // return Dependant::where('user_id', auth()->id())->get();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dependant $dependant)
    {
        Gate::authorize('update', $dependant);


        $validatedData = $request->validate([
            'name'          => 'sometimes|required|string|max:255',
            'date_of_birth' => 'sometimes|required|date',
            'avatar'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 
            'gender'        => 'sometimes|required|string|max:255',
            'school_name'   => 'sometimes|required|string|max:255',
            'grade'         => 'sometimes|required|string|max:255',
        ]);

        if ($request->hasFile('avatar')) {
            // Optional: Delete old avatar file here if you want to save space
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validatedData['avatar'] = $avatarPath;
        }

        $dependant->update($validatedData);
        
        return response()->json($dependant);
    }

    public function updateAvatar(Request $request, Dependant $dependant)
    {

        Log::info('Avatar Upload Attempt:', $request->all());
        Log::info('Files:', $request->allFiles());

        Gate::authorize('update', $dependant);

        $request->validate ([
            'avatar' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($dependant->avatar && Storage::disk('public')->Arr::exists($dependant->avatar)) {
                Storage::disk('public')->delete($dependant->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');

            $dependant->update([
                'avatar' => $path
            ]);
        }
        return response()->json($dependant);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dependant $dependant)
    {
        Gate::authorize('delete', $dependant);
        $dependant->delete();
        return response()->json(null, 204);
    }
}
