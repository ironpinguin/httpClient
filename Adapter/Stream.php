<?php
/**
 * Http browser bundle to make http[s] calls from a symfony project.
 *
 * Copyright see LICENCES file.
 *
 * User: michele
 * Date: 28.10.12 07:36
 */
namespace Ironpinguin\HttpClientBundle\Adapter;

use Ironpinguin\HttpClientBundle;
use Ironpinguin\HttpClientBundle\Message\Request;
use Ironpinguin\HttpClientBundle\Message\Response;

class Stream extends Adapter
{

    function __construct(array $options)
    {
        $this->_options = $options;
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
