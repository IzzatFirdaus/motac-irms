<?php

namespace Tests\Feature\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class HelpdeskUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public'); // Mock the storage
        \App\Models\HelpdeskCategory::factory()->create(['name' => 'General']);
        \App\Models\HelpdeskPriority::factory()->create(['name' => 'Low', 'level' => 1]);
    }

    /** @test */
    public function it_rejects_files_over_max_size_on_ticket_creation()
    {
        $user = User::factory()->create();
        $category = \App\Models\HelpdeskCategory::first();
        $priority = \App\Models\HelpdeskPriority::first();

        // Create a file larger than 2MB (2048 KB)
        $largeFile = UploadedFile::fake()->create('large.pdf', 3000); // 3MB

        Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\TicketForm::class)
            ->set('title', 'Large File Test')
            ->set('description', 'Attempting to upload a large file.')
            ->set('category_id', $category->id)
            ->set('priority_id', $priority->id)
            ->set('attachments', [$largeFile])
            ->call('createTicket')
            ->assertHasErrors(['attachments.0' => 'max']); // Check for max size error on first attachment

        $this->assertDatabaseMissing('helpdesk_tickets', ['title' => 'Large File Test']);
        Storage::disk('public')->assertMissing('helpdesk_attachments/' . $largeFile->hashName());
    }

    /** @test */
    public function it_rejects_unsupported_mime_types_on_ticket_creation()
    {
        $user = User::factory()->create();
        $category = \App\Models\HelpdeskCategory::first();
        $priority = \App\Models\HelpdeskPriority::first();

        // Create an unsupported file type (e.g., .exe)
        $unsupportedFile = UploadedFile::fake()->create('virus.exe', 100);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\TicketForm::class)
            ->set('title', 'Unsupported File Test')
            ->set('description', 'Attempting to upload an unsupported file type.')
            ->set('category_id', $category->id)
            ->set('priority_id', $priority->id)
            ->set('attachments', [$unsupportedFile])
            ->call('createTicket')
            ->assertHasErrors(['attachments.0' => 'mimes']); // Check for mime type error on first attachment

        $this->assertDatabaseMissing('helpdesk_tickets', ['title' => 'Unsupported File Test']);
        Storage::disk('public')->assertMissing('helpdesk_attachments/' . $unsupportedFile->hashName());
    }

    /** @test */
    public function it_rejects_files_over_max_size_on_comment_addition()
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);

        $largeFile = UploadedFile::fake()->create('comment_large.pdf', 3000); // 3MB

        Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\TicketDetail::class, ['ticket' => $ticket])
            ->set('newComment', 'Adding a large attachment.')
            ->set('commentAttachments', [$largeFile])
            ->call('addComment')
            ->assertHasErrors(['commentAttachments.0' => 'max']);

        $this->assertCount(0, $ticket->comments()->where('comment', 'Adding a large attachment.')->get());
        Storage::disk('public')->assertMissing('helpdesk_attachments/' . $largeFile->hashName());
    }
}
