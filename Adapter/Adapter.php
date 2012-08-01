<?php
/**
 * Http browser bundle to make http[s] calls from a symfony project.
 *
 * Copyright see LICENCES file.
 *
 * User: michele
 * Date: 29.07.12 14:23
 */
namespace Ironpinguin\BrowserBundle\Adapter;

use Ironpinguin\BrowserBundle\Message\Message;
use Ironpinguin\BrowserBundle\Message\Request;
use Ironpinguin\BrowserBundle\Message\Response;

abstract class Adapter
{
    /**
     * @var Request
     */
    protected $_request;

    /**
     * @var Response
     */
    protected $_response;

    abstract function __construct(array $options);
    /**
     * @abstract
     * @param Message\Request $request
     * @return mixed
     */
    abstract function send(Request $request);

    /**
     * @param Message\Request $request
     */
    public function setRequest(Request $request)
    {
        $this->_request = $request;
    }

    /**
     * @return Message\Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @param Message\Response $response
     */
    public function setResponse(Response $response)
    {
        $this->_response = $response;
    }

    /**
     * @return Message\Response
     */
    public function getResponse()
    {
        return $this->_response;
    }
}
