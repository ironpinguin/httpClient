<?php
/**
 * Http browser bundle to make http[s] calls from a symfony project.
 *
 * Copyright see LICENCES file.
 *
 * User: michele
 * Date: 29.07.12 14:26
 */
namespace Ironpinguin\BrowserBundle\Message;

abstract class Message
{
    const METHOD_GET = "GET";
    const METHOD_POST = "POST";
    const METHOD_PUT = "PUT";
    const METHOD_HEAD = "HEAD";
    const METHOD_DELETE = "DELETE";
    const METHOD_PATCH = "PATCH";

    private $_header = array();

    private $_rawBody = "";

    public function getHeaders()
    {
        return $this->_header;
    }

    public function getHeader($name)
    {
        $result = null;
        if (array_key_exists($name, $this->_header))
        {
            $result = $this->_header[$name];
        }
        return $result;
    }

    public function getHeadersToSend()
    {
        $headers = array();
        foreach($this->_header as $name => $value)
        {
            $headers[] = "$name: $value";
        }
        return $headers;
    }

    public function setHeaders(array $headers)
    {
        $this->_header = $headers;
    }

    public function setHeader($name, $value, $overwrite = false)
    {
        $result = false;
        if(!array_key_exists($name, $this->_header))
        {
            $this->_header[$name] = $value;
            $result = true;
        }
        elseif ($overwrite)
        {
            $this->_header[$name] = $value;
            $result = true;
        }
        return $result;
    }

    public function getRawBody()
    {
        return $this->_rawBody;
    }

    public function setRawBody($rawBody)
    {
        $this->_rawBody = $rawBody;
    }

}
