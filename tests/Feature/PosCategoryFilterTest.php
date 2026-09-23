<?php

use App\Filament\Resources\Sale\Pages\Pos;
use Filament\Schemas\Schema;

it('defines a searchable, live select bound to the filter property', function (string $method, string $property) {
    $page = new Pos;

    $select = $page
        ->{$method}(Schema::make($page))
        ->getFlatComponents()[$property] ?? null;

    expect($select)->not->toBeNull()
        ->and($select->isSearchable())->toBeTrue()
        ->and($select->isLive())->toBeTrue()
        ->and($select->getStatePath(isAbsolute: false))->toBe($property);
})->with([
    'category' => ['categoryFilterForm', 'categoryId'],
    'location' => ['locationFilterForm', 'locationId'],
]);
