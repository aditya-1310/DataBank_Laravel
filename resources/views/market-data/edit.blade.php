<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Market Data') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('market-data.show', $marketData->id) }}" class="text-indigo-600 hover:text-indigo-900">
                            &larr; Back to Details
                        </a>
                    </div>

                    @if(!$marketData->is_approved && !auth()->user()->is_admin)
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                            <p class="font-bold">Pending Approval</p>
                            <p>Editing this item will reset its approval status. It will need to be approved again by an administrator.</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('market-data.update', $marketData->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $marketData->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Product Name -->
                        <div>
                            <x-input-label for="product_name" :value="__('Product Name')" />
                            <x-text-input id="product_name" class="block mt-1 w-full" type="text" name="product_name" :value="old('product_name', $marketData->product_name)" required />
                            <x-input-error :messages="$errors->get('product_name')" class="mt-2" />
                        </div>

                        <!-- Price -->
                        <div>
                            <x-input-label for="price" :value="__('Price')" />
                            <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="old('price', $marketData->price)" step="0.01" min="0" required />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- Location -->
                        <div>
                            <x-input-label for="location" :value="__('Location')" />
                            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location', $marketData->location)" required />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div>
                            <x-input-label for="category" :value="__('Category')" />
                            <x-text-input id="category" class="block mt-1 w-full" type="text" name="category" :value="old('category', $marketData->category)" required />
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="4" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('description', $marketData->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Additional Info (JSON) -->
                        <div>
                            <x-input-label for="additional_info" :value="__('Additional Info (JSON format)')" />
                            <textarea id="additional_info" name="additional_info" rows="4" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('additional_info', is_array($marketData->additional_info) ? json_encode($marketData->additional_info, JSON_PRETTY_PRINT) : $marketData->additional_info) }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Example: {"manufacturer": "ABC Corp", "year": 2023, "attributes": ["waterproof", "durable"]}</p>
                            <x-input-error :messages="$errors->get('additional_info')" class="mt-2" />
                        </div>

                        @if(auth()->user()->is_admin)
                            <!-- Approval Status (Admin only) -->
                            <div class="mt-4">
                                <label for="is_approved" class="inline-flex items-center">
                                    <input id="is_approved" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_approved" value="1" {{ $marketData->is_approved ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600">{{ __('Approved') }}</span>
                                </label>
                            </div>
                        @endif

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('market-data.show', $marketData->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button class="ml-4">
                                {{ __('Update') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 