<?php

namespace Modules\Document\Enums;

enum DocumentStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case EMBEDDING = 'embedding';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
