<?php
namespace Modules\Auth\Dtos;

class RegisterDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}
}