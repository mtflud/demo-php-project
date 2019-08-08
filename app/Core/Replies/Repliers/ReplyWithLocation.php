<?php

namespace Ktbots\Core\Replies\Repliers;


use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Illuminate\Support\Collection;
use Ktbots\Core\Replies\RepliesToConversationInterface;

class ReplyWithLocation implements RepliesToConversationInterface
{

    /**
     * Handle how to reply to the conversation
     * @param Collection $payload
     * @param BotMan $botMan
     * @return mixed
     */
    public function handle(Collection $payload, BotMan $botMan)
    {
        // :: Create attachment
        $attachment = new Location($payload->get('lat'), $payload->get('lng'), [
            'custom_payload' => true,
        ]);

        // :: Build message object
        $message = OutgoingMessage::create($payload->get('title'))
            ->withAttachment($attachment);

        // :: Reply message object
        $botMan->reply($message);
    }
}