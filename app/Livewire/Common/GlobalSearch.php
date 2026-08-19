<?php

namespace App\Livewire\Common;

use App\Helpers\MenuHelper;
use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Support\Str;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';

    /**
     * Flat, permission-filtered list of every navigable menu link (top-level
     * items and sub-items), built once per request and reused for both the
     * empty-state quick links and the search matching.
     */
    private function navigableMenuItems(): array
    {
        $flat = [];

        foreach (MenuHelper::getMenuGroups() as $group) {
            foreach ($group['items'] as $item) {
                if (isset($item['subItems'])) {
                    foreach ($item['subItems'] as $subItem) {
                        if (isset($subItem['permission']) && ! auth()->user()->can($subItem['permission'])) {
                            continue;
                        }
                        $flat[] = [
                            'name' => $item['name'] . ' — ' . $subItem['name'],
                            'path' => $subItem['path'],
                            'icon' => $item['icon'] ?? 'file-text',
                        ];
                    }
                    continue;
                }

                if (isset($item['permission']) && ! auth()->user()->can($item['permission'])) {
                    continue;
                }

                $flat[] = [
                    'name' => $item['name'],
                    'path' => $item['path'],
                    'icon' => $item['icon'] ?? 'file-text',
                ];
            }
        }

        return $flat;
    }

    public function getResultsProperty(): array
    {
        $term = trim($this->query);

        if ($term === '') {
            // Empty state: a short list of the most commonly used destinations,
            // so the palette is useful the instant it opens (not a blank box).
            $shortcuts = collect($this->navigableMenuItems())
                ->whereIn('path', [
                    '/dashboard',
                    '/inventory/dashboard',
                    '/inventory/stocks/cards',
                    '/master/items',
                    '/procurement/requests',
                    '/inventory/initial-import',
                ])
                ->values()
                ->all();

            return [
                'menu' => $shortcuts,
                'items' => [],
                'suppliers' => [],
            ];
        }

        $menuMatches = collect($this->navigableMenuItems())
            ->filter(fn ($m) => Str::contains(Str::lower($m['name']), Str::lower($term)))
            ->values()
            ->take(6)
            ->all();

        $itemMatches = [];
        if (auth()->user()->can('master-items.view')) {
            $itemMatches = Item::query()
                ->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('code', 'like', "%{$term}%")
                        ->orWhere('generic_name', 'like', "%{$term}%");
                })
                ->limit(5)
                ->get(['id', 'code', 'name'])
                ->map(fn ($item) => [
                    'name' => $item->name,
                    'subtitle' => $item->code,
                    'path' => '/master/items?search=' . urlencode($item->name),
                ])
                ->all();
        }

        $supplierMatches = [];
        if (auth()->user()->can('master-suppliers.view')) {
            $supplierMatches = Supplier::query()
                ->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('code', 'like', "%{$term}%");
                })
                ->limit(4)
                ->get(['id', 'code', 'name'])
                ->map(fn ($supplier) => [
                    'name' => $supplier->name,
                    'subtitle' => $supplier->code,
                    'path' => '/master/suppliers?search=' . urlencode($supplier->name),
                ])
                ->all();
        }

        return [
            'menu' => $menuMatches,
            'items' => $itemMatches,
            'suppliers' => $supplierMatches,
        ];
    }

    public function resetQuery(): void
    {
        $this->query = '';
    }

    public function render()
    {
        return view('livewire.common.global-search');
    }
}
