<?php

namespace yiiunit;

use mcsneaky\moceansms\Message;
use mcsneaky\moceansms\Provider;
use yii\httpclient\Client;
use yii\httpclient\Transport;
use yii\httpclient\Response;
use yii\httpclient\Request;
use Yii;

/**
 *
 */
class BaseProviderTest extends TestCase
{
    public function testSendingMessage()
    {
        $providerMock = $this->getMockBuilder(Provider::className())
            ->setMethods(['getClient'])
            ->setConstructorArgs([[
                'username' => 'testuser',
                'password' => 'testpassword'
            ]])
            ->getMock();

        $transportMock = $this->getMockBuilder(Transport::className())
            ->setMethods(['send'])
            ->getMock();

        $transportMock->expects($this->once())
            ->method('send')
            ->with($this->callback(function ($request) {
                if ($request->data['mocean-text'] !== 'test') {
                    return false;
                }
                if ($request->data['mocean-to'] !== '12345') {
                    return false;
                }
                if ($request->data['mocean-api-key'] !== 'testuser') {
                    return false;
                }
                if ($request->data['mocean-api-secret'] !== 'testpassword') {
                    return false;
                }
                if ($request->data['mocean-from'] !== 'test sender') {
                    return false;
                }

                return true;
            }))->will($this->returnValue(new Response([
                'content' => '{"messages":[{"status":0,"receiver":"60173788399","msgid":"cust20013050311050614001"}]}'
            ])));

        $providerMock->expects($this->any())->method('getClient')->will($this->returnValue(new Client([
            'transport' => $transportMock
        ])));

        $sent = $providerMock->compose('test')->setTo('12345')->setFrom('test sender')->send();

        $this->assertTrue($sent);
    }

    public function testSendingMessageToMultipleRecipients()
    {
        $providerMock = $this->getMockBuilder(Provider::className())
            ->setMethods(['getClient'])
            ->getMock();

        $transportMock = $this->getMockBuilder(Transport::className())
            ->setMethods(['send'])
            ->getMock();
        $transportMock->expects($this->exactly(1))->method('send')->will($this->returnValue(new Response([
            'content' => '{"messages":[{"status":0,"receiver":"60173788399","msgid":"cust20013050311050614001"},{"status":0,"receiver":"60173788399","msgid":"cust20013050311050614001"}]}'
        ])));

        $providerMock->expects($this->any())->method('getClient')->will($this->returnValue(new Client([
            'transport' => $transportMock
        ])));

        $sent = $providerMock->compose('test')->setTo(['12345', '2234'])->send();

        $this->assertTrue($sent);
    }

    /**
     * @dataProvider reportProvider
     */
    public function testFailedSendingMessage($response, $expected)
    {
        $providerMock = $this->getMockBuilder(Provider::className())
                             ->setMethods(['getClient'])
                             ->setConstructorArgs([[
                                'username' => 'testuser',
                                'password' => 'testpassword'
                             ]])
                             ->getMock();

        $transportMock = $this->getMockBuilder(Transport::className())
                              ->setMethods(['send'])
                              ->getMock();

        $transportMock->expects($this->once())
            ->method('send')
            ->with($this->callback(function ($request) {
                if ($request->data['mocean-text'] !== 'test') {
                    return false;
                }
                if ($request->data['mocean-to'] !== '12345') {
                    return false;
                }
                if ($request->data['mocean-api-key'] !== 'testuser') {
                    return false;
                }
                if ($request->data['mocean-api-secret'] !== 'testpassword') {
                    return false;
                }
                if ($request->data['mocean-from'] !== 'test sender') {
                    return false;
                }

                return true;
            }))->will($this->returnValue(new Response([
                'content' => $response
            ])));

        $providerMock->expects($this->any())->method('getClient')->will($this->returnValue(new Client([
            'transport' => $transportMock
        ])));

        $sendResponse = $providerMock->compose('test')->setTo('12345')->setFrom('test sender')->send();

        $this->assertEquals($expected, $sendResponse);
    }

    public function reportProvider()
    {
        return [
            ['{"messages":[{"status":0,"receiver":"60173788399","msgid":"cust20013050311050614001"}]}', true],
            ['{"messages": [{"status": 1,"err_msg": "Authorization failed"}]}', false],
            ['{"messages":[{"status":2,"receiver":"60123456789","err_msg":"Insufficient credits to send sms"}]}', false],
            ['{"messages":[{"status":3,"receiver":"60123456789","err_msg":"No rate found"}]}', false],
        ];
    }

    public function testGetClient()
    {
        $provider = new Provider([
            'apiUrl' => 'test'
        ]);

        $this->assertEquals('test', $provider->getClient()->baseUrl);
    }
}
