<?php

namespace Ktbots\Core\Replies\Repliers;


use BotMan\BotMan\BotMan;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\MediaTemplate;
use BotMan\Drivers\Facebook\Extensions\MediaUrlElement;
use Illuminate\Support\Collection;
use Ktbots\Core\Replies\RepliesToConversationInterface;

class ReplyWithMediaTemplate implements RepliesToConversationInterface
{

    /**
     * Handle how to reply to the conversation
     * @param Collection $payload
     * @param BotMan $botMan
     * @return mixed
     * @ToDo: Enable this if its necessary, currently disabled for simplicity purposes
     */
    public function handle(Collection $payload, BotMan $botMan)
    {
        $template = MediaTemplate::create();
        $element = MediaUrlElement::create($payload->get('type'))->url($payload->get('media'));

        // :: Add buttons
        $buttons = collect($payload->get('buttons'));
        $urlButtons = $buttons->filter(function ($button) {
            return $button['type'] === 'url';
        });
        $postbackButtons = $buttons->filter(function ($button) {
            return $button['type'] === 'postback';
        });
        foreach ($urlButtons as $urlButton) {
            $element->addButton(ElementButton::create($urlButton['title'])
                ->url($urlButton['payload']));
        }
        foreach ($postbackButtons as $postbackButton) {
            $element->addButton(ElementButton::create($postbackButton['title'])
                ->type('postback')
                ->payload($postbackButton['payload']));
        }
        $template->element($element);
        $botMan->reply($template);
    }
}