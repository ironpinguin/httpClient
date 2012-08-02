<?php
/**
 * Http browser bundle to make http[s] calls from a symfony project.
 *
 * Copyright see LICENCES file.
 *
 * User: michele
 * Date: 29.07.12 14:29
 */
namespace Ironpinguin\BrowserBundle\Adapter;

use Ironpinguin\BrowserBundle;
use Ironpinguin\BrowserBundle\Message\Message;
use Ironpinguin\BrowserBundle\Message\Request;
use Ironpinguin\BrowserBundle\Message\Response;

class Curl extends Adapter
{
    private $_curl;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->_curl = curl_init();
        // TODO: Implement __construct() method.
    }

    /**
     * @param \Ironpinguin\BrowserBundle\Message\Request $request
     * @return mixed|void
     */
    public function send(Request $request)
    {
        // TODO: Implement send() method.
    }

}
