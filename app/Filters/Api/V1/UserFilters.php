<?php

namespace App\Filters\Api\V1;
use App\Filters\QueryFilters;


class UserFilters extends QueryFilters
{
    protected $safeParems = [
        "id"        => ["eq"],
        "name"      => ["eq", "like"],
        'email'     => ["eq", "like"],
    ];
    
}

?>