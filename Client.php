<?php
/**
 * Http browser bundle to make http[s] calls from a symfony project.
 *
 * Copyright see LICENCES file.
 *
 * User: michele
 * Date: 29.07.12 14:15
 */
namespace Ironpinguin\HttpClientBundle;

use Ironpinguin\HttpClientBundle\Adapter\Adapter;

class Client
{

    const AUTH_BASIC  = 'basic';
    const AUTH_DIGEST = 'digest';

    /**
     * @var Adapter
     */
    protected $_adapter;

    /**
     * @var array
     */
    protected $_config = array(
        'adapter'           => 'Adapter\Curl',
        'connectTimeout'   => 10,
        'timeout'           => 0,
        'protocolVersion'  => '1.1',
        'bufferSize'       => 16384,

        'proxyHost'        => '',
        'proxyPort'        => 0,
        'proxyUser'        => '',
        'proxyPassword'    => '',
        'proxyAuthScheme' => self::AUTH_BASIC,
        'proxyType'        => 'http',

        'sslVerifyPeer'   => true,
        'sslVerifyHost'   => true,
        'sslCafile'        => null,
        'sslCapath'        => null,
        'sslLocalCert'    => null,
        'sslPassphrase'    => null,

        'digestCompatIe'  => false,

        'followRedirects'  => false,
        'maxRedirects'     => 5,
        'strictRedirects'  => false
    );

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->_config = $config;
    }

    public function getConfig()
    {
        return $this->_config;
    }

    public function setAdapter($adapter)
    {
        if ($adapter instanceof Adapter)
        {
            $this->_adapter = $adapter;
        }
        elseif (is_string($adapter))
        {
            $this->_config['adapter'] = $adapter;
            $this->_adapter = $this->_loadAdapter($adapter);
        }
        else
        {
            throw new \Exception("No Adapter class or String with adapter name given");
        }
    }

    /**
     * @param string $adapter
     * @throws \Exception
     * @return Adapter
     */
    private function _loadAdapter($adapter)
    {
        try
        {
            $adapterInstance = new $adapter($this->_config);
            if ($adapterInstance instanceof Adapter)
            {
                return $adapterInstance;
            }
            else
            {
                throw new \Exception("Given class {$adapter} is not a adapter class");
            }
        }
        catch (\Exception $e)
        {
            throw new \Exception("Given adapter class {$adapter} not found");
        }
    }

    public function send($request = null)
    {
        if ($request == null)
        {
            //TODO implement a Request object generator
            throw new \Exception("Request generator is not implemented yet!");
        }

        if(!($this->_adapter instanceof Adapter))
        {
            $this->_loadAdapter($this->_config['adapter']);
        }

        $response = $this->_adapter->send($request);

        return $response;
    }

}
