<?php

namespace App\Livewire;

use Livewire\Component;

class ContactPage extends Component
{
    public function render()
    {
        return view('livewire.contact-page')
            ->layout('layouts.app')
            ->layoutData([
                'title' => 'Contact · Bahujan Collective',
            ]);
    }
}