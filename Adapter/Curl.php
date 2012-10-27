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
use Ironpinguin\HttpClientBundle\Message\Message;
use Ironpinguin\HttpClientBundle\Message\Request;
use Ironpinguin\HttpClientBundle\Message\Response;

class Curl extends Adapter
{
    private $_curl;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->_checkOptions($options);

        if (!extension_loaded('curl')) {
            throw new \Exception("Curl extension not loaded!");
        }

        $this->_curl = curl_init();
        if ($this->_curl === false)
        {
            throw new \Exception("Curl init failed with ".curl_error(($this->_curl)));
        }

        curl_setopt_array($this->_curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_BUFFERSIZE     => $options['bufferSize'],
            CURLOPT_CONNECTTIMEOUT => $options['connectionTimeout'],
            CURLOPT_TIMEOUT        => $options['timeout'],
            CURLOPT_FOLLOWLOCATION => 0 < $options['maxRedirects'],
            CURLOPT_MAXREDIRS      => $options['maxRedirects'],
            CURLOPT_FAILONERROR    => !$options['ignoreErrors'],
        ));
    }

    /**
     * @param \Ironpinguin\HttpClientBundle\Message\Request $request
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
            $request = $this->_request;
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
        $this->_response->setRawBody($responseData['content']);
        $this->_response->setHttpVersion($responseData['httpVersion']);

        return $this->_response;
    }

    private function _setSendOptions()
    {
        curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST, $this->_request->getMethod());
        curl_setopt($this->_curl, CURLOPT_URL, $this->_request->getUri());
        curl_setopt($this->_curl, CURLOPT_HTTPHEADER, $this->_request->getHeadersToSend());
        curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $this->_request->getRawBody());
    }

    private function _checkOptions(array $options)
    {
        $requiredOptions = array('bufferSize' => 'int', 'connectionTimeout' => 'int', 'timeout' => 'int', 'maxRedirects' => 'int', 'ignoreErrors' => 'bool');

        foreach($requiredOptions as $name => $type)
        {
            if (array_key_exists($name, $options))
            {
                switch ($type)
                {
                    case 'int':
                        if(!is_integer($options[$name]))
                        {
                            throw new \Exception("Option Parameter '$name' must be a Integer!");
                        }
                        break;
                    case 'bool':
                        if (!is_bool($options[$name]))
                        {
                            throw new \Exception("Option Parameter '$name' must be a Boolean!");
                        }
                        break;
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
        list(, $result['code']) = explode(' ', $headers[0]);
        list($result['httpVersion']) = explode(' ', $firstLine[0]);
        list(,, $result['responsePhrase']) = explode(' ', $firstLine[2], 3);
        array_shift($headers);

        foreach($headers as $header)
        {
            list($headerName, $headerValue) = explode(': ', $header, 1);
            $result['headers'][$headerName] = $headerValue;
        }

        return $result;
    }

}
