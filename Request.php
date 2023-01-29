<?php

namespace Qb3ti\ExtendedSymfonyRequest;

use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

use function count;
use function explode;
use function strpos;
use function substr;
use function str_replace;
use function trim;
use function preg_replace;
use function preg_match;

class Request extends HttpFoundationRequest
{
    protected static array $urlPatterns = [];

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        $this->loadParametersFromUrlPattern();
    }

    /**
     * @param string[] $urlPatterns
     */
    public static function setUrlPatterns(array $urlPatterns): void
    {
        static::$urlPatterns = $urlPatterns;
    }

    /**
     * @return string[]
     */
    public static function getUrlPatterns(): array
    {
        return static::$urlPatterns;
    }

    public function loadParametersFromUrlPattern()
    {
        if ($urlPattern = $this->getUrlPatternFromCurrentUrl()) {
            $requestUriParts = explode("/", trim($this->getRequestUri(), "\/"));
            $urlPatternParts = explode("/", trim($urlPattern, "\/"));
            foreach ($urlPatternParts as $key => $value) {
                if(strpos($value, "{{") !== false && strpos($value, "}}") !== false){
                    $this->query->set(substr($value, 2, -2), $requestUriParts[$key]);
                }
            }
        }
    }

    protected function getUrlPatternFromCurrentUrl(): ?string
    {
        if (count(static::$urlPatterns)){
            $matches = [];
            $uri = trim($this->getRequestUri(), "\/");
            
            foreach ($this->getUrlPatterns() as $urlPattern) {
                $urlPattern = trim($urlPattern, "\/");
                
                if ($uri == $urlPattern) {
                    return $urlPattern;
                }

                $pattern = preg_replace("{{[a-zA-Z0-9\_\-\{\}]+}}", "{{}}", $urlPattern);
                $pattern = str_replace("{{","[a-zA-Z0-9\_\-\{\}\.]+", $pattern);
                $pattern = str_replace("/", "\\/", $pattern);
                $pattern = "/^" . $pattern . '$/';
                $pattern = str_replace("}}", "", $pattern);
                
                if (preg_match($pattern, $urlPattern) && preg_match($pattern, $uri)) {
                    $matches[] = $urlPattern;
                }
            }
            
            if (count($matches) > 0) {
                return $matches[0];
            }
    
            return null;
        }
        return null;
    }
}