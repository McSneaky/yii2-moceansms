<?php

namespace mcsneaky\moceansms;

use Yii;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\helpers\ArrayHelper;
use mikk150\sms\BaseMessage;
use mikk150\sms\BaseProvider;

/**
 *
 */
class Provider extends BaseProvider
{
    const RESPONSE_OK_START = 'OK';

    /** @var  string API username */
    public $username;

    /** @var  string API password */
    public $password;

    /** @var  string messente API URL */
    public $apiUrl = 'https://rest-api.moceansms.com/rest/1/sms/';

    /**
     * @var string the default class name of the new message instances created by [[createMessage()]]
     */
    public $messageClass = 'mcsneaky\moceansms\Message';

    private $_client;

    /**
     * @return     Client  The client.
     */
    public function getClient()
    {
        if (!$this->_client) {
            $this->_client =  new Client([
                'baseUrl' => $this->apiUrl
            ]);
        }

        return $this->_client;
    }

    /**
     * @inheritdoc
     */
    protected function sendMessage($message)
    {
        // Create request to send
        $recipients = implode(',', (array) $message->getTo());
        $request = $this->getClient()->post('send_sms', [
            'mocean-api-key' => $this->username,
            'mocean-api-secret' => $this->password,
            'mocean-from' => $message->getFrom(),
            'mocean-to' => $recipients,
            'mocean-text' => $message->getBody(),
        ])->setFormat(Client::FORMAT_URLENCODED);

        // Send request
        $response = $this->getClient()->send($request);

        // Read messages out of response
        $response = Json::decode($response->content);
        $responseMessages = ArrayHelper::getValue($response, 'messages');

        // Check if all send messages where OK
        $messagesSent = true;
        foreach ($responseMessages as $responseMessage) {
            $status = ArrayHelper::getValue($responseMessage, 'status');
            if ($status != 0) {
                $messagesSent = false;
                $error = ArrayHelper::getValue($responseMessage, 'err_msg');
                $reciver = ArrayHelper::getValue($responseMessage, 'receiver');
                // Log error
                Yii::error('Error "' . $error . '" sending SMS "' . $message->getBody() . '" to "' . $reciver . '"', 'messente');
            }
        }

        return $messagesSent;
    }
}
