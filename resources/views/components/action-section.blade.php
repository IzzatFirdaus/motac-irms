@php
    use Illuminate\Support\Str;
@endphp
{{--
    resources/views/components/action-section.blade.php
    MYDS-compliant card section for key actions.

    Applies MYDS grid, color tokens, spacing, and typography.
    - Uses MYDS shadow, radius, and spacing for visual hierarchy.
    - Accessible: semantic roles, ARIA labels, heading.
    - Follows MyGOVEA principles: hierarchy, clarity, minimalism, accessibility, and consistent anatomy.
    - See MYDS-Design-Overview.md, prinsip-reka-bentuk-mygovea.md

    Props:
    - $title: string - Section title (required)
    - $description: string - Optional description
    - $content: slot - Main content area

    Usage:
    <x-action-section title="User Settings">
        <x-slot name="description">
            Configure your account preferences here.
        </x-slot>
        <x-slot name="content">
            <!-- Your form or content here -->
        </x-slot>
    </x-action-section>
--}}
<section
    {{ $attributes->merge(['class' => 'myds-card myds-section shadow-card radius-l mb-4']) }}
    role="region"
    aria-labelledby="{{ Str::slug($title, '-') }}-title"
>
    role="region"
    aria-labelledby="{{ Str::slug($title, '-') }}-title"
        <h2 id="{{ Str::slug($title, '-') }}-title"
            class="myds-card-title mb-0 fw-semibold"
            style="font-family: 'Poppins', Arial, sans-serif; font-size:1.25rem; color:var(--myds-primary-700); line-height:1.75rem;">
            {{ $title }}
        </h2>
            style="font-family: 'Poppins', Arial, sans-serif; font-size:1.25rem; color:var(--myds-primary-700); line-height:1.75rem;">
            {{ $title }}
        </h2>
    </header>

    {{-- MYDS Card Body --}}
    <div class="myds-card-body py-4 px-4" style="font-family: 'Inter', Arial, sans-serif;">
        @if (!empty($description))
            <p class="myds-section-desc text-muted small mb-3" style="font-size:0.95em;">
                {{ $description }}
            </p>
        @endif

        {{-- Content Slot --}}
        <div class="myds-section-content">
            {{ $content }}
        </div>
    </div>
</section>

{{--
    === MYDS & MyGOVEA Compliance Notes ===
    - Uses MYDS card anatomy and grid: card, header, body, spacing
    - Title uses MYDS heading typography (Poppins, semibold)
    - Body uses Inter, correct vertical rhythm
    - Section is semantic <section> for accessibility/structure (MyGOVEA Principle 12, 13)
    - ARIA attributes for region and heading
    - Visual separation via MYDS card shadow and radius
    - Description is visually de-emphasized (Principle 5, 9)
    - Consistent spacing and color tokens (MYDS, Principle 6)
--}}
