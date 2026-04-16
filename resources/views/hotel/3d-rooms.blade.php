@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5>Visualización 3D del Hotel</h5>
                </div>
                <div class="card-body p-0" style="height: 600px;">
                    <div id="visualizer-container" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5>Filtros</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="fecha-selector">Seleccionar Fecha</label>
                        <input type="date" id="fecha-selector" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>

                    <div id="estadisticas" class="mt-4">
                        <h6>Estadísticas</h6>
                        <div class="alert alert-info">
                            <small>
                                <strong>Total:</strong> <span id="stat-total">0</span><br>
                                <strong style="color: #4caf50;">Disponibles:</strong> <span id="stat-disponibles">0</span><br>
                                <strong style="color: #f44336;">Ocupadas:</strong> <span id="stat-ocupadas">0</span><br>
                                <strong style="color: #ffc107;">Reservadas:</strong> <span id="stat-reservadas">0</span>
                            </small>
                        </div>
                    </div>

                    <div id="room-details" class="mt-4" style="display: none;">
                        <h6>Detalles de Habitación</h6>
                        <div class="alert alert-light">
                            <small>
                                <strong>Número:</strong> <span id="detail-numero"></span><br>
                                <strong>Tipo:</strong> <span id="detail-tipo"></span><br>
                                <strong>Capacidad:</strong> <span id="detail-capacidad"></span><br>
                                <strong>Precio:</strong> $<span id="detail-precio"></span><br>
                                <strong>Estado:</strong> <span id="detail-estado"></span>
                            </small>
                        </div>
                        <button class="btn btn-sm btn-primary btn-block" onclick="reservarHabitacion()">Reservar</button>
                    </div>

                    <div class="mt-4">
                        <h6>Leyenda</h6>
                        <div class="mb-2">
                            <span style="display: inline-block; width: 20px; height: 20px; background-color: #4caf50; border-radius: 3px;"></span>
                            Disponible
                        </div>
                        <div class="mb-2">
                            <span style="display: inline-block; width: 20px; height: 20px; background-color: #f44336; border-radius: 3px;"></span>
                            Ocupada
                        </div>
                        <div>
                            <span style="display: inline-block; width: 20px; height: 20px; background-color: #ffc107; border-radius: 3px;"></span>
                            Reservada
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="{{ asset('js/3d-visualizer.js') }}"></script>

<script>
    let visualizer;
    let selectedRoomData = null;

    document.addEventListener('DOMContentLoaded', () => {
        visualizer = new HotelVisualizer3D('visualizer-container', window.location.origin);

        visualizer.onPlanLoaded = (data) => {
            document.getElementById('stat-total').textContent = data.estadisticas.total;
            document.getElementById('stat-disponibles').textContent = data.estadisticas.disponibles;
            document.getElementById('stat-ocupadas').textContent = data.estadisticas.ocupadas;
            document.getElementById('stat-reservadas').textContent = data.estadisticas.reservadas;
        };

        visualizer.onRoomSelected = (roomData) => {
            selectedRoomData = roomData;
            document.getElementById('detail-numero').textContent = roomData.numero;
            document.getElementById('detail-tipo').textContent = roomData.tipo;
            document.getElementById('detail-capacidad').textContent = roomData.capacidad;
            document.getElementById('detail-precio').textContent = roomData.precio_noche;
            document.getElementById('detail-estado').textContent = roomData.estado.toUpperCase();
            document.getElementById('room-details').style.display = 'block';
        };

        const fechaInput = document.getElementById('fecha-selector');
        fechaInput.addEventListener('change', (e) => {
            visualizer.loadHotelPlan(e.target.value);
        });

        visualizer.loadHotelPlan(fechaInput.value);
    });

    function reservarHabitacion() {
        if (!selectedRoomData) return;

        if (selectedRoomData.estado !== 'disponible') {
            alert('La habitación no está disponible');
            return;
        }

        console.log('Reservar habitación:', selectedRoomData);
    }
</script>

<style>
    #visualizer-container {
        background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%);
    }
</style>
@endsection
