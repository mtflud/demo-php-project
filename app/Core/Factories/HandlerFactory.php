<?php

namespace Ktbots\Core\Factories;


use BotMan\BotMan\BotMan;
use Ktbots\Bot;
use stdClass;

class HandlerFactory
{
    /**
     * @var BotMan
     */
    private $botManInstance;

    /**
     * @var Bot
     */
    private $bot;

    /**
     * @var string
     */
    private $payload;

    /**
     * HandlerFactory constructor.
     * @param BotMan $botManInstance
     * @param Bot $bot
     * @param string $payload
     */
    public function __construct(BotMan $botManInstance, Bot $bot, string $payload)
    {
        $this->botManInstance = $botManInstance;
        $this->bot = $bot;
        $this->payload = $payload;
    }

    /**
     * Parse handler data and return it
     * @param $payload
     * @return StdClass
     */
    public function parseData()
    {
        $jsonMessage = json_decode($this->payload);
        // :: Check if handler contains fully qualified namespace
        if (strpos($jsonMessage->handler, '\\') !== false) {
            $parts = explode('\\', $jsonMessage->handler);
            $jsonMessage->handler = $parts[count($parts) - 1];
        }
        $exploded = explode('@', $jsonMessage->handler);
        $obj = new StdClass();
        $obj->class = 'Ktbots\\Core\\Handlers\\' . $exploded[0];
        $obj->method = $exploded[1];
        $obj->value = $jsonMessage->value;
        return $obj;
    }

    /**
     * Whether or not the received handler payload is valid to dynamically assign
     * @return bool
     */
    protected function isValid()
    {
        $jsonMessage = json_decode($this->payload);
        $isValidKtbotsJson = (
            is_object($jsonMessage) &&
            isset($jsonMessage->handler, $jsonMessage->value) &&
            !empty($jsonMessage->handler) &&
            strpos($jsonMessage->handler, '@') !== false
        );
        return $isValidKtbotsJson;
    }

    /**
     * Build the requested handler
     * @return mixed
     */
    public function build()
    {
        if (!$this->isValid()) {
            return false;
        }

        $handlerData = $this->parseData($this->payload);
        $classExists = class_exists($handlerData->class);

        if (!$classExists) {
            return false;
        }

        $handler = new $handlerData->class($this->botManInstance, $this->bot, $handlerData->value);
        $methodExists = method_exists($handler, $handlerData->method);

        if (!$methodExists) {
            return false;
        }

        return $handler;
    }
}