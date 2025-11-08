<?php

namespace App\User\Domain\Repository;

use App\User\Domain\Entity\User;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\UserId;

interface UserRepositoryInterface
{
    public function findById(UserId $id): ?User;
    public function findByEmail(Email $email): ?User;

    /**
     * @return list<User>
     */
    public function findAllPaginated(int $page, int $limit): array;
    public function save(User $user): void;
    public function update(User $user): void;
    public function delete(User $user): void;
    public function countAll(): int;
    public function existsByEmail(Email $email): bool;
    public function findByEmailVerificationToken(string $token): ?User;
}
