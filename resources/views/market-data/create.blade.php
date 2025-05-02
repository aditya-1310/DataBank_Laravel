<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Submit New Market Data') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('market-data.store') }}" class="space-y-6">
                        @csrf

                        <!-- Title -->
                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Date -->
                        <div>
                            <x-input-label for="date" :value="__('Date')" />
                            <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="old('date', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                        </div>

                        <!-- Symbol -->
                        <div>
                            <x-input-label for="symbol" :value="__('Symbol')" />
                            <x-text-input id="symbol" class="block mt-1 w-full" type="text" name="symbol" :value="old('symbol')" required />
                            <x-input-error :messages="$errors->get('symbol')" class="mt-2" />
                        </div>

                        <!-- Open -->
                        <div>
                            <x-input-label for="open" :value="__('Open Price')" />
                            <x-text-input id="open" class="block mt-1 w-full" type="number" name="open" :value="old('open')" step="0.01" min="0" required />
                            <x-input-error :messages="$errors->get('open')" class="mt-2" />
                        </div>

                        <!-- High -->
                        <div>
                            <x-input-label for="high" :value="__('High Price')" />
                            <x-text-input id="high" class="block mt-1 w-full" type="number" name="high" :value="old('high')" step="0.01" min="0" required />
                            <x-input-error :messages="$errors->get('high')" class="mt-2" />
                        </div>

                        <!-- Low -->
                        <div>
                            <x-input-label for="low" :value="__('Low Price')" />
                            <x-text-input id="low" class="block mt-1 w-full" type="number" name="low" :value="old('low')" step="0.01" min="0" required />
                            <x-input-error :messages="$errors->get('low')" class="mt-2" />
                        </div>

                        <!-- Close -->
                        <div>
                            <x-input-label for="close" :value="__('Close Price')" />
                            <x-text-input id="close" class="block mt-1 w-full" type="number" name="close" :value="old('close')" step="0.01" min="0" required />
                            <x-input-error :messages="$errors->get('close')" class="mt-2" />
                        </div>

                        <!-- Volume -->
                        <div>
                            <x-input-label for="volume" :value="__('Volume')" />
                            <x-text-input id="volume" class="block mt-1 w-full" type="number" name="volume" :value="old('volume')" min="0" required />
                            <x-input-error :messages="$errors->get('volume')" class="mt-2" />
                        </div>

                        <!-- Product Name -->
                        <div>
                            <x-input-label for="product_name" :value="__('Product Name')" />
                            <x-text-input id="product_name" class="block mt-1 w-full" type="text" name="product_name" :value="old('product_name')" required />
                            <x-input-error :messages="$errors->get('product_name')" class="mt-2" />
                        </div>

                        <!-- Price -->
                        <div>
                            <x-input-label for="price" :value="__('Price')" />
                            <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="old('price')" step="0.01" min="0" required />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- Location -->
                        <div>
                            <x-input-label for="location" :value="__('Location')" />
                            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location')" required />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div>
                            <x-input-label for="category" :value="__('Category')" />
                            <x-text-input id="category" class="block mt-1 w-full" type="text" name="category" :value="old('category')" required />
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="4" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Additional Info (JSON) -->
                        <div>
                            <x-input-label for="additional_info" :value="__('Additional Info (JSON format)')" />
                            <textarea id="additional_info" name="additional_info" rows="4" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('additional_info') }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Example: {"manufacturer": "ABC Corp", "year": 2023, "attributes": ["waterproof", "durable"]}</p>
                            <x-input-error :messages="$errors->get('additional_info')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('market-data.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button class="ml-4">
                                {{ __('Submit') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 