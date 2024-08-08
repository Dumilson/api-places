<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceService
{
    protected $repository;

    public function __construct(Place $place)
    {
        $this->repository = $place;
    }

    public function save(array $data): Place
    {
        $data['slug'] = Str::slug($data['name']);
        $stmt = $this->repository->create($data);

        return $stmt;
    }

    public function update(Place $place, array $data): bool
    {
        $data['slug'] = Str::slug($data['name']);
        return $this->repository->find($place->id)->update($data);
    }

    public function delete(Place $place): bool
    {
        return $this->repository->find($place->id)->delete();
    }

    public function show(Place $place): Object
    {
        return $this->repository->find($place->id)->first() ?? [];
    }


    public function getAllPlaces(Request $filter): Object
    {
        $places = $this->repository->query();
    
        if ($filter->has('name')) {

            $searchTerm = strtolower(preg_replace('/[^a-zA-Z0-9\s]/', '', $filter->query('name')));
    
            $places->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"]);
        }
    
        return $places->get();
    }
    
}
