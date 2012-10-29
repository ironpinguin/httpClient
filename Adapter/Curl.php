<?php
/**
 * Http browser bundle to make http[s] calls from a symfony project.
 *
 * Copyright see LICENCES file.
 *
 * User: michele
 * Date: 29.07.12 14:29
 */
namespace Ironpinguin\HttpClientBundle\Adapter;

use Ironpinguin\HttpClientBundle;
use Ironpinguin\HttpClientBundle\Message\Request;
use Ironpinguin\HttpClientBundle\Message\Response;

class Curl extends Adapter
{
    private $_curl;

    /**
     * @param array $options
     * @throws \Exception
     */
    public function __construct(array $options)
    {
        $this->_options = $options;

        $this->_checkOptions();

        if (!extension_loaded('curl')) {
            throw new \Exception("Curl extension not loaded!");
        }

        $this->_curl = curl_init();
        if ($this->_curl === false)
        {
            throw new \Exception("Curl init failed with ".curl_error(($this->_curl)));
        }



    }

    /**
     * @param \Ironpinguin\HttpClientBundle\Message\Request $request
     * @throws \Exception
     * @return mixed|void
     */
    public function send(Request $request = null)
    {
        if ($request == null)
        {
            if ($this->_request == null)
            {
                throw new \Exception("No Request given!");
            }
        } else {
            $this->_request = $request;
        }

        $this->_setSendOptions();

        $data = curl_exec($this->_curl);
        if (false === $data)
        {
            $errorMsg = curl_error($this->_curl);
            $errorNo  = curl_errno($this->_curl);

            throw new \Exception("Curl Error $errorNo: $errorMsg");
        }
        $data = $this->_getLastResponse($data);
        $responseData = $this->_splitResponseData($data);
        $this->_request = new Response();
        $this->_response->setHeaders($responseData['headers']);
        $this->_response->setStatusCode($responseData['code']);
        $this->_response->setStatusMessage($responseData['responsePhrase']);
        $this->_response->setRawContent($responseData['content']);
        $this->_response->setHttpVersion($responseData['httpVersion']);

        $this->_setCurlOptions();

        return $this->_response;
    }

    private function _setSendOptions()
    {
        curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST, $this->_request->getMethod());
        curl_setopt($this->_curl, CURLOPT_URL, $this->_request->getUri());
        curl_setopt($this->_curl, CURLOPT_HTTPHEADER, $this->_request->getHeadersToSend());
        curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $this->_request->getRawContent());
    }

    private function _checkOptions()
    {
        $requiredOptions = array(
            'bufferSize' => 'int',
            'connectionTimeout' => 'int',
            'protocolVersion' => 'string',
            'timeout' => 'int',
            'maxRedirects' => 'int',
            'proxyHost'        => 'string',
            'proxyPort'        => 'int',
            'proxyUser'        => 'string',
            'proxyPassword'    => 'string',
            'proxyAuthScheme' => 'string',
            'proxyType'        => 'string',
            'sslVerifyPeer'   => 'bool',
            'sslVerifyHost'   => 'bool',
            'followRedirects' => 'bool',
            //'strictRedirects' => 'bool',
            //'digestCompatIe'  => 'bool',
        );

        foreach($requiredOptions as $name => $type)
        {
            if (array_key_exists($name, $this->_options))
            {
                switch ($type)
                {
                    case 'int':
                        if(!is_integer($this->_options[$name]))
                        {
                            throw new \Exception("Option Parameter '$name' must be a Integer!");
                        }
                        break;
                    case 'bool':
                        if (!is_bool($this->_options[$name]))
                        {
                            throw new \Exception("Option Parameter '$name' must be a Boolean!");
                        }
                        break;
                    case 'string':
                        if (!is_string($this->_options[$name]))
                        {
                            throw new \Exception("Option Parameter '$name' must be a String!");
                        }
                    default:
                        throw new \Exception("Option Parameter '$name' is from wrong Type!");
                }
            } else {
                throw new \Exception("Missing required Options Parameter '$name'!");
            }
        }
    }

    private function _getLastResponse($raw)
    {
        $parts = preg_split('/((?:\\r?\\n){2})/', $raw, -1, PREG_SPLIT_DELIM_CAPTURE);
        for ($i = count($parts) - 3; $i >= 0; $i -= 2) {
            if (0 === stripos($parts[$i], 'http')) {
                return implode('', array_slice($parts, $i));
            }
        }

        return $raw;
    }

    private function _splitResponseData($data)
    {
        $result = array('code' => '500',
                        'httpVersion' => '1.0',
                        'responsePhrase' => '',
                        'headers' => array(),
                        'content' => '');

        $headers = array();
        $lines = preg_split('/(\\r?\\n)/', $data, -1, PREG_SPLIT_DELIM_CAPTURE);
        for ($i = 0; $i < count($lines); $i += 2) {
            $line = $lines[$i];
            $eol = isset($lines[$i + 1]) ? $lines[$i + 1] : '';

            if (empty($line)) {
                $result['content'] = implode('', array_slice($lines, $i + 2));
                break;
            } else {
                $headers[] = $line;
            }
        }

        $firstLine = explode(' ', $headers[0]);
        $result['code'] = $firstLine[1];
        $result['httpVersion'] = $firstLine[0];
        list(,, $result['responsePhrase']) = explode(' ', $headers[0], 3);
        array_shift($headers);

        foreach($headers as $header)
        {
            list($headerName, $headerValue) = explode(': ', $header);
            $result['headers'][$headerName] = $headerValue;
        }

        return $result;
    }

    private function _setCurlOptions()
    {
        $curlOptsArray = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_BUFFERSIZE     => $this->_options['bufferSize'],
            CURLOPT_CONNECTTIMEOUT => $this->_options['connectionTimeout'],
            CURLOPT_TIMEOUT        => $this->_options['timeout'],
        );
        if ($this->_options['protocolVersion'] == '1.1')
        {
            $curlOptsArray[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        } else {
            $curlOptsArray[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_0;
        }
        $curlOptsArray[CURLOPT_FOLLOWLOCATION] = $this->_options['followRedirects'];
        $curlOptsArray[CURLOPT_MAXREDIRS] = $this->_options['maxRedirects'];

        if ($this->_options['proxyHost'] != '')
        {
            $curlOptsArray[CURLOPT_PROXY] = $this->_options['proxyHost'];
            if ($this->_options['proxyPort'] != 0)
            {
                $curlOptsArray[CURLOPT_PROXYPORT] = $this->_options['proxyPort'];
            }
            switch($this->_options['proxyType'])
            {
                case 'http':
                    $curlOptsArray[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP;
                    break;
                case 'socks5':
                    $curlOptsArray[CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS5;
                    break;
                case 'socks4':
                    $curlOptsArray[CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS4;
                    break;
                default:
                    throw new \Exception("Unknown proxy type ".$this->_options['proxyType']);
            }
            if ($this->_options['proxyUser'] != '' && $this->_options['proxyPassword'] != '')
            {
                $curlOptsArray[CURLOPT_PROXYUSERPWD] = $this->_options['proxyUser'].":".
                    $this->_options['proxyPassword'];
            }
            if ($this->_options['proxyAuthScheme'] == \Ironpinguin\HttpClientBundle\Client::AUTH_BASIC)
            {
                $curlOptsArray[CURLOPT_PROXYAUTH] = CURLAUTH_BASIC;
            }
        }
        $curlOptsArray[CURLOPT_SSL_VERIFYPEER] = $this->_options['sslVerifyPeer'];
        $curlOptsArray[CURLOPT_SSL_VERIFYHOST] = $this->_options['sslVerifyHost'];
        if ($this->_options['sslVerifyHost'])
        {
            $curlOptsArray[CURLOPT_SSL_VERIFYHOST] = 2;
        }

        if (!empty($this->_options['sslLocalCert']))
        {
            $curlOptsArray[CURLOPT_SSLCERT] = $this->_options['sslLocalCert'];
            if (!empty($this->_options['sslPassphrase']))
            {
                $curlOptsArray[CURLOPT_SSLCERTPASSWD] = $this->_options['sslPassphrase'];
            }
        }

        curl_setopt_array($this->_curl, $curlOptsArray);
    }
}
