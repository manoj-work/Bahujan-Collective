<?php

namespace App\Livewire;

use App\Models\Feedback;
use Livewire\Component;

class FeedbackModal extends Component
{
    public bool $showModal = false;
    public string $name = '';
    public string $email = '';
    public string $message = '';
    public bool $submitted = false;

    public function openModal(): void
    {
        $this->showModal = true;
        $this->resetForm();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function submit(): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'message' => 'required|string|min:10',
        ]);

        Feedback::create([
            'NAME' => $this->name,
            'EMAIL' => $this->email,
            'MESSAGE' => $this->message,
        ]);

        $this->submitted = true;
        
        // Reset form after a short delay
        $this->resetForm();
        
        // Close modal after 2 seconds
        $this->dispatch('feedback-submitted');
    }

    protected function resetForm(): void
    {
        $this->name = '';
        $this->email = '';
        $this->message = '';
        $this->submitted = false;
    }

    protected $listeners = ['open-feedback-modal' => 'openModal'];

    public function render()
    {
        return view('livewire.feedback-modal');
    }
}

