<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('category')->latest()->paginate(10);
        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.vehicles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'plate_number' => 'required|string|max:15|unique:vehicles,plate_number',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'daily_rate' => 'required|numeric|min:0',
            'status' => 'required|in:available,rented,maintenance',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('vehicle-photos', 'public');
            $validated['photo'] = $path;
        }

        Vehicle::create($validated);

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle created successfully.');
    }

    public function show(Vehicle $vehicle)
    {
        return view('admin.vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $categories = Category::all();
        return view('admin.vehicles.edit', compact('vehicle', 'categories'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'plate_number' => 'required|string|max:15|unique:vehicles,plate_number,' . $vehicle->id,
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'daily_rate' => 'required|numeric|min:0',
            'status' => 'required|in:available,rented,maintenance',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($vehicle->photo) {
                Storage::disk('public')->delete($vehicle->photo);
            }
            $path = $request->file('photo')->store('vehicle-photos', 'public');
            $validated['photo'] = $path;
        }

        $vehicle->update($validated);

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->photo) {
            Storage::disk('public')->delete($vehicle->photo);
        }
        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }
}
