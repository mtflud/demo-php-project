<?php

namespace Ktbots\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Storages\Drivers\FileStorage;
use Exception;
use Illuminate\Support\Facades\Log;
use Ktbots\Bot;
use Ktbots\Core\Routers\ConversationRouter;
use Ktbots\Middleware\KtbotsMiddleware;


class BotManController extends Controller
{
    /**
     * Create a dynamic botman instance using saved credentials from binded route model
     * For example: /bots/{bot}/webhooks
     * @param Bot $bot
     */
    public function handle(Bot $bot)
    {
        // :: Create dynamic BotMan instance
        $storage = new FileStorage(storage_path('botman'));
        $botman = BotManFactory::create([$bot->provider => $bot->credentials], null, request(), $storage);

        // :: Apply custom KTBOts Middleware
        $middleware = new KtbotsMiddleware();
        $botman->middleware->received($middleware);

        // :: Global listener for client messages
        $botman->hears('(.*)', function (BotMan $botManInstance, $message) use ($bot) {
            // :: Build router to route client's request
            try {
                $conversationRouter = new ConversationRouter($botManInstance, $bot, $message);
                return $conversationRouter->route();
            } catch (Exception $e) {
                // :: We don't want to block the bot's conversation, so we'll just log the error and return an empty response
                Log::error($e->getMessage());
                return response();
            }
        });

        // :: Start listening for client's input
        $botman->listen();
    }
}
