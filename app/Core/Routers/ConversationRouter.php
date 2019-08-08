<?php

namespace Ktbots\Core\Routers;

use BotMan\BotMan\BotMan;
use Ktbots\Bot;
use Ktbots\Core\Factories\HandlerFactory;
use StdClass;

class ConversationRouter
{
    /**
     * @var BotMan
     */
    private $botManInstance;

    /**
     * @var StdClass
     */
    private $payload;

    /**
     * @var Bot
     */
    private $bot;


    /**
     * ConversationHandler constructor.
     * @param BotMan $botManInstance
     * @param Bot $bot
     * @param string $payload
     */
    public function __construct(BotMan $botManInstance, Bot $bot, string $payload)
    {
        $this->botManInstance = $botManInstance;
        $this->payload = $payload;
        $this->bot = $bot;
    }


    /**
     * Route incoming message request to specialized handler
     * @param bool $fromAuth
     * @return mixed
     */
    public function route($fromAuth = false)
    {
        // :: Handle from dynamic handler
        $handlerFactory = new HandlerFactory($this->botManInstance, $this->bot, $this->payload);
        $handler = $handlerFactory->build();

        // :: If a the handler could not be resolved, return to the client with an error message
        if (!$handler) {
            return $this->botManInstance->reply(
                $this->bot->fallback_text ?: "Whoops! We were not able to process your request. Please try again.");
        }

        // :: Else, invoke the handler and let it take over
        $methodName = $handlerFactory->parseData()->method;
        return $handler->$methodName();
    }

}
