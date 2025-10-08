<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class GuestbookEntryInput
{
    #[Assert\NotBlank(message: "Name should not be blank")]
    #[Assert\Length(
        max:100,
        maxMessage: "Name should not be more than 100 characters",
    )]
    public string $name;

    #[Assert\NotBlank(message: "Message should not be blank")]
    #[Assert\Length(
        min: 5,
        minMessage: "Message should not be less than 5 characters",
    )]
    public string $message;
}
