<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\MicrosoftTeams\MicrosoftTeamsChannel;
use NotificationChannels\MicrosoftTeams\MicrosoftTeamsMessage;

class RealisticTrafficNotification extends Notification
{
    use Queueable;

    protected $message; // Define the message property

    /**
     * Create a new notification instance.
     *
     * @param string $message The message to be sent via Microsoft Teams
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message; // Set the message property
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [MicrosoftTeamsChannel::class];
    }

    /**
     * Get the Microsoft Teams representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \NotificationChannels\MicrosoftTeams\MicrosoftTeamsMessage
     */
    public function toMicrosoftTeams($notifiable)
    {
        return MicrosoftTeamsMessage::create()
            ->to(config('services.microsoft_teams.realistictraffic_url'))
            ->type('success')
            ->content($this->message); // Use the $message property
    }
}
