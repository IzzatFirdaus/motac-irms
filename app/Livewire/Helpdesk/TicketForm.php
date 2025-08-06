<?php

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskCategory;
use App\Models\HelpdeskPriority;
use App\Services\HelpdeskService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;


class TicketForm extends Component
{
    use WithFileUploads;

    public $title;
    public $description;
    public $category_id;
    public $priority_id;
    public $attachments = [];

    protected HelpdeskService $helpdeskService;

    public function boot(HelpdeskService $helpdeskService)
    {
        $this->helpdeskService = $helpdeskService;
    }

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => ['required', 'integer', Rule::exists('helpdesk_categories', 'id')],
            'priority_id' => ['required', 'integer', Rule::exists('helpdesk_priorities', 'id')],
            'attachments.*' => 'nullable|file|max:2048|mimes:jpg,png,pdf,docx,txt,xlsx', // Max 2MB, allowed types
        ];
    }

    public function createTicket()
    {
        $this->validate();

        try {
            $ticket = $this->helpdeskService->createTicket(
                [
                    'title' => $this->title,
                    'description' => $this->description,
                    'category_id' => $this->category_id,
                    'priority_id' => $this->priority_id,
                ],
                Auth::user(),
                $this->attachments // Pass uploaded files
            );

            session()->flash('message', 'Ticket created successfully!');
            return redirect()->route('helpdesk.view', $ticket->id);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create ticket: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.helpdesk.ticket-form', [
            'categories' => HelpdeskCategory::all(),
            'priorities' => HelpdeskPriority::all(),
        ]);
    }
}
