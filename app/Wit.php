<?php

namespace App;

use Jeylabs\Wit\Laravel\Facades\Wit as BaseWit;

class Wit extends BaseWit
{
    const WIT_API_VERSION = '20190715';

    protected function makeRequest($method, $uri, $query = [], $data = [])
    {
        $query = array_merge($query, ['v' => static::WIT_API_VERSION]);

        return parent::makeRequest($method, $uri, $query, $data);
    }
}
