<?php

namespace yiiunit;

use mcsneaky\moceansms\Message;
use mcsneaky\moceansms\Provider;
use yii\base\ErrorHandler;
use Yii;

/**
 *
 */
class MessageTest extends TestCase
{
    public function setUp()
    {
        $this->mockApplication([
            'components' => [
                'sms' => $this->createTestSmsComponent(),
            ],
        ]);
    }

    /**
     * @return Provider test sms component instance.
     */
    protected function createTestSmsComponent()
    {
        $component = new Provider();
        return $component;
    }

    public function testMessageTo()
    {
        $message = new Message;

        $message->setTo('12345');

        $this->assertEquals('12345', $message->getTo());
    }

    public function testMessageToAsArray()
    {
        $message = new Message;

        $message->setTo(['12345', '22134']);

        $this->assertEquals(['12345', '22134'], $message->getTo());
    }

    public function testMessageFrom()
    {
        $message = new Message;

        $message->setFrom('Test');

        $this->assertEquals('Test', $message->getFrom());
    }

    public function testMessageBody()
    {
        $message = new Message;

        $message->setBody('Test');

        $this->assertEquals('Test', $message->getBody());
    }

    public function testToString()
    {
        $message = new Message;

        $message->setBody('Test');

        $this->assertEquals((string) $message, $message->toString());
    }
}
