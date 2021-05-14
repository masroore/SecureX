<?php

namespace App\Http\Livewire\Vault\Site;

use App\Models\Vaults\Site;
use App\Models\Vaults\SiteCustomField;
use App\Models\Vaults\Vault;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Lang;

class AddCustomField extends Component
{
    use AuthorizesRequests;

    public $name;
    public $value;
    public $site;
    public $field;

    public function mount(Site $site)
    {
        $this->site = $site;
    }

    public function addField()
    {
        $this->validate([
            'name' => 'required|string|max:25',
            'value' => 'required|string|max:235'
        ]);

        $this->authorize('update', $this->site);

        $field = $this->site->custom_fields()->create(['name' => $this->name, 'value' => $this->value]);

        $this->emit('fieldAdded', Lang::get('alerts.site.custom_field_added'));
        
        $this->resetInput();
    }

    public function resetInput()
    {
        $this->name = null;
        $this->value = null;
    }

    public function render()
    {
        return view('livewire.vault.site.add-custom-field',[
            's' => $this->site
        ]);
    }
}
