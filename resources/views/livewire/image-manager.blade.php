<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Gestión de Imágenes</h1>
            <p class="text-slate-600 mt-1">
                {{ $room ? 'Administrando fotos de la Habitación #' . $room->numero : 'Selecciona una habitación para comenzar' }}
            </p>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center justify-between">
            <p class="text-green-800">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center justify-between">
            <p class="text-red-800">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <div class="bg-white rounded-lg border border-slate-200 p-6 mb-6 shadow-sm">
        <label class="block text-sm font-bold text-slate-700 mb-2">Seleccionar Habitación</label>
        <select wire:model.live="roomId" class="w-full md:w-1/2 px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 transition">
            <option value="">-- Seleccione una habitación --</option>
            @foreach($rooms as $r)
                <option value="{{ $r->id }}">Habitación #{{ $r->numero }} - {{ ucfirst($r->tipo) }}</option>
            @endforeach
        </select>
    </div>

    @if($room)
        <div class="bg-white rounded-lg border border-slate-200 p-6 mb-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Subir Imagen para Habitación #{{ $room->numero }}</h3>
            <form wire:submit.prevent="uploadImage" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Seleccionar Imagen (Máximo 5MB)</label>
                    <div class="border-2 border-dashed border-slate-300 rounded-lg p-6 text-center hover:bg-slate-50 cursor-pointer hover:border-blue-400 transition" onclick="document.getElementById('fileInput').click()">
                        <input type="file" wire:model="imagen" accept="image/*" class="hidden" id="fileInput">
                        
                        {{-- SE ELIMINÓ LA VISTA PREVIA QUE CAUSABA EL ERROR 500 --}}
                        @if ($imagen)
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-16 w-16 bg-green-100 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-slate-800 mb-1">Archivo listo para subir</p>
                                <p class="text-xs text-slate-500 bg-slate-100 px-3 py-1 rounded-full">{{ $imagen->getClientOriginalName() }}</p>
                                <p class="text-xs text-blue-600 font-medium mt-2">Haz clic en "Guardar Imagen" para enviarla a la nube</p>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center pointer-events-none">
                                <svg class="w-12 h-12 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                <p class="text-slate-700 font-medium">Haz clic para buscar</p>
                                <p class="text-slate-500 text-sm mt-1">Soporta JPG, PNG (Max 5MB)</p>
                            </div>
                        @endif
                    </div>
                    @error('imagen') <span class="text-red-600 text-xs mt-2 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm" wire:loading.attr="disabled" @if(!$imagen) disabled class="opacity-50 cursor-not-allowed" @endif>
                        <span wire:loading.remove wire:target="uploadImage">Guardar Imagen</span>
                        <span wire:loading wire:target="uploadImage">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Subiendo a la nube...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Galería de la Habitación
            </h3>
            
            @if(count($images) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($images as $image)
                        <div class="border border-slate-200 rounded-lg overflow-hidden bg-slate-50 flex flex-col hover:shadow-md transition">
                            <div class="h-48 w-full bg-slate-200 relative group">
                                <img src="{{ $image['path'] }}" alt="Imagen de habitación" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/400x300?text=Error+de+URL'">
                                @if($image['is_primary'])
                                    <span class="absolute top-2 left-2 px-2 py-1 bg-green-500 text-white text-xs font-bold rounded shadow border border-green-600">Principal</span>
                                @endif
                                
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <button wire:click="deleteImage({{ $image['id'] }})" onclick="return confirm('¿Seguro que deseas eliminar esta imagen permanentemente?')" class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full shadow-lg transition transform hover:scale-110" title="Eliminar Imagen">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="p-3 mt-auto bg-white border-t border-slate-100 text-center">
                                @if(!$image['is_primary'])
                                    <button wire:click="setPrimaryImage({{ $image['id'] }})" class="w-full text-blue-600 hover:text-blue-800 hover:bg-blue-50 text-sm font-medium py-1.5 rounded transition">
                                        Hacer Principal
                                    </button>
                                @else
                                    <span class="w-full block text-green-600 text-sm font-medium py-1.5">
                                        Imagen destacada
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 bg-slate-50 rounded-lg border-2 border-dashed border-slate-300 flex flex-col items-center">
                    <div class="p-4 bg-white rounded-full shadow-sm border border-slate-100 mb-3">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <p class="text-slate-700 font-medium">No hay imágenes registradas</p>
                    <p class="text-slate-500 text-sm mt-1">Sube la primera foto para esta habitación en la parte superior.</p>
                </div>
            @endif
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-lg border border-slate-200 shadow-sm flex flex-col items-center justify-center">
            <svg class="w-16 h-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Selecciona una Habitación</h3>
            <p class="text-slate-500 max-w-md mx-auto">Para empezar a gestionar o subir nuevas fotografías, primero debes seleccionar una habitación del menú desplegable superior.</p>
        </div>
    @endif
</div>