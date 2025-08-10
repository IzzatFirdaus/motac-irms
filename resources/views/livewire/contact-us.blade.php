{{--
    Contact Us Livewire Component View
    IMPORTANT: Livewire requires a SINGLE root HTML element per component view.
    Do NOT use @extends or @section here.
    Layout is applied via the Livewire component class, NOT with Blade layout inheritance.
    The layout should be set in ContactUs.php with ->layout('layouts.app') or ->layout('layouts.layout-app')
    depending on your app structure.
--}}

@php
    use Illuminate\Support\Str;
@endphp

<div>
    {{-- Card styles for markdown readability --}}
    <style>
        .card-body-markdown {
            font-family: 'Noto Sans', sans-serif !important;
            color: var(--bs-body-color);
            line-height: 1.65;
        }
        .card-body-markdown h1,
        .card-body-markdown h2,
        .card-body-markdown h3,
        .card-body-markdown h4,
        .card-body-markdown h5,
        .card-body-markdown h6 {
            font-family: 'Noto Sans', sans-serif !important;
            color: var(--bs-emphasis-color);
            margin-top: 1.6em;
            margin-bottom: 0.5em;
            font-weight: 600;
        }
        .card-body-markdown h1 { font-size: 2rem; }
        .card-body-markdown h2 { font-size: 1.35rem; }
        .card-body-markdown h3 { font-size: 1.12rem; }
        .card-body-markdown ul,
        .card-body-markdown ol { padding-left: 2em; margin-bottom: 1.1em; }
        .card-body-markdown p { margin-bottom: 1em; }
        .card-body-markdown a { color: var(--bs-primary); text-decoration: underline; }
        .card-body-markdown a:hover { filter: brightness(90%); }
        .card-body-markdown strong { font-weight: 600; }
        .card-body-markdown blockquote {
            border-left: 4px solid var(--bs-primary);
            margin-left: 0;
            padding-left: 1em;
            color: #555;
        }
        .app-brand-text { color: var(--bs-body-color) !important; }
        @media (max-width: 600px) {
            .card-body-markdown { font-size: 0.98rem; }
        }
    </style>

    {{-- Success Message (ARIA live region for accessibility) --}}
    @if($showSuccessMessage)
        <div class="row justify-content-center mb-3" aria-live="polite">
            <div class="col-md-10 col-lg-8">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>{{ __('contact-us.success_title') }}</strong>
                    {{ __('contact-us.success_message') }}
                    <button type="button" class="btn-close" wire:click="hideSuccessMessage" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    {{-- Error Message --}}
    @if($showErrorMessage)
        <div class="row justify-content-center mb-3" aria-live="polite">
            <div class="col-md-10 col-lg-8">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>{{ __('contact-us.error_title') }}</strong>
                    {{ __('contact-us.submission_error') }}
                    <button type="button" class="btn-close" wire:click="hideErrorMessage" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    {{-- Main page content --}}
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8 col-xl-7">
                {{-- Application Logo and Name --}}
                <div class="app-brand justify-content-center mb-4">
                    <a href="{{ url('/') }}" class="app-brand-link gap-2" aria-label="MOTAC IRMS Home">
                        <span class="app-brand-logo demo">
                            <img src="{{ asset('assets/img/logo/motac-logo.svg') }}"
                                 alt="MOTAC IRMS Logo"
                                 style="height: 32px; width: auto;">
                        </span>
                        <span class="app-brand-text demo text-body fw-bold fs-4 ms-1 app-brand-text">
                            {{ __('app.system_name') }}
                        </span>
                    </a>
                </div>

                {{-- Contact Info Card (Markdown-rendered) --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white p-3">
                        <h4 class="mb-0 text-white d-flex align-items-center">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            {{ __('contact-us.contact_info_title') ?? __('contact-us.title') }}
                        </h4>
                    </div>
                    <div class="card-body p-4 card-body-markdown">
                        {!! Str::markdown(__('contact-us.content')) !!}
                    </div>
                </div>

                {{-- Contact Form (Livewire) --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white p-3">
                        <h4 class="mb-0 text-white d-flex align-items-center">
                            <i class="bi bi-envelope-fill me-2"></i>
                            {{ __('contact-us.form.submit') }}
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <form wire:submit.prevent="submitForm" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">
                                        {{ __('contact-us.form.name') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           wire:model.lazy="name"
                                           placeholder="{{ __('contact-us.form.name_placeholder') }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        {{ __('contact-us.form.email') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           wire:model.lazy="email"
                                           placeholder="{{ __('contact-us.form.email_placeholder') }}"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">
                                        {{ __('contact-us.form.phone') }}
                                    </label>
                                    <input type="tel"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           id="phone"
                                           wire:model.lazy="phone"
                                           placeholder="{{ __('contact-us.form.phone_placeholder') }}">
                                    @error('phone')
                                        <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="inquiry_type" class="form-label">
                                        {{ __('contact-us.form.inquiry_type') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('inquiry_type') is-invalid @enderror"
                                            id="inquiry_type"
                                            wire:model.lazy="inquiry_type"
                                            required>
                                        <option value="">{{ __('contact-us.form.select_inquiry_type') }}</option>
                                        @foreach($inquiryTypes as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('inquiry_type')
                                        <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label">
                                    {{ __('contact-us.form.subject') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('subject') is-invalid @enderror"
                                       id="subject"
                                       wire:model.lazy="subject"
                                       placeholder="{{ __('contact-us.form.subject_placeholder') }}"
                                       required>
                                @error('subject')
                                    <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">
                                    {{ __('contact-us.form.message') }} <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('message') is-invalid @enderror"
                                          id="message"
                                          rows="5"
                                          wire:model.lazy="message"
                                          placeholder="{{ __('contact-us.form.message_placeholder') }}"
                                          required></textarea>
                                @error('message')
                                    <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input @error('consent') is-invalid @enderror"
                                       type="checkbox"
                                       id="consent"
                                       wire:model="consent"
                                       required>
                                <label class="form-check-label" for="consent">
                                    {!! __('contact-us.form.consent') !!}
                                </label>
                                @error('consent')
                                    <div class="invalid-feedback d-block" role="alert">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button"
                                        class="btn btn-outline-secondary me-md-2"
                                        wire:click="resetForm">
                                    <i class="bi bi-arrow-clockwise me-1"></i>
                                    {{ __('contact-us.form.reset') }}
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send-fill me-2"></i>
                                    {{ __('contact-us.form.submit') }}
                                </button>
                            </div>

                            <div class="form-text mt-3">
                                <strong>Note:</strong>
                                All fields marked with <span class="text-danger">*</span> are required.
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Map Card --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white p-3">
                        <h4 class="mb-0 text-white d-flex align-items-center">
                            <i class="bi bi-map-fill me-2"></i>
                            {{ __('contact-us.location_title') ?? 'Our Location' }}
                        </h4>
                    </div>
                    <div class="card-body p-0">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15938.09392734158!2d101.671579!3d2.912552!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cdc8e43e1975cf%3A0x4c6c7f50db0e3e9!2sKementerian%20Pelancongan%2C%20Seni%20dan%20Budaya%20Malaysia%20(MOTAC)%2C%20No.%202%2C%20Tower%201%2C%20Jalan%20P5%2F6%2C%20Presint%205%2C%2062200%20Putrajaya%2C%20Wilayah%20Persekutuan%20Putrajaya%2C%20Malaysia!5e0!3m2!1sen!2smy!4v1691224012345!5m2!1sen!2smy"
                            width="100%"
                            height="300"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="MOTAC Office Location Map"
                            aria-label="Interactive map showing the location of MOTAC office in Putrajaya">
                        </iframe>
                    </div>
                    <div class="card-footer p-3">
                        <small class="text-muted">
                            <i class="bi bi-geo-alt me-1"></i>
                            Click the map to get directions to our office
                        </small>
                    </div>
                </div>

                {{-- Back to Dashboard --}}
                <div class="text-center mt-4 mb-3">
                    <a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left-circle-fill me-1"></i>{{ __('app.back_to_dashboard') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
