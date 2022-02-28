<?php

namespace App\Babel\Extension\noj;

class JudgeClient
{
    private $ch = null;
    private $serverBaseUrl = '';
    private $token;
    private $languageConfigs = [];

    public function __construct($token, $serverBaseUrl)
    {
        $this->serverBaseUrl = rtrim($serverBaseUrl, '/');
        $this->token = hash('sha256', $token);
        $this->languageConfigs = Languages::get();
    }

    public function ping()
    {
        return $this->post($this->serverBaseUrl . '/ping');
    }

    /**
     * Call NOJ Judge Server APIs
     * @param string $src source code to be judged
     * @param string $language language
     * @param string $testCaseId test_case_id
     * @param array $config extra configs
     * @return array
     * @throws Exception
     */
    public function judge($src, $language, $testCaseId, $config = [])
    {
        $languageConfig = $this->getLanguageConfigByLanguage($language);
        if (is_null($languageConfig)) {
            throw new \Exception("don't support \"$language\" language!");
        }
        if ($config['spj_config']) {
            $LanguageConfigSPJ = $this->getLanguageConfigByLanguage($config['spj_config'], true);
            if (is_null($LanguageConfigSPJ)) {
                throw new \Exception("don't support \"{$config['spj_config']}\" language!");
            }
            $config['spj_config'] = $LanguageConfigSPJ['run'];
            $config['spj_compile_config'] = $LanguageConfigSPJ['compile'];
        }
        $default = [
            'language_config' => $languageConfig,
            'src' => $src,
            'test_case_id' => $testCaseId,
            'max_cpu_time' => is_null($languageConfig['compile']) ? null : $languageConfig['compile']['max_cpu_time'],
            'max_memory' =>  is_null($languageConfig['compile']) ? null : $languageConfig['compile']['max_memory'],
            'spj_version' => null,
            'spj_config' => null,
            'spj_compile_config' => null,
            'spj_src' => null,
            'output' => false
        ];
        return $this->post($this->serverBaseUrl . '/judge', array_merge($default, $config));
    }

    public function compileSpj($src, $spjVersion, $spjCompileConfig)
    {
        $data = [
            'src' => $src,
            'spj_version' => $spjVersion,
            'spj_compile_config' => $spjCompileConfig,
        ];
        return $this->post($this->serverBaseUrl . '/compile_spj', $data);
    }

    public function getLanguageConfigByLanguage($language, $spj = false)
    {
        return $this->getLanguageConfigByKey($language . ($spj ? '_lang_config_spj' : '_lang_config'));
    }

    public function getLanguageConfigByKey($key)
    {
        return isset($this->languageConfigs[$key]) ? $this->languageConfigs[$key] : null;
    }

    private function needCreateCurl()
    {
        return is_null($this->ch);
    }

    /**
     * Get curl reousrces
     * @return null|resource
     */
    private function getCurl()
    {
        if ($this->needCreateCurl()) {
            $this->ch = curl_init();
            $defaults = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                // set HTTP request header
                CURLOPT_HTTPHEADER => [
                    'Content-type: application/json',
                    'X-Judge-Server-Token: ' . $this->token
                ],
            ];
            curl_setopt_array($this->ch, $defaults);
        }
        return $this->ch;
    }

    /**
     * Send GET requests
     * @param string $url query url
     * @param array $data query parameters
     * @return array
     */
    private function get($url, $data = [])
    {
        return $this->request('GET', $url, $data);
    }

    /**
     * Send POST requests
     * @param string $url query url
     * @param array $data query parameters
     * @return array
     */
    private function post($url, $data = [])
    {
        return $this->request('POST', $url, $data);
    }

    /**
     * Send HTTP requests
     * @param string $method http method
     * @param string $url query url
     * @param array $data query parameters
     * @return array
     */
    private function request($method, $url, $data = [])
    {
        $ch = $this->getCurl();
        $method = strtoupper($method);
        if (in_array($method, ['GET', 'HEAD', 'DELETE', 'POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, empty($data) ? '{}' : json_encode($data));
        if (!$result = curl_exec($this->ch)) {
            trigger_error(curl_error($this->ch));
        }
        return json_decode($result, true);
    }

    /**
     * Close curl resources
     */
    public function close()
    {
        if (is_resource($this->ch)) {
            curl_close($this->ch);
        }
        $this->ch = null;
    }

    public function __destruct()
    {
        $this->close();
    }
}
