<?php

namespace Ktbots\Core\Replies\Repliers;


use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Support\Collection;
use Ktbots\Core\Replies\RepliesToConversationInterface;

class ReplyWithQuestion implements RepliesToConversationInterface
{

    /**
     * Handle how to reply to the conversation
     * @param Collection $payload
     * @param BotMan $botMan
     * @return mixed
     */
    public function handle(Collection $payload, BotMan $botMan)
    {
        $question = Question::create($payload->get('question'));
        $buttons = $payload->get('buttons');
        $botButtons = array();
        foreach ($buttons as $button) {
            $button = collect($button);
            $btn = Button::create($button->get('title'))
                ->value($button->get('payload'));
            $additionalParameters = ['payload' => $button->get('payload')];
            if ($button->has('type') && $button->get('type') === 'location') {
                $additionalParameters['content_type'] = 'location';
            }
            $btn->additionalParameters($additionalParameters);
            $botButtons[] = $btn;
        }
        $question->addButtons($botButtons);

        $botMan->ask($question, function (Answer $answer) {
            // ;; Intentionally left blank
        });
    }
}