@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Gestión de Imágenes</h1>
        <p class="text-slate-600 mt-1">Administra las imágenes de las habitaciones</p>
    </div>

    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <h2 class="text-xl font-bold text-slate-900 mb-6">Seleccionar Habitación</h2>
        
        @if($rooms->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
                <p class="mt-4 text-slate-600">No hay habitaciones registradas aún</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($rooms as $room)
                    <a href="{{ route('admin.images.room', $room->id) }}" class="block group">
                        <div class="bg-slate-50 rounded-lg border border-slate-200 hover:border-blue-400 hover:shadow-lg transition overflow-hidden">
                            <div class="h-40 bg-gradient-to-br from-slate-300 to-slate-400 flex items-center justify-center">
                                <svg class="w-16 h-16 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-lg text-slate-900 group-hover:text-blue-600 transition">Habitación {{ $room->number }}</h3>
                                <p class="text-sm text-slate-600 mt-1">{{ ucfirst($room->type) }}</p>
                                <div class="flex items-center justify-between mt-4">
                                    <span class="text-sm font-medium">
                                        @if($room->images_count > 0)
                                            <span class="text-green-600">{{ $room->images_count }} imagen{{ $room->images_count !== 1 ? 's' : '' }}</span>
                                        @else
                                            <span class="text-amber-600">Sin imágenes</span>
                                        @endif
                                    </span>
                                    <svg class="w-5 h-5 text-blue-600 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
