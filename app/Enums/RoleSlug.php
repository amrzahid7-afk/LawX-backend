<?php

namespace App\Enums;

enum RoleSlug: string
{
    case Syndicate = 'syndicate';
    case Lawyer = 'lawyer';
    case Client = 'client';
}
