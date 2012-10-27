<?php
/**
 * Http browser bundle to make http[s] calls from a symfony project.
 *
 * Copyright see LICENCES file.
 *
 * User: michele
 * Date: 29.07.12 14:23
 */
namespace Ironpinguin\HttpClient\Adapter;

use Ironpinguin\HttpClient\Message\Message;
use Ironpinguin\HttpClient\Message\Request;
use Ironpinguin\HttpClient\Message\Response;

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
     * @param Request $request
     * @return mixed
     */
    abstract function send(Request $request = null);

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->_request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->_response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->_response;
    }
}
