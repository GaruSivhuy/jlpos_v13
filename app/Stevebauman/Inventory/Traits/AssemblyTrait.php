<?php

namespace App\Stevebauman\Inventory\Traits;

use App\Stevebauman\Inventory\Exceptions\InvalidPartException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;
use Stevebauman\Inventory\Exceptions\InvalidQuantityException;

/**
 * Class AssemblyTrait.
 */
trait AssemblyTrait
{
    /**
     * The items assembly cache key.
     *
     * @var string
     */
    protected $assemblyCacheKey = 'inventory::assembly.';

    /**
     * The hasMany assemblies relationship.
     *
     * @return BelongsToMany
     */
    abstract public function assemblies();

    /**
     * The belongsToMany recursive assemblies relationship.
     *
     * @return BelongsToMany
     */
    public function assembliesRecursive()
    {
        return $this->assemblies()->with('assembliesRecursive');
    }

    /**
     * Makes the current item an assembly.
     *
     * @return $this
     */
    public function makeAssembly()
    {
        $this->is_assembly = true;

        return $this->save();
    }

    /**
     * Returns true / false if the current item
     * has a cached assembly.
     *
     * @return bool
     */
    public function hasCachedAssemblyItems()
    {
        return Cache::has($this->getAssemblyCacheKey());
    }

    /**
     * Returns the current cached items assembly if
     * it exists inside the cache. Returns false
     * otherwise.
     *
     * @return bool|Collection
     */
    public function getCachedAssemblyItems()
    {
        if ($this->hasCachedAssemblyItems()) {
            return Cache::get($this->getAssemblyCacheKey());
        }

        return false;
    }

    /**
     * Removes the current items assembly items
     * from the cache.
     *
     * @return bool
     */
    public function forgetCachedAssemblyItems()
    {
        return Cache::forget($this->getAssemblyCacheKey());
    }

    /**
     * Returns all of the assemblies items. If recursive
     * is true, the entire nested assemblies collection
     * is returned.
     *
     * @param  bool  $recursive
     * @return Collection
     */
    public function getAssemblyItems($recursive = true)
    {
        if ($recursive) {
            $results = $this->getCachedAssemblyItems();

            if (! $results) {
                $results = $this->assembliesRecursive;

                /*
                 * Cache forever since adding / removing assembly
                 * items will automatically clear this cache
                 */
                Cache::forever($this->getAssemblyCacheKey(), $results);
            }

            return $results;
        }

        return $this->assemblies;
    }

    /**
     * Returns all of the assemblies items in an
     * easy to work with array.
     *
     * @param  bool  $recursive
     * @param  int  $depth
     * @return array
     */
    public function getAssemblyItemsList($recursive = true, $depth = 0)
    {
        $list = [];

        $level = 0;

        $depth++;

        $items = $this->getAssemblyItems();

        foreach ($items as $item) {
            $list[$level] = [
                'id' => $item->getKey(),
                'name' => $item->name,
                'metric_id' => $item->metric_id,
                'category_id' => $item->category_id,
                'quantity' => $item->pivot->quantity,
                'depth' => $depth,
            ];

            if ($item->is_assembly && $recursive) {
                $list[$level]['parts'] = $item->getAssemblyItemsList(true, $depth);
            }

            $level++;
        }

        return $list;
    }

    /**
     * Adds an item to the current assembly.
     *
     * @param  int|float|string  $quantity
     * @return $this
     *
     * @throws InvalidQuantityException
     */
    public function addAssemblyItem(Model $part, $quantity = 1, array $extra = [])
    {
        if ($this->isValidQuantity($quantity)) {
            if (! $this->is_assembly) {
                $this->makeAssembly();
            }

            if ($part->is_assembly) {
                $this->validatePart($part);
            }

            $attributes = array_merge(['quantity' => $quantity], $extra);

            if ($this->assemblies()->save($part, $attributes)) {
                $this->fireEvent('inventory.assembly.part-added', [
                    'item' => $this,
                    'part' => $part,
                ]);

                $this->forgetCachedAssemblyItems();

                return $this;
            }
        }

        return false;
    }

    /**
     * Adds multiple parts to the current items assembly.
     *
     * @param  int|float|string  $quantity
     * @return int
     *
     * @throws InvalidQuantityException
     */
    public function addAssemblyItems(array $parts, $quantity = 1, array $extra = [])
    {
        $count = 0;

        if (count($parts) > 0) {
            foreach ($parts as $part) {
                if ($this->addAssemblyItem($part, $quantity, $extra)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Updates the inserted parts quantity for the current
     * item's assembly.
     *
     * @param  int|string|Model  $part
     * @param  int|float|string  $quantity
     * @return $this|bool
     *
     * @throws InvalidQuantityException
     */
    public function updateAssemblyItem($part, $quantity = 1, array $extra = [])
    {
        if ($this->isValidQuantity($quantity)) {
            $id = $part;

            if ($part instanceof Model) {
                $id = $part->getKey();
            }

            $attributes = array_merge(['quantity' => $quantity], $extra);

            if ($this->assemblies()->updateExistingPivot($id, $attributes)) {
                $this->fireEvent('inventory.assembly.part-updated', [
                    'item' => $this,
                    'part' => $part,
                ]);

                $this->forgetCachedAssemblyItems();

                return $this;
            }
        }

        return false;
    }

    /**
     * Updates multiple parts with the specified quantity.
     *
     * @param  int|float|string  $quantity
     * @return int
     *
     * @throws InvalidQuantityException
     */
    public function updateAssemblyItems(array $parts, $quantity, array $extra = [])
    {
        $count = 0;

        if (count($parts) > 0) {
            foreach ($parts as $part) {
                if ($this->updateAssemblyItem($part, $quantity, $extra)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Removes the specified part from
     * the current items assembly.
     *
     * @param  int|string|Model  $part
     * @return bool
     */
    public function removeAssemblyItem($part)
    {
        if ($this->assemblies()->detach($part)) {
            $this->fireEvent('inventory.assembly.part-removed', [
                'item' => $this,
                'part' => $part,
            ]);

            $this->forgetCachedAssemblyItems();

            return true;
        }

        return false;
    }

    /**
     * Removes multiple parts from the current items assembly.
     *
     *
     * @return int
     */
    public function removeAssemblyItems(array $parts)
    {
        $count = 0;

        if (count($parts) > 0) {
            foreach ($parts as $part) {
                if ($this->removeAssemblyItem($part)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Scopes the current query to only retrieve
     * inventory items that are an assembly.
     *
     *
     * @return mixed
     */
    public function scopeAssembly(Builder $query)
    {
        return $query->where('is_assembly', '=', true);
    }

    /**
     * Validates that the inserted parts assembly
     * does not contain the current item. This
     * prevents infinite recursion.
     *
     *
     * @return bool
     *
     * @throws InvalidPartException
     */
    private function validatePart(Model $part)
    {
        if ((int) $part->getKey() === (int) $this->getKey()) {
            $message = 'An item cannot be an assembly of itself.';

            throw new InvalidPartException($message);
        }

        $list = $part->getAssemblyItemsList();

        array_walk_recursive($list, [$this, 'validatePartAgainstList']);

        return true;
    }

    /**
     * Validates the value and key of the values
     * from the assemblies item list to verify that
     * it does not equal the current items ID.
     *
     * @param  mixed  $value
     * @param  int|string  $key
     *
     * @throws InvalidPartException
     */
    private function validatePartAgainstList($value, $key)
    {
        if ($key === $this->getKeyName()) {
            if ((int) $value === (int) $this->getKey()) {
                $message = 'The inserted part exists inside the assembly tree. An item cannot be an assembly of itself.';

                throw new InvalidPartException($message);
            }
        }
    }

    /**
     * Returns the current items assemblies cache key.
     *
     * @return string
     */
    private function getAssemblyCacheKey()
    {
        return $this->assemblyCacheKey.$this->getKey();
    }
}
