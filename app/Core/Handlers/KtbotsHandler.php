<?php

namespace Ktbots\Core\Handlers;


use Ktbots\Core\Replies\Exceptions\UnknownReplyStrategyException;
use Ktbots\Core\Replies\ReplyStrategyResolver;

class KtbotsHandler extends Handler
{
    /**
     * Handle incoming message for Ktbots CMS Conversation
     * @return mixed
     */
    public function handle()
    {
        // :: Search for trigger
        $trigger = $this->bot->triggers()
            ->with('replies')
            ->where('keyword', $this->value)
            ->first();

        // :: Return with fallback text if trigger is not found
        if (!$trigger) {
            return $this->botManInstance->reply($this->bot->fallback_text ?? "An error occurred. Please try again in a little while.");
        }

        // :: Reply with configured replies
        foreach ($trigger->replies as $reply) {

            // :: Get proper strategy for the reply
            try {
                $strategy = ReplyStrategyResolver::resolve($reply);
            } catch (UnknownReplyStrategyException $e) {
                return $this->botManInstance->reply("An error occurred. Please try again in a little while.");
            }

            // :: Handle the task
            $payload = collect($reply->payload);
            $strategy->handle($payload, $this->botManInstance);
        }
    }
}
