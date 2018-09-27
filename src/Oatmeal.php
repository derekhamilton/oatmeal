<?php
namespace DerekHamilton\Oatmeal;

use DerekHamilton\Oatmeal\Contracts\Oatmeal as OatmealContract;

class Oatmeal implements OatmealContract
{
    private $path;
    private $domain;
    private $secure;
    private $httpOnly;

    public function __construct(array $config = [])
    {
        $this->path     = isset($config['path'])     ? $config['path']     : '';
        $this->domain   = isset($config['domain'])   ? $config['domain']   : '';
        $this->secure   = isset($config['secure'])   ? $config['secure']   : false;
        $this->httpOnly = isset($config['httpOnly']) ? $config['httpOnly'] : true;
    }

    public function get(string $name)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }

    public function pull(string $name)
    {
        $value = $this->get($name);

        if (!is_null($value)) {
            // don't need to forget the cookie if it already doesn't exist
            $this->forget($name);
        }

        return $value;
    }

    public function set(string $name, $value, int $minutes): bool
    {
        $result = setcookie(
            $name,
            $value,
            strtotime("+$minutes"),
            $this->path,
            $this->domain,
            $this->secure,
            $this->httpOnly
        );

        if ($result) {
            $_COOKIE[$name] = $value;
        }

        return $result;
    }

    public function forever(string $name, $value): bool
    {
        // shamelessly stole the 2628000 minutes number from Laravel CookieJar
        // it is exactly 5 years in minutes if we count 365 days even as a year
        return $this->set($name, $value, 2628000);
    }

    public function forget(string $name): OatmealContract
    {
        $this->set($name, false, -1);
        unset($_COOKIE[$name]);
        return $this;
    }

    public function setPath(string $path): OatmealContract
    {
        $this->path = $path;
        return $this;
    }

    public function setDomain(string $domain): OatmealContract
    {
        $this->domain = $domain;
        return $this;
    }

    public function setSecure(bool $secure): OatmealContract
    {
        $this->secure = $secure;
        return $this;
    }

    public function setHttpOnly(bool $httpOnly): OatmealContract
    {
        $this->httpOnly = $httpOnly;
        return $this;
    }
}