<?php

namespace App\Helpers;

class MenuHelper
{
    public static function getMenuGroups(): array
    {
        return [
            [
                'title' => 'OVERVIEW',
                'items' => [
                    [
                        'name' => 'Dashboard Stok',
                        'path' => '/inventory/dashboard',
                        'icon' => 'bar-chart',
                        'permission' => 'dashboard.view',
                    ],
                ]
            ],
            [
                'title' => 'KONTROL STOK',
                'items' => [
                    [
                        'name' => 'Kartu Stok',
                        'path' => '/inventory/stocks/cards',
                        'icon' => 'file-text',
                        'permission' => 'stocks.view',
                    ],
                    [
                        'name' => 'Monitoring Batch',
                        'path' => '/inventory/stocks/batches',
                        'icon' => 'package',
                        'permission' => 'stocks.view',
                    ],
                    [
                        'name' => 'Optimasi Stok',
                        'path' => '/inventory/thresholds',
                        'icon' => 'activity',
                        'permission' => 'stocks.view',
                    ],
                ]
            ],
            [
                'title' => 'AUDIT & KONTROL INTERNAL',
                'items' => [
                    [
                        'name' => 'Stock Opname',
                        'path' => '/inventory/stock-opnames',
                        'icon' => 'check-square',
                        'permission' => 'stock-opnames.view',
                    ],
                    [
                        'name' => 'Adjustment Stok',
                        'path' => '/inventory/adjustments',
                        'icon' => 'activity',
                        'permission' => 'stock-adjustments.view',
                    ],
                    
                    [
                        'name' => 'Retur Barang',
                        'path' => '/inventory/returns',
                        'icon' => 'rotate-ccw',
                        'permission' => 'returns.view',
                    ],
                    [
                        'name' => 'Pemusnahan (Disposal)',
                        'path' => '/inventory/disposals',
                        'icon' => 'trash',
                        'permission' => 'disposals.view',
                    ],
                ]
            ],
            [
                'title' => 'MASTER DATA',
                'items' => [
                    [
                        'name' => 'Data Master',
                        'icon' => 'database',
                        'permission' => 'master-items.view',
                        'subItems' => [
                            [
                                'name' => 'Kategori Item',
                                'path' => '/master/categories',
                                'permission' => 'master-categories.view',
                            ],
                            [
                                'name' => 'Satuan Item',
                                'path' => '/master/units',
                                'permission' => 'master-items.view',
                            ],
                            [
                                'name' => 'Item (Obat & BMHP)',
                                'path' => '/master/items',
                                'permission' => 'master-items.view',
                            ],
                            [
                                'name' => 'Supplier',
                                'path' => '/master/suppliers',
                                'permission' => 'master-suppliers.view',
                            ],
                            [
                                'name' => 'Gudang/Depo',
                                'path' => '/master/warehouses',
                                'permission' => 'master-warehouses.view',
                            ],
                            [
                                'name' => 'Unit Layanan',
                                'path' => '/master/service-units',
                                'permission' => 'master-warehouses.view',
                            ],
                        ]
                    ],
                    [
                        'name' => 'Pengguna',
                        'icon' => 'users',
                        'permission' => 'master-users.view',
                        'subItems' => [
                            [
                                'name' => 'Manajemen User',
                                'path' => '/master/users',
                                'permission' => 'master-users.view',
                            ],
                            [
                                'name' => 'Role & Permission',
                                'path' => '/master/roles',
                                'permission' => 'master-users.view',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'PENGADAAN',
                'items' => [
                    [
                        'name' => 'E-Catalog Harga',
                        'path' => '/procurement/prices',
                        'icon' => 'tag',
                        'permission' => 'master-items.view',
                    ],
                    [
                        'name' => 'Permintaan',
                        'path' => '/procurement/requests',
                        'icon' => 'file-text',
                        'permission' => 'purchase-requests.view',
                    ],
                    [
                        'name' => 'Approval Direktur',
                        'path' => '/procurement/approvals',
                        'icon' => 'check-square',
                        'permission' => 'purchase-orders.direktur-approve',
                    ],
                    [
                        'name' => 'Pemesanan Barang',
                        'path' => '/procurement/orders',
                        'icon' => 'shopping-cart',
                        'permission' => 'purchase-orders.view',
                    ],
                    [
                        'name' => 'Penerimaan Barang',
                        'path' => '/procurement/receivings',
                        'icon' => 'package',
                        'permission' => 'receivings.view',
                    ],
                    [
                        'name' => 'Distribusi Barang',
                        'path' => '/inventory/distributions',
                        'icon' => 'truck',
                        'permission' => 'distributions.view',
                    ],
                ]
            ],
            [
                'title' => 'KLINIK & PELAYANAN',
                'items' => [
                    [
                        'name' => 'Resep Pasien',
                        'path' => '/clinical/prescriptions',
                        'icon' => 'file-text',
                        'permission' => 'prescriptions.view',
                    ],
                    [
                        'name' => 'Permintaan Ruangan',
                        'path' => '/clinical/ward-requests',
                        'icon' => 'package',
                        'permission' => 'ward-requests.view',
                    ],
                ]
            ],
            [
                'title' => 'AKUNTANSI',
                'items' => [
                    [
                        'name' => 'Bagan Akun (CoA)',
                        'path' => '/accounting/coa',
                        'icon' => 'database',
                        'permission' => 'master-accounts.view',
                    ],
                    [
                        'name' => 'Jurnal Akuntansi',
                        'path' => '/accounting/journals',
                        'icon' => 'file-text',
                        'permission' => 'journals.view',
                    ],
                    [
                        'name' => 'Buku Besar',
                        'path' => '/accounting/reports/general-ledger',
                        'icon' => 'activity',
                        'permission' => 'reports-accounting.view',
                    ],
                    [
                        'name' => 'Neraca Saldo',
                        'path' => '/accounting/reports/trial-balance',
                        'icon' => 'bar-chart',
                        'permission' => 'reports-accounting.view',
                    ],
                ]
            ],
            [
                'title' => 'LAPORAN',
                'items' => [
                    [
                        'name' => 'Laporan Stok',
                        'path' => '/reports/stock',
                        'icon' => 'file-text',
                        'permission' => 'reports-stock.view',
                    ],
                    [
                        'name' => 'Laporan Distribusi',
                        'path' => '/reports/distribution',
                        'icon' => 'file-text',
                        'permission' => 'distributions.view',
                    ],
                ]
            ],
            [
                'title' => 'SISTEM',
                'items' => [
                    [
                        'name' => 'Pengaturan',
                        'path' => '/settings',
                        'icon' => 'settings',
                        'permission' => 'settings.view',
                    ],
                    [
                        'name' => 'Log Audit Sistem',
                        'path' => '/activity-logs',
                        'icon' => 'file-text',
                        'permission' => 'audit-logs.view',
                    ],
                ]
            ],
        ];
    }

    public static function getIconSvg(string $iconName): string
    {
        $icons = [
            'dashboard' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.58333 2.70833C3.54738 2.70833 2.70833 3.54738 2.70833 4.58333V7.49998C2.70833 8.53593 3.54738 9.37498 4.58333 9.37498H7.5C8.53595 9.37498 9.375 8.53593 9.375 7.49998V4.58333C9.375 3.54738 8.53595 2.70833 7.5 2.70833H4.58333ZM3.95833 4.58333C3.95833 4.23816 4.23816 3.95833 4.58333 3.95833H7.5C7.84517 3.95833 8.125 4.23816 8.125 4.58333V7.49998C8.125 7.84515 7.84517 8.12498 7.5 8.12498H4.58333C4.23816 8.12498 3.95833 7.84515 3.95833 7.49998V4.58333ZM4.58333 10.625C3.54738 10.625 2.70833 11.4641 2.70833 12.5V15.4167C2.70833 16.4526 3.54738 17.2917 4.58333 17.2917H7.5C8.53595 17.2917 9.375 16.4526 9.375 15.4167V12.5C9.375 11.4641 8.53595 10.625 7.5 10.625H4.58333ZM3.95833 12.5C3.95833 12.1548 4.23816 11.875 4.58333 11.875H7.5C7.84517 11.875 8.125 12.1548 8.125 12.5V15.4167C8.125 15.7618 7.84517 16.0417 7.5 16.0417H4.58333C4.23816 16.0417 3.95833 15.7618 3.95833 15.4167V12.5ZM10.625 4.58333C10.625 3.54738 11.4641 2.70833 12.5 2.70833H15.4167C16.4526 2.70833 17.2917 3.54738 17.2917 4.58333V7.49998C17.2917 8.53593 16.4526 9.37498 15.4167 9.37498H12.5C11.4641 9.37498 10.625 8.53593 10.625 7.49998V4.58333ZM12.5 3.95833C12.1548 3.95833 11.875 4.23816 11.875 4.58333V7.49998C11.875 7.84515 12.1548 8.12498 12.5 8.12498H15.4167C15.7618 8.12498 16.0417 7.84515 16.0417 7.49998V4.58333C16.0417 4.23816 15.7618 3.95833 15.4167 3.95833H12.5ZM12.5 10.625C11.4641 10.625 10.625 11.4641 10.625 12.5V15.4167C10.625 16.4526 11.4641 17.2917 12.5 17.2917H15.4167C16.4526 17.2917 17.2917 16.4526 17.2917 15.4167V12.5C17.2917 11.4641 16.4526 10.625 15.4167 10.625H12.5ZM11.875 12.5C11.875 12.1548 12.1548 11.875 12.5 11.875H15.4167C15.7618 11.875 16.0417 12.1548 16.0417 12.5V15.4167C16.0417 15.7618 15.7618 16.0417 15.4167 16.0417H12.5C12.1548 16.0417 11.875 15.7618 11.875 15.4167V12.5Z" fill="currentColor"/></svg>',

            'database' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.125 5.625C3.125 7.21178 6.20304 8.125 10 8.125C13.797 8.125 16.875 7.21178 16.875 5.625M3.125 5.625C3.125 4.03822 6.20304 3.125 10 3.125C13.797 3.125 16.875 4.03822 16.875 5.625M3.125 5.625V14.375C3.125 15.9618 6.20304 16.875 10 16.875C13.797 16.875 16.875 15.9618 16.875 14.375V5.625M3.125 10C3.125 11.5868 6.20304 12.5 10 12.5C13.797 12.5 16.875 11.5868 16.875 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'users' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.125 5.625C13.125 7.35089 11.7259 8.75 10 8.75C8.27411 8.75 6.875 7.35089 6.875 5.625C6.875 3.89911 8.27411 2.5 10 2.5C11.7259 2.5 13.125 3.89911 13.125 5.625Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 11.25C6.54822 11.25 3.75 14.0482 3.75 17.5H16.25C16.25 14.0482 13.4518 11.25 10 11.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'settings' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.28033 1.46967C8.57322 1.76256 8.57322 2.23744 8.28033 2.53033L7.81066 3H12.1893L11.7197 2.53033C11.4268 2.23744 11.4268 1.76256 11.7197 1.46967C12.0126 1.17678 12.4874 1.17678 12.7803 1.46967L14.7803 3.46967C15.0732 3.76256 15.0732 4.23744 14.7803 4.53033L12.7803 6.53033C12.4874 6.82322 12.0126 6.82322 11.7197 6.53033C11.4268 6.23744 11.4268 5.76256 11.7197 5.46967L12.1893 5H7.81066L8.28033 5.46967C8.57322 5.76256 8.57322 6.23744 8.28033 6.53033C7.98744 6.82322 7.51256 6.82322 7.21967 6.53033L5.21967 4.53033C4.92678 4.23744 4.92678 3.76256 5.21967 3.46967L7.21967 1.46967C7.51256 1.17678 7.98744 1.17678 8.28033 1.46967ZM3 9.25C3 8.83579 3.33579 8.5 3.75 8.5H16.25C16.6642 8.5 17 8.83579 17 9.25V16.25C17 16.6642 16.6642 17 16.25 17H3.75C3.33579 17 3 16.6642 3 16.25V9.25ZM4.5 10V15.5H15.5V10H4.5Z" fill="currentColor"/></svg>',

            'activity' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.5 10H5.83333L7.91667 5L12.0833 15L14.1667 10H17.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'tag' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>',

            'file-text' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>',

            'check-square' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>',

            'shopping-cart' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>',
            'package' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>',
            'bar-chart' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>',
            'rotate-ccw' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>',
            'truck' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>',
            'trash' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>',
        ];

        return $icons[$iconName] ?? '<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="3" fill="currentColor"/></svg>';
    }
}
