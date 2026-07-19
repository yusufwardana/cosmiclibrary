<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\NotificationTemplate;
use App\Models\User;
use App\Notifications\GenericNotification;
use Illuminate\Support\Facades\Notification;

class NotificationEngine extends BaseService
{
    public function name(): string
    {
        return 'notification';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function getTemplate(string $slug): ?NotificationTemplate
    {
        return NotificationTemplate::where('slug', $slug)->where('is_active', true)->first();
    }

    public function defineTemplate(string $slug, array $data): NotificationTemplate
    {
        $template = NotificationTemplate::updateOrCreate(
            ['slug' => $slug],
            [
                'title' => $data['title'] ?? null,
                'subject' => $data['subject'] ?? null,
                'body' => $data['body'] ?? null,
                'channel' => $data['channel'] ?? 'database',
                'is_active' => $data['is_active'] ?? true,
            ]
        );

        return $template;
    }

    public function send(string $slug, User $user, array $data = []): void
    {
        $template = $this->getTemplate($slug);

        if (! $template) {
            return;
        }

        $channels = collect(explode(',', $template->channel))
            ->map(fn ($ch) => trim($ch))
            ->filter()
            ->values()
            ->toArray();

        $renderedSubject = $this->renderTemplate($template->subject, $data);
        $renderedBody = $this->renderTemplate($template->body, $data);

        Notification::send(
            $user,
            new GenericNotification(
                subject: $renderedSubject,
                body: $renderedBody,
                channels: $channels,
                data: $data,
            )
        );
    }

    public function sendToAdmins(string $slug, array $data = []): void
    {
        $admins = User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->get();

        foreach ($admins as $admin) {
            $this->send($slug, $admin, $data);
        }
    }

    public function renderTemplate(?string $template, array $data): string
    {
        if (! $template) {
            return '';
        }

        return preg_replace_callback('/\{(\w+)\}/', function ($matches) use ($data) {
            return $data[$matches[1]] ?? $matches[0];
        }, $template);
    }
}