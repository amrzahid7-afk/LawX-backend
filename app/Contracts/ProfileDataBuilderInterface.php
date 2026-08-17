<?php

namespace App\Contracts;

interface ProfileDataBuilderInterface
{
    public function build($user): array;
}
