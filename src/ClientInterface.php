<?php

namespace Api;

interface ClientInterface
{
    public function __get($name): ClientInterface;
}