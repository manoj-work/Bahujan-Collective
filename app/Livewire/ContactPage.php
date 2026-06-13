<?php

namespace App\Livewire;

use App\Models\Feedback;
use Livewire\Component;

class ContactPage extends Component
{
    public string $name = '';

    public string $email = '';

    public string $message = '';

    public bool $feedbackSubmitted = false;

    public function submitFeedback(): void
    {
        Feedback::createFromSubmission($this->validate(Feedback::submissionRules()));

        $this->feedbackSubmitted = true;
        $this->resetFeedbackForm();
    }

    protected function resetFeedbackForm(): void
    {
        $this->name = '';
        $this->email = '';
        $this->message = '';
    }

    public function render()
    {
        return view('livewire.contact-page')
            ->layout('layouts.app')
            ->layoutData([
                'title' => 'Contact & Feedback · Bahujan Collective',
            ]);
    }
}
