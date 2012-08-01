<?php
/**
 * Http browser bundle to make http[s] calls from a symfony project.
 *
 * Copyright see LICENCES file.
 *
 * User: michele
 * Date: 29.07.12 14:15
 */
namespace Ironpinguin\BrowserBundle;

use Ironpinguin\BrowserBundle\Adapter\Adapter;

class Client
{
    /**
     * @var Adapter
     */
    protected $_adapter;

    /**
     * @var array
     */
    protected $_config = array(
        'adapter'   => 'Adapter\Http',
        'connect_timeout'   => 10,
        'timeout'           => 0,
        'use_brackets'      => true,
        'protocol_version'  => '1.1',
        'buffer_size'       => 16384,
        'store_body'        => true,

        'proxy_host'        => '',
        'proxy_port'        => '',
        'proxy_user'        => '',
        'proxy_password'    => '',
        'proxy_auth_scheme' => self::AUTH_BASIC,
        'proxy_type'        => 'http',

        'ssl_verify_peer'   => true,
        'ssl_verify_host'   => true,
        'ssl_cafile'        => null,
        'ssl_capath'        => null,
        'ssl_local_cert'    => null,
        'ssl_passphrase'    => null,

        'digest_compat_ie'  => false,

        'follow_redirects'  => false,
        'max_redirects'     => 5,
        'strict_redirects'  => false
    );

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->_config = $config;
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
            $adapterInstance = new $adapter();
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
        // TODO: Implement send() method.
    }

}
