<?php

namespace App\Http\Livewire\Search;

use App\Models\Vaults\Site;
use App\Models\Vaults\Vault;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Sites extends Component
{
    public $search = '';
    public $sites;

    public function render()
    {
        $search = '%' . $this->search . '%';
        
        $this->sites = Site::whereLike('name', $search)->where('user_id', Auth::id())->limit(5)->get();

        return view('livewire.search.sites');
    }
}
