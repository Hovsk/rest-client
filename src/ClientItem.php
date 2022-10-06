<?php

namespace Api;

class ClientItem implements ClientInterface
{
    protected array $resources = [];
    protected array $acceptableMethods = ['get', 'post'];
    protected string $name;

    protected ClientInterface $parent;

    public function __construct(ClientInterface $parent, string $name)
    {
        $this->parent = $parent;
        $this->name = $name;
    }

    public function __get($name): self
    {
        if (!isset($this->resources[$name])) {
            $this->resources[$name] = new ClientItem($this, $name);
        }

        return $this->resources[$name];
    }

    public function __call(string $method, array $args): void
    {
        if (!in_array($method, $this->acceptableMethods)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Request method must be one of %s, %s given',
                    implode(',', $this->acceptableMethods),
                    $method
                )
            );
        }

        $params = $args[0] ?? [];
        if (!is_array($params)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Argument 1 passed to %s::%s must be an instance of array, %s given',
                    __CLASS__,
                    __FUNCTION__,
                    gettype($params)
                )
            );
        }

        $this->execute($this->name, $params, $method);
    }

    public function execute(string $uri, array $params = [], string $method = 'get'): void
    {
        if ($uri == '') {
            $uri = $this->name;
        } else {
            if (isset($this->parent->name)) {
                $uri = sprintf('%s/%s', $this->parent->name, $uri);
            }
        }

        $this->parent->execute($uri, $params, $method);
    }
}