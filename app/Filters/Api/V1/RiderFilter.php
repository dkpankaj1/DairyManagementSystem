<?php

namespace App\Filters\API\V1;
use App\Filters\QueryFilters;
class RiderFilter extends QueryFilters
{
    protected $safeParems = [
        "id"        => ["eq"],
        "name"      => ["eq", "like"],
        'email'     => ["eq", "like"],
    ];
    
}

?>