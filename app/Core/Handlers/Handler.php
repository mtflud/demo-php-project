<?php

namespace Ktbots\Core\Handlers;


use BotMan\BotMan\BotMan;
use Ktbots\Bot;
use Ktbots\Trigger;
use ReflectionClass;

abstract class Handler
{
    /**
     * @var BotMan
     */
    public $botManInstance;

    /**
     * @var Trigger
     */
    public $bot;

    /**
     * @var string
     */
    public $value;

    /**
     * Handler constructor.
     * @param BotMan $botManInstance
     * @param Bot $bot
     * @param string $value
     */
    public function __construct(BotMan $botManInstance, Bot $bot, string $value)
    {
        $this->botManInstance = $botManInstance;
        $this->bot = $bot;
        $this->value = $value;
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public function getClassName()
    {
        return (new ReflectionClass($this))->getShortName();
    }

}
