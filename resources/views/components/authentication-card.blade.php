{{-- resources/views/components/authentication-card.blade.php --}}
<div class="container">
  <div class="row justify-content-center align-items-center min-vh-100 py-4"> {{-- Added py-4 for some padding on small screens --}}
    <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5"> {{-- Adjusted column classes for better responsiveness --}}
      <div class="text-center mb-4">
        {{ $logo }}
      </div>

      <div class="card shadow-lg motac-auth-card"> {{-- Added motac-auth-card for potential specific styling, increased shadow --}}
        {{-- The slot would typically contain a card-body or specific form structure --}}
        <div class="card-body p-4 p-sm-5"> {{-- Added padding to card-body --}}
            {{ $slot }}
        </div>
      </div>
    </div>
  </div>
</div>
