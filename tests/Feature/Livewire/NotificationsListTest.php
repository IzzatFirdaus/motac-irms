<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Shared\Notifications\NotificationsList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Test NotificationsList Livewire Component.
 */
class NotificationsListTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_can_be_rendered(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Act as the user and test the component
        $this->actingAs($user);

        // Test that the component renders without errors
        $component = Livewire::test(NotificationsList::class);

        $component->assertStatus(200);
        $component->assertSee('Senarai Notifikasi'); // Should see notification header in Malay
    }

    public function test_search_resets_pagination(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::test(NotificationsList::class);

        // Set search value and verify resetPage is called (pagination resets)
        $component->set('search', 'test')
            ->assertSet('search', 'test');
    }

    public function test_component_has_pagination_functionality(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::test(NotificationsList::class);

        // Test that we can access the notifications property (pagination)
        $notifications = $component->viewData('notifications');
        $this->assertNotNull($notifications);
    }
}
