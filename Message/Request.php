<?php
/**
 * Http browser bundle to make http[s] calls from a symfony project.
 *
 * Copyright see LICENCES file.
 *
 * User: michele
 * Date: 29.07.12 14:27
 */
namespace Ironpinguin\HttpClientBundle\Message;
class Request extends Message
{
    private $_baseUri;

    private $_uri;

    private $_method;

    public function __construct($url = null)
    {
        if (!is_null($url))
        {
            // TODO: Add uri Check.
            $this->_baseUri = $url;
            $this->_uri = $url;
        }
    }

    public function setMethod($method)
    {
        $this->_method = $method;
    }

    public function getMethod()
    {
        return $this->_method;
    }

    public function setUri($uri)
    {
        $this->_uri = $uri;
    }

    public function getUri()
    {
        return $this->_uri;
    }

}
