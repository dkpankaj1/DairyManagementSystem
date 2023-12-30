<?php

namespace App\Filters;
use Illuminate\Http\Request;

class QueryFilters{

    protected  $safeParems = [];
    protected  $columnMap = [];
    protected  $operetorMap = [
        "eq" => "=",
        "ne" => "!=",
        "gt" => "<",
        "gte" => "<=",
        "lt" => ">",
        "lte" => ">=",
        "like" => "like",
    ];

    /**
     * Multiple filter
     */
    public function transform(Request $request)
    {

        $eloQuery = [];

        foreach ($this->safeParems as $param => $operetors) {

            $query = $request->query($param);

            if (!isset($query)) {
                continue;
            }

            $column = $this->columnMap[$param] ?? $param;

            foreach($operetors as $operetor){
                if(isset($query[$operetor])){
                    if($operetor == "like")
                    {
                        $eloQuery[] = [$column, $this->operetorMap[$operetor],"%{$query[ $operetor ]}%"];
                    }
                    else{
                        $eloQuery[] = [$column, $this->operetorMap[$operetor],$query[ $operetor ]];
                    }
                }
            }

        }

        return $eloQuery;

    }
}

?>