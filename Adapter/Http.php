<?php
/**
 * Http browser bundle to make http[s] calls from a symfony project.
 *
 * Copyright see LICENCES file.
 *
 * User: michele
 * Date: 29.07.12 14:31
 */
namespace Ironpinguin\BrowserBundle\Adapter;

use Ironpinguin\BrowserBundle;
use Ironpinguin\BrowserBundle\Message\Message;
use Ironpinguin\BrowserBundle\Message\Request;
use Ironpinguin\BrowserBundle\Message\Response;

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
    function send(Request $request)
    {
        // TODO: Implement send() method.
    }
}
