<?php

namespace PaulisRatnieks\ApiKeyAuth\Commands\Enums;

enum UpdateAction: string
{
    case Regenerate = 'regenerate';
    case Revoke = 'revoke';
    case RemoveRevoke = 'remove-revoke';
}
