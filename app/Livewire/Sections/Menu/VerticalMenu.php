<?php

namespace App\Livewire\Sections\Menu; // Assuming this is the correct namespace based on your file structure

use App\Models\User; // Ensures the User model is referenced
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class VerticalMenu extends Component
{
  public ?string $role = null;
  public mixed $menuData = []; // This will hold the structured menu data, typically an object or array

  /**
   * The mount method is called when the component is first initialized.
   * It's a good place to set up initial state.
   */
  public function mount(): void
  {
    $this->initializeUserRole();
    $this->loadMenuData();
  }

  /**
   * Determine and set the current authenticated user's role.
   * This relies on the User model having a getRoleNames() method (e.g., from Spatie/laravel-permission).
   */
  protected function initializeUserRole(): void
  {
    /** @var \App\Models\User|null $user */
    $user = Auth::user();

    if ($user && method_exists($user, 'getRoleNames')) {
      // Assuming getRoleNames() returns a collection and we take the first role.
      // Adjust if your role logic is different.
      $this->role = $user->getRoleNames()->first();
    } else {
      $this->role = null; // No user or role method doesn't exist
    }
  }

  /**
   * Load menu data from the Laravel configuration.
   * This assumes that your menu structure (e.g., from verticalMenu.json)
   * is loaded into the 'menu' key in your config files, perhaps by a service provider.
   */
  protected function loadMenuData(): void
  {
    // Fetches the menu configuration. If 'menu' config is not found, defaults to an empty array.
    // The structure of config('menu') should match what verticalMenu.blade.php expects
    // (e.g., an object with a ->menu property that is an array of menu items).
    $this->menuData = config('menu'); // Corrected `verticalMenu.json` should populate this.

    if (is_null($this->menuData)) {
      $this->menuData = []; // Ensure it's an array or empty object if config is null
    }
  }

  /**
   * Render the component's view.
   * This will pass the $role and $menuData to the specified Blade view.
   */
  public function render(): View
  {
    // Ensure the view path matches where your vertical menu Blade file is located.
    // Based on your other files, it might be 'layouts.sections.menu.verticalMenu'
    // or 'livewire.sections.menu.vertical-menu'. Adjust as needed.
    return view('livewire.sections.menu.vertical-menu', [
      'menuData' => $this->menuData,
      'currentUserRole' => $this->role // Pass the role to the view, making it available for logic there
    ]);
  }
}
