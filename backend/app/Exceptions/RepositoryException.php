<?php

class RepositoryException extends Exception
{
    public static function entityNotFound(string $domain, string $entity): self
    {
        return new self("Entity {$entity} for domain {$domain} not found.", 404);
    }
}
