<?php

namespace Ktbots\Core\Repliess\Repliers;


use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Illuminate\Support\Collection;
use Ktbots\Core\Replies\RepliesToConversationInterface;

class ReplyWithImage implements RepliesToConversationInterface
{

    /**
     * Handle how to reply to the conversation
     * @param Collection $payload
     * @param BotMan $botMan
     * @return mixed
     */
    public function handle(Collection $payload, BotMan $botMan)
    {
        $attachment = new Image($payload->get('image'), [
            'custom_payload' => true,
        ]);
        $message = OutgoingMessage::create($payload->get('title'))
            ->withAttachment($attachment);
        $botMan->reply($message);
    }
}