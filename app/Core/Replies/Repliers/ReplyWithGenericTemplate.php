<?php

namespace Ktbots\Core\Replies\Repliers;


use BotMan\BotMan\BotMan;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;
use Illuminate\Support\Collection;
use Ktbots\Core\Replies\RepliesToConversationInterface;

class ReplyWithGenericTemplate implements RepliesToConversationInterface
{

    /**
     * Handle how to reply to the conversation
     * @param Collection $payload
     * @param BotMan $botMan
     * @return mixed
     */
    public function handle(Collection $payload, BotMan $botMan)
    {
        $ratio = ($payload->get('ratio') === 'horizontal') ? GenericTemplate::RATIO_HORIZONTAL : GenericTemplate::RATIO_SQUARE;
        $template = GenericTemplate::create()
            ->addImageAspectRatio($ratio);

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
            $callButtons = $buttons->filter(function ($button) {
                return $button['type'] === 'phone_number';
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
            foreach ($callButtons as $callButton) {
                $cardElement->addButton(ElementButton::create($callButton['title'])
                    ->type('phone_number')
                    ->payload($callButton['payload']));
            }

            $botManElements[] = $cardElement;

        }
        $template->addElements($botManElements);
        $botMan->reply($template);
    }
}