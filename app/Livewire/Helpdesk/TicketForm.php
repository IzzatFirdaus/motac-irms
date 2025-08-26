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
 * TicketForm.
 *
 * Livewire component for submitting a new helpdesk ticket.
 */
class TicketForm extends Component
{
    use WithFileUploads;

    public $title;

    public $description;

    public $category_id;

    public $priority_id;

    public $attachments = [];

    protected HelpdeskService $helpdeskService;

    /**
     * Called on component boot; inject HelpdeskService for ticket creation.
     */
    public function boot(HelpdeskService $helpdeskService)
    {
        $this->helpdeskService = $helpdeskService;
    }

    /**
     * Define validation rules for ticket creation.
     */
    protected function rules()
    {
        return [
            'title'         => 'required|string|max:255',
            'description'   => 'required|string|max:5000',
            'category_id'   => ['required', 'integer', Rule::exists('helpdesk_categories', 'id')],
            'priority_id'   => ['required', 'integer', Rule::exists('helpdesk_priorities', 'id')],
            'attachments'   => 'nullable|array',
            'attachments.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx,txt,xlsx',
        ];
    }

    /**
     * Handles the submission of the ticket creation form.
     */
    public function createTicket()
    {
        $this->validate();

        try {
            $ticket = $this->helpdeskService->createTicket(
                [
                    'title'       => trim($this->title),
                    'description' => trim($this->description),
                    'category_id' => $this->category_id,
                    'priority_id' => $this->priority_id,
                ],
                Auth::user(),
                $this->attachments
            );

            session()->flash('message', __('Tiket berjaya dihantar!'));

            // Redirect to the ticket's show page (route name according to convention)
            return redirect()->route('helpdesk.tickets.show', $ticket->id);
        } catch (\Exception $e) {
            session()->flash('error', __('Gagal menghantar tiket: ').$e->getMessage());
        }
    }

    /**
     * Render the ticket form with active categories and all priorities.
     */
    public function render()
    {
        return view('livewire.helpdesk.ticket-form', [
            'categories' => HelpdeskCategory::active()->get(),
            'priorities' => HelpdeskPriority::all(),
        ]);
    }
}
