<?php

namespace Ktbots\Core\Replies\Repliers;


use BotMan\BotMan\BotMan;
use Illuminate\Support\Collection;
use Ktbots\Core\Replies\RepliesToConversationInterface;

class ReplyWithWait implements RepliesToConversationInterface
{

    /**
     * Handle how to reply to the conversation
     * @param Collection $payload
     * @param BotMan $botMan
     * @return mixed
     */
    public function handle(Collection $payload, BotMan $botMan)
    {
        $botMan->typesAndWaits($payload->get('time'));
    }
}