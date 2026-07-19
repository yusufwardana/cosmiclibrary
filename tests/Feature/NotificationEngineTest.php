<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\NotificationTemplate;
use App\Models\Role;
use App\Models\User;
use App\Services\NotificationEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationEngineTest extends TestCase
{
    use RefreshDatabase;

    private NotificationEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new NotificationEngine;
    }

    public function test_name_returns_notification(): void
    {
        $this->assertSame('notification', $this->engine->name());
    }

    public function test_version_returns_1_0_0(): void
    {
        $this->assertSame('1.0.0', $this->engine->version());
    }

    public function test_get_template_returns_null_when_not_found(): void
    {
        $this->assertNull($this->engine->getTemplate('nonexistent'));
    }

    public function test_get_template_returns_active_template(): void
    {
        $template = NotificationTemplate::factory()->create([
            'slug' => 'welcome',
            'is_active' => true,
        ]);

        $result = $this->engine->getTemplate('welcome');

        $this->assertNotNull($result);
        $this->assertSame('welcome', $result->slug);
    }

    public function test_get_template_returns_null_for_inactive_template(): void
    {
        NotificationTemplate::factory()->create([
            'slug' => 'disabled',
            'is_active' => false,
        ]);

        $this->assertNull($this->engine->getTemplate('disabled'));
    }

    public function test_define_template_creates_new(): void
    {
        $template = $this->engine->defineTemplate('alert', [
            'title' => 'Peringatan',
            'subject' => 'Subjek Peringatan',
            'body' => 'Isi {name}',
            'channel' => 'database,mail',
        ]);

        $this->assertSame('alert', $template->slug);
        $this->assertSame('Peringatan', $template->title);
        $this->assertSame('database,mail', $template->channel);
    }

    public function test_define_template_updates_existing(): void
    {
        $existing = NotificationTemplate::factory()->create(['slug' => 'alert', 'title' => 'Lama']);
        $template = $this->engine->defineTemplate('alert', ['title' => 'Baru']);

        $this->assertSame('Baru', $template->fresh()->title);
        $this->assertSame($existing->id, $template->id);
    }

    public function test_render_template_replaces_variables(): void
    {
        $result = $this->engine->renderTemplate('Halo {name}, selamat {event}', ['name' => 'Andi', 'event' => 'datang']);

        $this->assertSame('Halo Andi, selamat datang', $result);
    }

    public function test_render_template_returns_empty_for_null(): void
    {
        $this->assertSame('', $this->engine->renderTemplate(null, []));
    }

    public function test_send_dispatches_notification(): void
    {
        Notification::fake();

        $template = NotificationTemplate::factory()->create([
            'slug' => 'greet',
            'subject' => 'Halo {name}',
            'body' => 'Selamat datang {name}',
            'channel' => 'database',
        ]);

        $user = User::factory()->create();

        $this->engine->send('greet', $user, ['name' => 'Andi']);

        Notification::assertSentTo($user, \App\Notifications\GenericNotification::class, function ($notification) {
            return $notification->subject === 'Halo Andi' && $notification->body === 'Selamat datang Andi';
        });
    }

    public function test_send_skips_when_template_not_found(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->engine->send('missing', $user, []);

        Notification::assertNothingSent();
    }

    public function test_send_skips_when_template_inactive(): void
    {
        Notification::fake();

        NotificationTemplate::factory()->create([
            'slug' => 'off',
            'is_active' => false,
        ]);

        $user = User::factory()->create();

        $this->engine->send('off', $user, []);

        Notification::assertNothingSent();
    }

    public function test_send_to_admins_sends_to_all_admins(): void
    {
        Notification::fake();

        NotificationTemplate::factory()->create([
            'slug' => 'admin_alert',
            'subject' => 'Alert',
            'body' => 'Body',
            'channel' => 'database',
        ]);

        $adminRole = Role::factory()->create(['name' => 'admin']);
        $admin1 = User::factory()->create();
        $admin2 = User::factory()->create();
        $member = User::factory()->create();

        $admin1->roles()->attach($adminRole);
        $admin2->roles()->attach($adminRole);
        $member->roles()->attach(Role::factory()->create(['name' => 'member']));

        $this->engine->sendToAdmins('admin_alert', []);

        Notification::assertSentTo($admin1, \App\Notifications\GenericNotification::class);
        Notification::assertSentTo($admin2, \App\Notifications\GenericNotification::class);
        Notification::assertNotSentTo($member, \App\Notifications\GenericNotification::class);
    }

    public function test_notification_uses_multiple_channels(): void
    {
        Notification::fake();

        $template = NotificationTemplate::factory()->create([
            'slug' => 'multi',
            'subject' => 'Subjek',
            'body' => 'Isi',
            'channel' => 'database,mail',
        ]);

        $user = User::factory()->create();

        $this->engine->send('multi', $user, []);

        Notification::assertSentTo($user, \App\Notifications\GenericNotification::class, function ($notification) {
            return in_array('database', $notification->channels)
                && in_array('mail', $notification->channels);
        });
    }
}