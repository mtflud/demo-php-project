<?php

namespace Ktbots\Core\Replies\Repliers;


use BotMan\BotMan\BotMan;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\ListTemplate;
use Illuminate\Support\Collection;
use Ktbots\Core\Replies\RepliesToConversationInterface;

class ReplyWithListTemplate implements RepliesToConversationInterface
{

    /**
     * Handle how to reply to the conversation
     * @param Collection $payload
     * @param BotMan $botMan
     * @return mixed
     */
    public function handle(Collection $payload, BotMan $botMan)
    {
        $listTemplate = ListTemplate::create()
            ->useCompactView();

        // :: Add global title and URL
        if ($payload->has('global_title') && $payload->has('global_url') && filled($payload->get('global_title')) && filled($payload->get('global_url'))) {
            $listTemplate->addGlobalButton(ElementButton::create($payload->get('global_title'))->url($payload->get('global_url')));
        }

        // :: Add elements to template
        $elements = collect($payload->get('elements'));
        foreach ($elements as $element) {

            $element = collect($element);
            $cardElement = Element::create($element->get('title'))
                ->image($element->get('image'));
            if (filled($element->get('subtitle'))) {
                $cardElement->subtitle($element->get('subtitle'));
            }

            $buttons = collect($element->get('buttons'));
            $urlButtons = $buttons->filter(function ($button) {
                return $button['type'] === 'url';
            });
            $postbackButtons = $buttons->filter(function ($button) {
                return $button['type'] === 'postback';
            });
            foreach ($urlButtons as $urlButton) {
                $cardElement->addButton(ElementButton::create($urlButton['title'])
                    ->url($urlButton['payload']));
            }
            foreach ($postbackButtons as $postbackButton) {
                $cardElement->addButton(ElementButton::create($postbackButton['title'])
                    ->type('postback')
                    ->payload($postbackButton['payload']));
            }

            $botManElements[] = $cardElement;

        }
        $listTemplate->addElements($botManElements);
        $botMan->reply($listTemplate);
    }
}