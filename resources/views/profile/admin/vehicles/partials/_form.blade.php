<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Plate Number -->
    <div>
        <x-input-label for="plate_number" :value="__('Plate Number')" />
        <x-text-input id="plate_number" class="block mt-1 w-full" type="text" name="plate_number" :value="old('plate_number', $vehicle->plate_number ?? '')" required autofocus />
    </div>

    <!-- Category -->
    <div>
        <x-input-label for="category_id" :value="__('Category')" />
        <select name="category_id" id="category_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $vehicle->category_id ?? '') == $category->id)>
                {{ $category->name }}
            </option>
            @endforeach
        </select>
    </div>

    <!-- Brand -->
    <div>
        <x-input-label for="brand" :value="__('Brand')" />
        <x-text-input id="brand" class="block mt-1 w-full" type="text" name="brand" :value="old('brand', $vehicle->brand ?? '')" required />
    </div>

    <!-- Model -->
    <div>
        <x-input-label for="model" :value="__('Model')" />
        <x-text-input id="model" class="block mt-1 w-full" type="text" name="model" :value="old('model', $vehicle->model ?? '')" required />
    </div>

    <!-- Year -->
    <div>
        <x-input-label for="year" :value="__('Year')" />
        <x-text-input id="year" class="block mt-1 w-full" type="number" name="year" :value="old('year', $vehicle->year ?? '')" required />
    </div>

    <!-- Daily Rate -->
    <div>
        <x-input-label for="daily_rate" :value="__('Daily Rate (Rp)')" />
        <x-text-input id="daily_rate" class="block mt-1 w-full" type="number" name="daily_rate" step="1000" :value="old('daily_rate', $vehicle->daily_rate ?? '')" required />
    </div>

    <!-- Status -->
    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select name="status" id="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            <option value="available" @selected(old('status', $vehicle->status ?? '') == 'available')>Available</option>
            <option value="rented" @selected(old('status', $vehicle->status ?? '') == 'rented')>Rented</option>
            <option value="maintenance" @selected(old('status', $vehicle->status ?? '') == 'maintenance')>Maintenance</option>
        </select>
    </div>

    <!-- Photo -->
    <div>
        <x-input-label for="photo" :value="__('Photo')" />
        <input id="photo" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" name="photo">
        @if(isset($vehicle) && $vehicle->photo)
        <div class="mt-2">
            <img src="{{ asset('storage/' . $vehicle->photo) }}" alt="Current Photo" class="h-24 w-auto rounded">
            <p class="text-xs text-gray-500 mt-1">Current photo. Uploading a new one will replace it.</p>
        </div>
        @endif
    </div>
</div>