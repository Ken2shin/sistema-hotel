<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

/**
 * POO: CLASE ABSTRACTA PADRE
 * Define el contrato y la lógica compartida para todo el panel de configuración.
 */
abstract class BaseConfigComponent extends Component
{
    use WithPagination;

    public $searchQuery = '';
    public $activeTab = 'settings';
    public $isSaving = false;

    public function updatedSearchQuery()
    {
        $this->resetPage('settingsPage');
        $this->resetPage('usersPage');
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->searchQuery = '';
    }

    abstract public function resetForm();
}

/**
 * POO: CLASE HIJA (SettingsManager)
 * Hereda la funcionalidad base e implementa la lógica de Negocio (Variables y Usuarios)
 */
#[Layout('layouts.app')]
class SettingsManager extends BaseConfigComponent
{
    // --- ESTADO: VARIABLES DE ENTORNO ---
    public $editingKey = null;
    public $editingValue = null;
    public $editingType = 'string';
    public $editingDescription = '';
    public $showSettingForm = false;

    // --- ESTADO: GESTIÓN DE USUARIOS Y PERMISOS ---
    public $showUserForm = false;
    public $userName = '';
    public $userEmail = '';
    public $userRole = '';
    public $userStatus = true;
    public $generatedPassword = null;
    
    // Propiedades para el panel de Módulos
    public $customPermissions = false;
    public $selectedModules = [];

    /**
     * POO: Encapsulamiento del catálogo de módulos del ERP
     */
    public function getAvailableModulesProperty()
    {
        return [
            'dashboard' => ['icon' => 'fa-chart-pie', 'label' => 'Dashboard Analítico'],
            'reservations' => ['icon' => 'fa-calendar-check', 'label' => 'Gestión de Reservas'],
            'clients' => ['icon' => 'fa-users', 'label' => 'Directorio de Clientes'],
            'rooms' => ['icon' => 'fa-bed', 'label' => 'Control de Habitaciones'],
            'payments' => ['icon' => 'fa-credit-card', 'label' => 'Facturación y Pagos'],
            'reports' => ['icon' => 'fa-chart-line', 'label' => 'Generador de Reportes'],
            'settings' => ['icon' => 'fa-sliders', 'label' => 'Configuración Avanzada'],
        ];
    }

    public function render()
    {
        $settingsQuery = Setting::query();
        if (!empty($this->searchQuery) && $this->activeTab === 'settings') {
            $settingsQuery->where('key', 'ilike', '%' . $this->searchQuery . '%')
                          ->orWhere('description', 'ilike', '%' . $this->searchQuery . '%');
        }
        $paginatedSettings = $settingsQuery->orderBy('key', 'asc')->paginate(10, ['*'], 'settingsPage');
        
        $mappedSettings = $paginatedSettings->getCollection()->map(fn($s) => [
            'key' => $s->key,
            'value' => $s->is_encrypted ? '[ENCRYPTED]' : $s->getValue(),
            'type' => $s->type,
            'description' => $s->description,
            'is_encrypted' => $s->is_encrypted,
        ]);
        $paginatedSettings->setCollection($mappedSettings);

        $usersQuery = User::with('role');
        if (!empty($this->searchQuery) && $this->activeTab === 'users') {
            $usersQuery->where('name', 'ilike', '%' . $this->searchQuery . '%')
                       ->orWhere('email', 'ilike', '%' . $this->searchQuery . '%');
        }
        $paginatedUsers = $usersQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'usersPage');

        return view('livewire.settings-manager', [
            'settings' => $paginatedSettings,
            'users' => $paginatedUsers,
            'roles' => Role::all(),
        ]);
    }

    // ==================================================
    // MÉTODOS DE VARIABLES DE ENTORNO
    // ==================================================
    public function openEditForm($key = null)
    {
        $this->resetForm();
        if ($key) {
            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                $this->editingKey = $setting->key;
                $val = $setting->getValue();
                $this->editingValue = is_array($val) ? json_encode($val, JSON_PRETTY_PRINT) : $val;
                $this->editingType = $setting->type;
                $this->editingDescription = $setting->description ?? '';
            }
        }
        $this->showSettingForm = true;
    }

    public function saveSetting()
    {
        $this->validate([
            'editingKey' => 'required|string|max:255',
            'editingValue' => 'required',
            'editingType' => 'required|in:string,boolean,integer,decimal,array,json',
        ]);

        $this->isSaving = true;

        try {
            $valToSave = $this->editingValue;
            if (in_array($this->editingType, ['array', 'json']) && is_string($this->editingValue)) {
                $decoded = json_decode($this->editingValue, true);
                if (json_last_error() === JSON_ERROR_NONE) $valToSave = $decoded;
            }

            Setting::setSetting($this->editingKey, $valToSave, $this->editingType, $this->editingDescription);
            session()->flash('message', 'Variable de entorno actualizada.');
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        } finally {
            $this->isSaving = false;
        }
    }

    public function deleteSetting($key)
    {
        Setting::where('key', $key)->delete();
        session()->flash('message', 'Variable eliminada del sistema.');
    }

    // ==================================================
    // MÉTODOS DE GESTIÓN DE USUARIOS Y PERMISOS
    // ==================================================
    public function openUserForm()
    {
        $this->resetForm();
        $this->showUserForm = true;
    }

    public function saveUser()
    {
        $this->validate([
            'userName' => 'required|string|max:255',
            'userEmail' => 'required|email|unique:users,email',
            'userRole' => $this->customPermissions ? 'nullable' : 'required|exists:roles,id',
        ], [
            'userRole.required' => 'Debes asignar un rol o activar los permisos personalizados.'
        ]);

        $this->isSaving = true;

        try {
            DB::beginTransaction();

            $roleIdToAssign = $this->userRole;

            // Lógica Central: Si el administrador activó el panel de módulos manuales
            if ($this->customPermissions) {
                if (empty($this->selectedModules)) {
                    throw new \Exception("Debes habilitar al menos un módulo de acceso.");
                }

                // POO: Generamos un Rol único detrás de escena para no romper la base de datos
                $customRole = Role::create([
                    'name' => 'Acceso Personalizado - ' . $this->userName,
                    'description' => 'Rol generado automáticamente con permisos granulares.'
                ]);
                $roleIdToAssign = $customRole->id;

                // Asignamos los permisos (Módulos) seleccionados en la UI
                foreach ($this->selectedModules as $moduleKey) {
                    $permId = DB::table('permissions')->where('name', $moduleKey)->value('id');
                    if (!$permId) {
                        $permId = DB::table('permissions')->insertGetId(['name' => $moduleKey]);
                    }
                    DB::table('role_permissions')->insert([
                        'role_id' => $customRole->id,
                        'permission_id' => $permId
                    ]);
                }
            }

            // Generación de contraseña de grado empresarial
            $rawPassword = Str::password(12, true, true, false, false);

            User::create([
                'name' => $this->userName,
                'email' => $this->userEmail,
                'password' => Hash::make($rawPassword),
                'role_id' => $roleIdToAssign,
                'is_active' => $this->userStatus,
            ]);

            DB::commit();

            // Mostramos la clave y reseteamos campos (pero dejamos el modal abierto para que la copie)
            $this->generatedPassword = $rawPassword;
            session()->flash('message', 'Empleado registrado. Guarda la contraseña generada a continuación.');
            
            $this->userName = '';
            $this->userEmail = '';
            $this->userRole = '';
            $this->selectedModules = [];
            $this->customPermissions = false;

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al procesar: ' . $e->getMessage());
        } finally {
            $this->isSaving = false;
        }
    }

    public function toggleUserStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            session()->flash('error', 'No puedes suspender tu propia sesión.');
            return;
        }
        $user->update(['is_active' => !$user->is_active]);
        session()->flash('message', 'Estado del empleado actualizado.');
    }

    public function resetForm()
    {
        $this->reset([
            'editingKey', 'editingValue', 'editingType', 'editingDescription', 'showSettingForm',
            'showUserForm', 'userName', 'userEmail', 'userRole', 'generatedPassword', 'userStatus',
            'customPermissions', 'selectedModules'
        ]);
        $this->resetValidation();
    }
}