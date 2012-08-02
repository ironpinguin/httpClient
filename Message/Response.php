<?php
/**
 * Http browser bundle to make http[s] calls from a symfony project.
 *
 * Copyright see LICENCES file.
 *
 * User: michele
 * Date: 29.07.12 14:27
 */
namespace Ironpinguin\BrowserBundle\Message;

class Response extends Message
{
    private $_statusCode;

    private $_statusMessage;

    public function setStatusCode($status)
    {
        $this->_statusCode = $status;
    }

    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    public function setStatusMessage($statusMessage)
    {
        $this->_statusMessage = $statusMessage;
    }

    public function getStatusMessage()
    {
        return $this->_statusMessage;
    }

}
