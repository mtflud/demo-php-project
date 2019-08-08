<?php

namespace Ktbots\Core\Replies;

use Ktbots\Core\Replies\Exceptions\UnknownReplyStrategyException;
use Ktbots\Core\Replies\Repliers\ReplyWithAudio;
use Ktbots\Core\Replies\Repliers\ReplyWithButtonTemplate;
use Ktbots\Core\Replies\Repliers\ReplyWithFile;
use Ktbots\Core\Replies\Repliers\ReplyWithGenericTemplate;
use Ktbots\Core\Replies\Repliers\ReplyWithListTemplate;
use Ktbots\Core\Replies\Repliers\ReplyWithLocation;
use Ktbots\Core\Replies\Repliers\ReplyWithMediaTemplate;
use Ktbots\Core\Replies\Repliers\ReplyWithQuestion;
use Ktbots\Core\Replies\Repliers\ReplyWithSingleMessage;
use Ktbots\Core\Replies\Repliers\ReplyWithVideo;
use Ktbots\Core\Replies\Repliers\ReplyWithWait;
use Ktbots\Core\Repliess\Repliers\ReplyWithImage;
use Ktbots\Reply;


class ReplyStrategyResolver
{
    /**
     * Get a new instance of the proper strategy to reply to a conversation
     * @param Reply $reply
     * @return ReplyWithAudio|ReplyWithButtonTemplate|ReplyWithFile|ReplyWithGenericTemplate|ReplyWithListTemplate|ReplyWithLocation|ReplyWithMediaTemplate|ReplyWithQuestion|ReplyWithSingleMessage|ReplyWithVideo|ReplyWithWait|ReplyWithImage
     * @throws UnknownReplyStrategyException
     */
    public static function resolve(Reply $reply)
    {
        $type = $reply->getType();
        if ($type === 'single_message') {
            return new ReplyWithSingleMessage();
        }
        if ($type === 'image') {
            return new ReplyWithImage();
        }
        if ($type === 'video') {
            return new ReplyWithVideo();
        }
        if ($type === 'audio') {
            return new ReplyWithAudio();
        }
        if ($type === 'file') {
            return new ReplyWithFile();
        }
        if ($type === 'location') {
            return new ReplyWithLocation();
        }
        if ($type === 'wait') {
            return new ReplyWithWait();
        }
        if ($type === 'button_template') {
            return new ReplyWithButtonTemplate();
        }
        if ($type === 'generic_template') {
            return new ReplyWithGenericTemplate();
        }
        if ($type === 'list_template') {
            return new ReplyWithListTemplate();
        }
        if ($type === 'media_template') {
            return new ReplyWithMediaTemplate();
        }
        if ($type === 'question') {
            return new ReplyWithQuestion();
        }

        throw new UnknownReplyStrategyException('No known strategy for "' . $type . '"');
    }
}