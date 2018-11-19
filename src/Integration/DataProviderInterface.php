<?php
namespace sky\Integration;

interface DataProviderInterface
{
    public function get(array $request): array;
}
