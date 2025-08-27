<?php

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskCategory;
use App\Models\HelpdeskPriority;
use App\Services\HelpdeskService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * CreateTicketForm.
 *
 * Allows user to submit a new helpdesk ticket with attachments.
 */
class CreateTicketForm extends Component
{
    use WithFileUploads;

    public $title;

    public $description;

    public $category_id;

    public $priority_id;

    public $attachments = [];

    protected HelpdeskService $helpdeskService;

    /**
     * Inject HelpdeskService.
     */
    public function boot(HelpdeskService $helpdeskService)
    {
        $this->helpdeskService = $helpdeskService;
    }

    /**
     * Validation rules for new ticket.
     */
    protected function rules()
    {
        return [
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'category_id'   => ['required', 'integer', Rule::exists('helpdesk_categories', 'id')],
            'priority_id'   => ['required', 'integer', Rule::exists('helpdesk_priorities', 'id')],
            'attachments.*' => 'nullable|file|max:2048|mimes:jpg,png,pdf,docx,txt,xlsx',
        ];
    }

    /**
     * Handle creation of ticket.
     */
    public function createTicket()
    {
        $this->validate();

        try {
            $ticket = $this->helpdeskService->createTicket(
                [
                    'title'       => $this->title,
                    'description' => $this->description,
                    'category_id' => $this->category_id,
                    'priority_id' => $this->priority_id,
                ],
                Auth::user(),
                $this->attachments
            );

            session()->flash('message', 'Ticket created successfully!');

            return redirect()->route('helpdesk.show', $ticket->id);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create ticket: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.helpdesk.create-ticket-form', [
            'categories' => HelpdeskCategory::all(),
            'priorities' => HelpdeskPriority::all(),
        ]);
    }
}
