<?php
namespace App\Services\Timeline;
use App\Contracts\Services\Timeline\TimelineEventProviderInterface;
use illuminate\Support\Collection;
class TimelineAggregatorService
{
    /*
    @var param mixid $case
    */
    protected $providers =  [];
    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    /*@param mixed $case*/
    public function getTimeLine($case)
    {

$events=collect();

foreach($this->providers as $provider){
    $events=$events->concat($provider->getEvents($case));

    }

    $sorted = $events->sortBy(function ($event) {
    return $event['date'] instanceof \DateTime ? $event['date'] : new \DateTime($event['date']);
    })->values();
    return $sorted->toArray();
    }
}
