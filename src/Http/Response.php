<?php

namespace App\Http;

class Response
{
    public function redirection(string $url)
    {
        return header('Location: '.$url);
    }
}
