<?php

namespace App\Exceptions;

class RepositoryException extends \Exception
{
    public static function entityNotFound(string $domain, string $entity)
    {
        return new self("Entity {$entity} for domain {$domain} not found.", 404);
    }
}
