<?php

namespace App\Services;

use App\Models\Distribution;
use App\Models\ItemBatch;
use App\Models\PurchaseRequest;
use App\Models\StockOpname;
use App\Models\User;
use Carbon\Carbon;

/**
 * Computes real, actionable notifications for the logged-in user on the
 * fly (no persisted table) — always reflects live data, scoped to what
 * that user actually has permission to act on.
 */
class NotificationService
{
    public function forUser(User $user, int $limit = 8): array
    {
        $items = [];

        if ($user->can('purchase-requests.approve')) {
            $items = array_merge($items, PurchaseRequest::where('status', 'submitted')
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn ($pr) => [
                    'title' => 'Permintaan pembelian menunggu persetujuan',
                    'subtitle' => $pr->request_number,
                    'time' => $pr->created_at,
                    'url' => route('procurement.approvals.index'),
                    'icon' => 'file-text',
                    'color' => 'amber',
                ])->all());
        }

        if ($user->can('distributions.receive') && $user->warehouse_id) {
            $items = array_merge($items, Distribution::where('status', 'sent')
                ->where('destination_warehouse_id', $user->warehouse_id)
                ->latest('sent_at')
                ->limit(5)
                ->get()
                ->map(fn ($d) => [
                    'title' => 'Barang dikirim, menunggu konfirmasi terima',
                    'subtitle' => $d->distribution_number,
                    'time' => $d->sent_at ?? $d->updated_at,
                    'url' => route('inventory.distributions.receive', $d->id),
                    'icon' => 'package',
                    'color' => 'blue',
                ])->all());
        }

        if ($user->can('stock-opnames.review') || $user->can('stock-opnames.approve')) {
            $items = array_merge($items, StockOpname::where('status', 'submitted')
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn ($so) => [
                    'title' => 'Stock opname menunggu review',
                    'subtitle' => $so->opname_number ?? ('Opname #' . $so->id),
                    'time' => $so->updated_at,
                    'url' => route('inventory.stock-opnames.index'),
                    'icon' => 'check-circle',
                    'color' => 'indigo',
                ])->all());
        }

        if ($user->can('stocks.view')) {
            $expiringQuery = ItemBatch::with('item')
                ->where('is_active', true)
                ->where('current_qty', '>', 0)
                ->whereBetween('expired_date', [Carbon::now(), Carbon::now()->addDays(30)]);

            if ($user->warehouse_id) {
                $expiringQuery->where('warehouse_id', $user->warehouse_id);
            }

            $items = array_merge($items, $expiringQuery->orderBy('expired_date')
                ->limit(3)
                ->get()
                ->map(fn ($b) => [
                    'title' => ($b->item->name ?? 'Item') . ' akan kadaluarsa',
                    'subtitle' => 'Dalam ' . (int) Carbon::now()->diffInDays($b->expired_date, false) . ' hari — batch ' . $b->batch_number,
                    'time' => $b->updated_at,
                    'url' => route('inventory.dashboard'),
                    'icon' => 'alert-triangle',
                    'color' => 'red',
                ])->all());
        }

        usort($items, fn ($a, $b) => $b['time'] <=> $a['time']);

        return array_slice($items, 0, $limit);
    }
}
