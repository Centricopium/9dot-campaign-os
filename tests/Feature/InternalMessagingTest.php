<?php

namespace Tests\Feature;

use App\Models\Constituency;
use App\Models\InternalConversation;
use App\Models\Permission;
use App\Models\User;
use App\Services\InternalMessagingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InternalMessagingTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_message_reply_and_track_read_status(): void
    {
        $constituency = Constituency::create(['name' => 'Vansda']);
        $sender = User::factory()->create(['constituency_id' => $constituency->id, 'is_active' => true]);
        $recipient = User::factory()->create(['constituency_id' => $constituency->id, 'is_active' => true]);
        $service = app(InternalMessagingService::class);

        $conversation = $service->startConversation($sender, [$recipient->id], 'Booth meeting', 'Meeting at 5 PM.', 'Important');

        $this->assertSame(2, $conversation->participants()->count());
        $this->assertSame(1, $conversation->messages()->count());
        $this->assertSame(1, $conversation->load('participants')->unreadCountFor($recipient));

        $service->markRead($recipient, $conversation);
        $this->assertSame(0, $conversation->load('participants')->unreadCountFor($recipient));

        $service->reply($recipient, $conversation, 'Confirmed.');
        $this->assertSame(2, $conversation->messages()->count());
        $this->assertSame(1, $conversation->load('participants')->unreadCountFor($sender));
    }

    public function test_user_cannot_message_outside_permitted_constituency(): void
    {
        $first = Constituency::create(['name' => 'Vansda']);
        $second = Constituency::create(['name' => 'Dang']);
        $sender = User::factory()->create(['constituency_id' => $first->id, 'is_active' => true]);
        $outsideUser = User::factory()->create(['constituency_id' => $second->id, 'is_active' => true]);

        $this->expectException(ValidationException::class);

        app(InternalMessagingService::class)->startConversation($sender, [$outsideUser->id], 'Restricted', 'This must not be sent.');
    }

    public function test_authorized_user_can_open_internal_message_centre(): void
    {
        foreach (['internal_message.view', 'internal_message.send'] as $name) {
            Permission::create(['name' => $name, 'guard_name' => 'web']);
        }

        $user = User::factory()->create(['is_active' => true]);
        $user->givePermissionTo(['internal_message.view', 'internal_message.send']);

        $this->actingAs($user)->get('/admin/internal-message-centre')->assertOk();
        $this->assertSame(0, InternalConversation::query()->count());
    }

    public function test_dashboard_shows_unread_message_summary_without_exposing_body(): void
    {
        foreach (['dashboard.view', 'internal_message.view'] as $name) {
            Permission::create(['name' => $name, 'guard_name' => 'web']);
        }

        $constituency = Constituency::create(['name' => 'Vansda']);
        $sender = User::factory()->create(['name' => 'Campaign Manager', 'constituency_id' => $constituency->id, 'is_active' => true]);
        $recipient = User::factory()->create(['constituency_id' => $constituency->id, 'is_active' => true]);
        $recipient->givePermissionTo(['dashboard.view', 'internal_message.view']);

        app(InternalMessagingService::class)->startConversation(
            $sender,
            [$recipient->id],
            'Urgent booth coordination',
            'Private operational message body.',
            'Urgent',
        );

        $this->actingAs($recipient)
            ->get('/admin')
            ->assertOk()
            ->assertSee('New Internal Messages')
            ->assertSee('Urgent booth coordination')
            ->assertSee('Campaign Manager')
            ->assertDontSee('Private operational message body.');
    }
}
