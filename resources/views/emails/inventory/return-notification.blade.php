<x-mail::message>
# Pemberitahuan Retur Barang

Halo **{{ $inventoryReturn->supplier->name }}**,

Kami ingin memberitahukan bahwa terdapat transaksi retur barang dengan detail sebagai berikut:

**Nomor Retur:** {{ $inventoryReturn->return_number }}
**Tanggal Retur:** {{ $inventoryReturn->return_date->format('d M Y') }}
**Alasan:** {{ $inventoryReturn->reason_category }} ({{ $inventoryReturn->reason }})

### Daftar Barang
<x-mail::table>
| Barang | Batch | Qty | Harga | Total |
| :--- | :--- | :---: | :--- | :--- |
@foreach($inventoryReturn->details as $detail)
| {{ $detail->item->name }} | {{ $detail->batch->batch_number }} | {{ number_format($detail->qty) }} | {{ number_format($detail->price) }} | {{ number_format($detail->total_value) }} |
@endforeach
</x-mail::table>

**Total Nilai Retur: Rp {{ number_format($inventoryReturn->total_value) }}**

Mohon segera memproses pengambilan barang di gudang kami.

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
