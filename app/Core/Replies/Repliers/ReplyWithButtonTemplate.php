<?php

namespace Ktbots\Core\Replies\Repliers;


use BotMan\BotMan\BotMan;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use Illuminate\Support\Collection;
use Ktbots\Core\Replies\RepliesToConversationInterface;

class ReplyWithButtonTemplate implements RepliesToConversationInterface
{

    /**
     * Handle how to reply to the conversation
     * @param Collection $payload
     * @param BotMan $botMan
     * @return mixed
     */
    public function handle(Collection $payload, BotMan $botMan)
    {
        $template = ButtonTemplate::create($payload->get('title'));
        $buttons = collect($payload->get('buttons'));
        $urlButtons = $buttons->filter(function ($button) {
            return $button['type'] === 'url';
        });
        $postbackButtons = $buttons->filter(function ($button) {
            return $button['type'] === 'postback';
        });
        $callButtons = $buttons->filter(function ($button) {
            return $button['type'] === 'phone_number';
        });
        foreach ($urlButtons as $urlButton) {
            $template->addButton(ElementButton::create($urlButton['title'])
                ->url($urlButton['payload']));
        }
        foreach ($callButtons as $callButton) {
            $template->addButton(ElementButton::create($callButton['title'])
                ->type('phone_number')
                ->payload($callButton['payload']));
        }
        foreach ($postbackButtons as $postbackButton) {
            $template->addButton(ElementButton::create($postbackButton['title'])
                ->type('postback')
                ->payload($postbackButton['payload']));
        }
        $botMan->reply($template);
    }
}