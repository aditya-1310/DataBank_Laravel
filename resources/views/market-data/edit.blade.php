<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Edit Market Data') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-white">
                    <div class="mb-6">
                        <a href="{{ route('market-data.show', $marketData->id) }}" class="text-blue-400 hover:text-blue-300">
                            &larr; Back to Details
                        </a>
                    </div>

                    @if(!$marketData->is_approved && !auth()->user()->is_admin)
                        <div class="bg-yellow-800 border-l-4 border-yellow-500 text-yellow-100 p-4 mb-6" role="alert">
                            <p class="font-bold">Pending Approval</p>
                            <p>Editing this item will reset its approval status. It will need to be approved again by an administrator.</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('market-data.update', $marketData->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div>
                            <x-input-label for="title" :value="__('Title')" class="text-white" />
                            <x-text-input id="title" class="block mt-1 w-full bg-gray-700 text-white border-gray-600 placeholder-gray-400" type="text" name="title" :value="old('title', $marketData->title)" required autofocus placeholder="Enter title" />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Product Name -->
                        <div>
                            <x-input-label for="product_name" :value="__('Product Name')" class="text-white" />
                            <x-text-input id="product_name" class="block mt-1 w-full bg-gray-700 text-white border-gray-600 placeholder-gray-400" type="text" name="product_name" :value="old('product_name', $marketData->product_name)" required placeholder="Enter product name" />
                            <x-input-error :messages="$errors->get('product_name')" class="mt-2" />
                        </div>

                        <!-- Price -->
                        <div>
                            <x-input-label for="price" :value="__('Price')" class="text-white" />
                            <x-text-input id="price" class="block mt-1 w-full bg-gray-700 text-white border-gray-600 placeholder-gray-400" type="number" name="price" :value="old('price', $marketData->price)" step="0.01" min="0" required placeholder="0.00" />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- Location -->
                        <div>
                            <x-input-label for="location" :value="__('Location')" class="text-white" />
                            <x-text-input id="location" class="block mt-1 w-full bg-gray-700 text-white border-gray-600 placeholder-gray-400" type="text" name="location" :value="old('location', $marketData->location)" required placeholder="Enter location" />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div>
                            <x-input-label for="category" :value="__('Category')" class="text-white" />
                            <x-text-input id="category" class="block mt-1 w-full bg-gray-700 text-white border-gray-600 placeholder-gray-400" type="text" name="category" :value="old('category', $marketData->category)" required placeholder="Enter category" />
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" class="text-white" />
                            <textarea id="description" name="description" rows="4" class="bg-gray-700 text-white border-gray-600 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block mt-1 w-full placeholder-gray-400" placeholder="Enter description (optional)">{{ old('description', $marketData->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Additional Info (JSON) -->
                        <div>
                            <x-input-label for="additional_info" :value="__('Additional Info (JSON format)')" class="text-white" />
                            <textarea id="additional_info" name="additional_info" rows="4" class="bg-gray-700 text-white border-gray-600 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block mt-1 w-full placeholder-gray-400" placeholder='{"key": "value"}'>{{ old('additional_info', is_array($marketData->additional_info) ? json_encode($marketData->additional_info, JSON_PRETTY_PRINT) : $marketData->additional_info) }}</textarea>
                            <p class="text-sm text-gray-400 mt-1">Example: {"manufacturer": "ABC Corp", "year": 2023, "attributes": ["waterproof", "durable"]}</p>
                            <x-input-error :messages="$errors->get('additional_info')" class="mt-2" />
                        </div>

                        @if(auth()->user()->is_admin)
                            <!-- Approval Status (Admin only) -->
                            <div class="mt-4">
                                <label for="is_approved" class="inline-flex items-center">
                                    <input id="is_approved" type="checkbox" class="rounded border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500 bg-gray-700" name="is_approved" value="1" {{ $marketData->is_approved ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-300">{{ __('Approved') }}</span>
                                </label>
                            </div>
                        @endif

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('market-data.show', $marketData->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-700 border border-gray-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button class="ml-4 bg-blue-600 hover:bg-blue-500">
                                {{ __('Update') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
    /* Add custom styles to ensure placeholders are visible */
    ::placeholder {
        color: rgba(156, 163, 175, 0.8) !important; /* gray-400 with higher opacity */
        opacity: 1 !important;
    }
    </style>
</x-app-layout> 