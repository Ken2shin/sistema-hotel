class HotelVisualizer3D {
    constructor(containerId, apiUrl) {
        this.container = document.getElementById(containerId);
        this.apiUrl = apiUrl;
        this.scene = null;
        this.camera = null;
        this.renderer = null;
        this.rooms = new Map();
        this.selectedRoom = null;
        this.init();
    }

    init() {
        const width = this.container.clientWidth;
        const height = this.container.clientHeight;

        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(0xf0f0f0);

        this.camera = new THREE.PerspectiveCamera(75, width / height, 0.1, 1000);
        this.camera.position.set(15, 15, 20);
        this.camera.lookAt(10, 5, 0);

        this.renderer = new THREE.WebGLRenderer({ antialias: true });
        this.renderer.setSize(width, height);
        this.renderer.shadowMap.enabled = true;
        this.container.appendChild(this.renderer.domElement);

        this.setupLighting();
        this.setupScene();
        this.setupEventListeners();
        this.animate();

        window.addEventListener('resize', () => this.onWindowResize());
    }

    setupLighting() {
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
        this.scene.add(ambientLight);

        const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
        directionalLight.position.set(20, 30, 20);
        directionalLight.castShadow = true;
        directionalLight.shadow.mapSize.width = 2048;
        directionalLight.shadow.mapSize.height = 2048;
        this.scene.add(directionalLight);
    }

    setupScene() {
        const floorGeometry = new THREE.PlaneGeometry(50, 40);
        const floorMaterial = new THREE.MeshStandardMaterial({ color: 0xe0e0e0 });
        const floor = new THREE.Mesh(floorGeometry, floorMaterial);
        floor.rotation.x = -Math.PI / 2;
        floor.receiveShadow = true;
        this.scene.add(floor);

        const walls = [
            { pos: [25, 5, 0], size: [50, 10, 0.5] },
            { pos: [-25, 5, 0], size: [50, 10, 0.5] },
            { pos: [0, 5, 20], size: [0.5, 10, 40] },
            { pos: [0, 5, -20], size: [0.5, 10, 40] },
        ];

        walls.forEach(wall => {
            const wallGeometry = new THREE.BoxGeometry(...wall.size);
            const wallMaterial = new THREE.MeshStandardMaterial({ color: 0xccccccc });
            const wallMesh = new THREE.Mesh(wallGeometry, wallMaterial);
            wallMesh.position.set(...wall.pos);
            wallMesh.castShadow = true;
            wallMesh.receiveShadow = true;
            this.scene.add(wallMesh);
        });
    }

    createRoom(roomData) {
        const color = this.getStateColor(roomData.estado);
        const geometry = new THREE.BoxGeometry(roomData.ancho, roomData.alto, roomData.profundo);
        const material = new THREE.MeshStandardMaterial({ color: color });
        const mesh = new THREE.Mesh(geometry, material);

        mesh.position.set(roomData.posicion_x, roomData.alto / 2, roomData.posicion_z);
        mesh.castShadow = true;
        mesh.receiveShadow = true;
        mesh.userData = roomData;

        const labelGeometry = new THREE.BoxGeometry(roomData.ancho * 0.8, 0.3, roomData.profundo * 0.8);
        const labelMaterial = new THREE.MeshStandardMaterial({ color: 0x333333 });
        const label = new THREE.Mesh(labelGeometry, labelMaterial);
        label.position.z = roomData.profundo / 2 - 0.2;
        label.position.y = roomData.alto * 0.3;

        mesh.add(label);
        this.scene.add(mesh);
        this.rooms.set(roomData.id, mesh);

        return mesh;
    }

    getStateColor(estado) {
        const colors = {
            'disponible': 0x4caf50,
            'ocupada': 0xf44336,
            'reservada': 0xffc107,
        };
        return colors[estado] || 0x9e9e9e;
    }

    loadHotelPlan(fecha) {
        fetch(`${this.apiUrl}/api/hotel/floor-plan?fecha=${fecha}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.clearRooms();
                    data.data.habitaciones.forEach(room => {
                        this.createRoom(room);
                    });

                    if (this.onPlanLoaded) {
                        this.onPlanLoaded(data.data);
                    }
                }
            })
            .catch(error => console.error('Error loading hotel plan:', error));
    }

    clearRooms() {
        this.rooms.forEach(room => {
            this.scene.remove(room);
        });
        this.rooms.clear();
    }

    setupEventListeners() {
        document.addEventListener('click', (event) => this.onDocumentClick(event));
    }

    onDocumentClick(event) {
        const raycaster = new THREE.Raycaster();
        const mouse = new THREE.Vector2();

        mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
        mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

        raycaster.setFromCamera(mouse, this.camera);

        const intersects = raycaster.intersectObjects(Array.from(this.rooms.values()));

        if (intersects.length > 0) {
            const selectedMesh = intersects[0].object;
            if (this.selectedRoom) {
                const prevColor = this.getStateColor(this.selectedRoom.userData.estado);
                this.selectedRoom.material.color.setHex(prevColor);
            }

            this.selectedRoom = selectedMesh;
            this.selectedRoom.material.color.setHex(0x00bcd4);

            if (this.onRoomSelected) {
                this.onRoomSelected(selectedMesh.userData);
            }
        }
    }

    updateRoomState(roomId, estado) {
        const room = this.rooms.get(roomId);
        if (room) {
            room.userData.estado = estado;
            const color = this.getStateColor(estado);
            room.material.color.setHex(color);
        }
    }

    animate() {
        requestAnimationFrame(() => this.animate());
        this.renderer.render(this.scene, this.camera);
    }

    onWindowResize() {
        const width = this.container.clientWidth;
        const height = this.container.clientHeight;
        this.camera.aspect = width / height;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(width, height);
    }
}

export default HotelVisualizer3D;
