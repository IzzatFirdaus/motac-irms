<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Livewire Component: Contact Us.
 *
 * Features:
 * - Contact information display with localization support
 * - Interactive contact form with validation and PDPA consent
 * - Success/error message handling
 * - Maps integration for office location
 *
 * Note: Since we cannot use ->layout() directly with the View object in this project,
 * the navbar must be included via a regular route with @extends in a wrapper view.
 * This component should be embedded in a page that extends the main layout.
 *
 * @author IzzatFirdaus
 *
 * @last_update 2025-08-10
 */
class ContactUs extends Component
{
    // Form fields (typed properties)
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $subject = '';

    public string $message = '';

    public string $inquiry_type = '';

    public bool $consent = false;

    // UI state flags
    public bool $showSuccessMessage = false;

    public bool $showErrorMessage = false;

    /**
     * Define validation rules for the contact form.
     *
     * @return array<string, string> Validation rules
     */
    protected function rules(): array
    {
        return [
            'name'         => 'required|string|min:2|max:100',
            'email'        => 'required|email|max:255',
            'phone'        => 'nullable|string|max:20|regex:/^[\d\s\-\+\(\)]+$/',
            'subject'      => 'required|string|min:5|max:200',
            'message'      => 'required|string|min:10|max:1000',
            'inquiry_type' => 'required|string|in:general,technical,feedback,complaint,equipment_loan',
            'consent'      => 'accepted',
        ];
    }

    /**
     * Custom validation error messages (localized).
     *
     * @return array<string, string> Error messages
     */
    protected function messages(): array
    {
        return [
            'name.required'         => __('contact-us.validation.name_required'),
            'name.min'              => __('contact-us.validation.name_min'),
            'email.required'        => __('contact-us.validation.email_required'),
            'email.email'           => __('contact-us.validation.email_invalid'),
            'phone.regex'           => __('contact-us.validation.phone_invalid'),
            'subject.required'      => __('contact-us.validation.subject_required'),
            'subject.min'           => __('contact-us.validation.subject_min'),
            'message.required'      => __('contact-us.validation.message_required'),
            'message.min'           => __('contact-us.validation.message_min'),
            'inquiry_type.required' => __('contact-us.validation.inquiry_type_required'),
            'consent.accepted'      => __('contact-us.validation.consent_required'),
        ];
    }

    /**
     * Submit the contact form.
     * Validates input, sends notification (if applicable), and shows success message.
     */
    public function submitForm(): void
    {
        try {
            // Validate the form data
            $validated = $this->validate();

            // Example: Send notification email (uncomment to implement)
            // Mail::to('bpm@motac.gov.my')->send(new ContactFormSubmission($validated));

            // Example: Log the submission (uncomment to implement)
            // \Log::info('Contact form submitted', ['data' => $validated, 'user' => auth()->user() ? auth()->user()->name : 'Guest']);

            // Reset form and show success message
            $this->resetForm();
            $this->showSuccessMessage = true;
        } catch (ValidationException $e) {
            // Livewire automatically handles validation errors
        } catch (\Throwable $e) {
            // Log the error for debugging
            \Log::error('Contact form submission error', [
                'message'   => $e->getMessage(),
                'exception' => $e,
                'trace'     => $e->getTraceAsString(),
            ]);

            // Show user-friendly error message
            $this->showErrorMessage = true;
        }
    }

    /**
     * Reset the form fields and validation state.
     */
    public function resetForm(): void
    {
        $this->name         = '';
        $this->email        = '';
        $this->phone        = '';
        $this->subject      = '';
        $this->message      = '';
        $this->inquiry_type = '';
        $this->consent      = false;

        $this->resetValidation();
    }

    /**
     * Hide the success message banner.
     */
    public function hideSuccessMessage(): void
    {
        $this->showSuccessMessage = false;
    }

    /**
     * Hide the error message banner.
     */
    public function hideErrorMessage(): void
    {
        $this->showErrorMessage = false;
    }

    /**
     * Get localized inquiry types for the dropdown.
     * This is a computed property (accessed via $this->inquiryTypes).
     *
     * @return array<string, string> Inquiry types mapping
     */
    public function getInquiryTypesProperty(): array
    {
        return [
            'general'        => __('contact-us.inquiry_types.general'),
            'technical'      => __('contact-us.inquiry_types.technical'),
            'feedback'       => __('contact-us.inquiry_types.feedback'),
            'complaint'      => __('contact-us.inquiry_types.complaint'),
            'equipment_loan' => __('contact-us.inquiry_types.equipment_loan'),
        ];
    }

    /**
     * Render the Contact Us component.
     *
     * IMPORTANT: We cannot use the ->layout() method here because our setup uses
     * Illuminate\View\View which doesn't have this method. Instead, create a regular
     * route/controller that returns a view extending the main layout, and embed this
     * Livewire component inside it.
     *
     * @return \Illuminate\View\View The contact form view
     */
    public function render(): View
    {
        $pageTitle = __('contact-us.title');

        return view('livewire.contact-us', [
            'title'        => $pageTitle,
            'pageTitle'    => $pageTitle,
            'inquiryTypes' => $this->inquiryTypes,
        ]);
    }
}
