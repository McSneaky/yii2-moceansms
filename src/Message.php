<?php

namespace mcsneaky\moceansms;

/**
 * Test Message class.
 */
class Message extends \mikk150\sms\BaseMessage
{
    private $_from;
    private $_body;
    private $_to;

    /**
     * @inheritdoc
     */
    public function getFrom()
    {
        return $this->_from;
    }

    /**
     * @inheritdoc
     */
    public function setFrom($from)
    {
        $this->_from = $from;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * @inheritdoc
     */
    public function setBody($body)
    {
        $this->_body = $body;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTo()
    {
        return $this->_to;
    }

    /**
     * @inheritdoc
     */
    public function setTo($to)
    {
        $this->_to = $to;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toString()
    {
        $provider = $this->provider;
        $this->provider = null;
        $s = var_export($this, true);
        $this->provider = $provider;
        return $s;
    }
}
