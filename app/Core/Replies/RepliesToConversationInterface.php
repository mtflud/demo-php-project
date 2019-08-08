<?php

namespace Ktbots\Core\Replies;


use BotMan\BotMan\BotMan;
use Illuminate\Support\Collection;

interface RepliesToConversationInterface
{
    /**
     * Handle how to reply to the conversation
     * @param Collection $payload
     * @param BotMan $botMan
     * @return mixed
     */
    public function handle(Collection $payload, BotMan $botMan);
}