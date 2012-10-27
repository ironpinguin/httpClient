<?php
/**
 * Http browser bundle to make http[s] calls from a symfony project.
 *
 * Copyright see LICENCES file.
 *
 * User: michele
 * Date: 29.07.12 14:31
 */
namespace Ironpinguin\HttpClientBundle\Adapter;

use Ironpinguin\HttpClientBundle;
use Ironpinguin\HttpClientBundle\Message\Message;
use Ironpinguin\HttpClientBundle\Message\Request;
use Ironpinguin\HttpClientBundle\Message\Response;

class Http extends Adapter
{

    function __construct(array $options)
    {
        // TODO: Implement __construct() method.
    }

    /**
     * @param Request $request
     * @return mixed
     */
    function send(Request $request = null)
    {
        // TODO: Implement send() method.
    }
}
