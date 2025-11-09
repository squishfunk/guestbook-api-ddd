<?php

namespace App\Post\Domain\ValueObject;

class RegisteredAuthor implements AuthorInterface
{
    use DisplayNameTrait;
    private string $userId;

    public function __construct(string $userId, string $displayName){
        $this->setUserId($userId);
        $this->setDisplayName($displayName);
    }


    public function getUserId(): string
    {
        return $this->userId;
    }

    private function setUserId(string $userId): void
    {
        if(strlen($userId) < 1){
            throw new \DomainException('User ID cannot be empty.');
        }

        $this->userId = $userId;
    }
}

