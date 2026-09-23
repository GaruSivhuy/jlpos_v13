<?php

namespace App\Filament;

use App\Filament\Pages\Reports;
use App\Filament\Resources\Control\ChangeProducts\ChangeProductResource;
use App\Filament\Resources\Control\ExchangeMoney\ExchangeMoneyResource;
use App\Filament\Resources\Control\OverMoney\OverMoneyResource;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationManager;

/**
 * Filament always renders ungrouped navigation items before every group.
 * This moves the standalone items listed below so they render right after a given group instead.
 */
class SidebarNavigationManager extends NavigationManager
{
    /**
     * Keys of the ungrouped navigation items that belong after the Sale group.
     *
     * @var array<int, class-string>
     */
    public const array AFTER_SALE_GROUP = [
        ExchangeMoneyResource::class,
        ChangeProductResource::class,
        OverMoneyResource::class,
    ];

    /**
     * Keys of the ungrouped navigation items that belong after the Control group.
     *
     * @var array<int, class-string>
     */
    public const array AFTER_CONTROL_GROUP = [
        Reports::class,
    ];

    /**
     * @return array<NavigationGroup>
     */
    public function get(): array
    {
        $groups = array_values(parent::get());

        $groups = $this->moveStandaloneItemsAfterGroup($groups, __('global.sale'), self::AFTER_SALE_GROUP);
        $groups = $this->moveStandaloneItemsAfterGroup($groups, __('global.control'), self::AFTER_CONTROL_GROUP);

        return array_values(array_filter($groups, fn (NavigationGroup $group): bool => filled($group->getItems())));
    }

    /**
     * @param  array<NavigationGroup>  $groups
     * @param  array<int, class-string>  $itemKeys
     * @return array<NavigationGroup>
     */
    protected function moveStandaloneItemsAfterGroup(array $groups, string $targetLabel, array $itemKeys): array
    {
        $standaloneGroup = collect($groups)->first(fn (NavigationGroup $group): bool => blank($group->getLabel()));
        $targetGroupIndex = collect($groups)->search(fn (NavigationGroup $group): bool => $group->getLabel() === $targetLabel);

        if (! $standaloneGroup || $targetGroupIndex === false) {
            return $groups;
        }

        [$movedItems, $remainingItems] = collect($standaloneGroup->getItems())
            ->partition(fn (NavigationItem $item): bool => in_array($item->getKey(), $itemKeys, true));

        if ($movedItems->isEmpty()) {
            return $groups;
        }

        $standaloneGroup->items($remainingItems->values());

        array_splice($groups, $targetGroupIndex + 1, 0, [NavigationGroup::make()->items($movedItems->values())]);

        return $groups;
    }
}
