{{-- Most likely, this would be a Livewire component itself, e.g.,
     // app/Livewire/Pages/ComingSoonPage.php
     // resources/views/livewire/pages/coming-soon-page.blade.php
     // And then your route would point to ComingSoonPage::class
--}}
<div>

  @section('title', 'Coming soon!') {{-- This @section('title') works if the layout this extends yields 'title' --}}

  {{-- If this is a full-page Livewire component extending livewire.layouts.app,
       the title is usually set via #[Title('Coming Soon!')] attribute in the component class.
       Or, if commonMaster.blade.php is the direct parent and uses @yield('title'), this is fine.
  --}}

  <div style="text-align: center">
    <div class="container-xxl container-p-y">
      <div class="misc-wrapper">
        <h2 class="mb-1 mx-2">{{ __(('Under Development')) }} 🚀</h2>
        <p class="mb-4 mx-2">{{ __("We're creating something awesome. Please keep calm until it's ready!") }}</p>
        <div class="mt-4">
          <img src="{{ asset('assets/img/illustrations/page-misc-launching-soon.png') }}" width="140"
               alt="page-misc-launching-soon" class="img-fluid">
        </div>
      </div>
    </div>
  </div>
</div>
