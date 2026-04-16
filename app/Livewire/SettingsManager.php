<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Component;

class SettingsManager extends Component
{
    public $settings = [];
    public $editingKey = null;
    public $editingValue = null;
    public $editingType = 'string';
    public $editingDescription = '';
    public $showForm = false;
    public $searchQuery = '';
    public $isSaving = false;

    public function mount()
    {
        $this->loadSettings();
    }

    private function loadSettings()
    {
        $this->settings = Setting::all()
            ->map(fn($s) => [
                'id' => $s->id,
                'key' => $s->key,
                'value' => $s->is_encrypted ? '[ENCRYPTED]' : $s->getValue(),
                'type' => $s->type,
                'description' => $s->description,
                'is_encrypted' => $s->is_encrypted,
            ])
            ->toArray();
    }

    public function render()
    {
        $filtered = collect($this->settings)->filter(function ($setting) {
            if (empty($this->searchQuery)) return true;
            return str_contains(strtolower($setting['key']), strtolower($this->searchQuery)) ||
                   str_contains(strtolower($setting['description'] ?? ''), strtolower($this->searchQuery));
        });

        return view('livewire.settings-manager', [
            'filteredSettings' => $filtered->values(),
            'editingKey' => $this->editingKey,
            'editingValue' => $this->editingValue,
            'editingType' => $this->editingType,
            'editingDescription' => $this->editingDescription,
            'showForm' => $this->showForm,
            'searchQuery' => $this->searchQuery,
            'isSaving' => $this->isSaving,
        ]);
    }

    public function openEditForm($key)
    {
        $setting = collect($this->settings)->firstWhere('key', $key);
        if ($setting) {
            $this->editingKey = $key;
            $this->editingValue = $setting['value'];
            $this->editingType = $setting['type'];
            $this->editingDescription = $setting['description'] ?? '';
            $this->showForm = true;
        }
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
            Setting::setSetting(
                $this->editingKey,
                $this->editingValue,
                $this->editingType
            )->update(['description' => $this->editingDescription]);

            session()->flash('message', 'Configuración actualizada exitosamente');
            $this->resetForm();
            $this->loadSettings();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        } finally {
            $this->isSaving = false;
        }
    }

    public function deleteSetting($key)
    {
        try {
            Setting::where('key', $key)->delete();
            session()->flash('message', 'Configuración eliminada');
            $this->loadSettings();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar');
        }
    }

    public function resetForm()
    {
        $this->reset(['editingKey', 'editingValue', 'editingType', 'editingDescription', 'showForm']);
        $this->resetValidation();
    }
}
