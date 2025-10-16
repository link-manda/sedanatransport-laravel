<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehiclePageController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::where('status', 'available')->latest()->paginate(9);
        return view('vehicles.index', compact('vehicles'));
    }

    public function show(Vehicle $vehicle)
    {
        return view('vehicles.show', compact('vehicle'));
    }
}
