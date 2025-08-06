{{-- resources/views/components/authentication-card.blade.php --}}
<div class="container">
  <div class="row justify-content-center align-items-center min-vh-100 py-4">
    <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5">
      <div class="text-center mb-4">
        {{ $logo }}
      </div>
      <div class="card shadow-lg motac-auth-card">
        <div class="card-body p-4 p-sm-5">
            {{ $slot }}
        </div>
      </div>
    </div>
  </div>
</div>
