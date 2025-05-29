<div class="container">
  <div class="row justify-content-center align-items-center min-vh-100"> {{-- Added align-items-center and min-vh-100 for better centering --}}
    <div class="col-sm-12 col-md-8 col-lg-5">
      <div class="text-center mb-4"> {{-- Centered logo --}}
        {{ $logo }}
      </div>

      <div class="card shadow-sm"> {{-- Added shadow for better appearance --}}
        {{-- The slot would typically contain a card-body or specific form structure --}}
        {{ $slot }}
      </div>
    </div>
  </div>
</div>
