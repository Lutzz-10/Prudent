@extends('layouts.app')
@section('title', 'Hasil Scan — Prudent')

@section('content')

<script>
    const scanItems = @json($result['items'] ?? []);
    const scanTotal = {{ intval($result['total'] ?? 0) }};
</script>

<div class="px-5 pt-8 pb-32" x-data="receiptForm()">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('receipt.upload') }}"
           style="color:var(--text-muted); font-size:13px; text-decoration:none;">
            ← Scan ulang
        </a>
        <h1 class="font-display text-2xl mt-2">Hasil Scan</h1>
        <p style="color:var(--text-muted); font-size:13px;">
            Periksa dan koreksi jika ada yang salah.
        </p>
    </div>

    {{-- Foto struk --}}
    <div class="mb-6 rounded-2xl overflow-hidden" style="border:1px solid var(--border);">
        <img src="{{ $receipt->image_url }}" alt="Struk"
             style="width:100%; max-height:200px; object-fit:cover;">
    </div>

    <form method="POST" action="{{ route('receipt.save', $receipt) }}">
        @csrf

        {{-- Info Toko --}}
        <div class="mb-4 p-4 rounded-2xl" style="background:var(--bg-card); border:1px solid var(--border);">
            <p class="label-field">Nama Toko</p>
            <p style="font-size:16px; font-weight:500;">
                {{ $result['store_name'] ?? '—' }}
            </p>
        </div>

        {{-- Daftar Item --}}
        <div class="mb-4">
            <div class="flex items-center justify-between mb-3">
                <p class="label-field mb-0">Item Belanja</p>
                <button type="button" @click="addItem()"
                        style="font-size:12px; color:var(--amber); background:none; border:none; cursor:pointer;">
                    + Tambah Item
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, i) in items" :key="i">
                    <div class="p-4 rounded-2xl" style="background:var(--bg-card); border:1px solid var(--border);">
                        <div class="flex justify-between items-start mb-3">
                            <input type="text" x-model="item.item_name"
                                   :name="`items[${i}][item_name]`"
                                   class="input-field" placeholder="Nama produk"
                                   style="flex:1; margin-right:8px; padding:10px 12px; font-size:14px;">
                            <button type="button" @click="removeItem(i)"
                                    style="background:rgba(239,68,68,0.1); border:none; border-radius:8px;
                                           padding:10px 12px; cursor:pointer; font-size:16px;">
                                🗑️
                            </button>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <p class="label-field" style="font-size:10px;">Qty</p>
                                <input type="number" x-model.number="item.qty"
                                       :name="`items[${i}][qty]`"
                                       @input="updateSubtotal(i)"
                                       class="input-field" min="1"
                                       style="padding:10px 12px; font-size:14px;">
                            </div>
                            <div>
                                <p class="label-field" style="font-size:10px;">Harga</p>
                                <input type="number" x-model.number="item.unit_price"
                                       :name="`items[${i}][unit_price]`"
                                       @input="updateSubtotal(i)"
                                       class="input-field" min="0"
                                       style="padding:10px 12px; font-size:14px;">
                            </div>
                            <div>
                                <p class="label-field" style="font-size:10px;">Subtotal</p>
                                <input type="number" x-model.number="item.subtotal"
                                       :name="`items[${i}][subtotal]`"
                                       @input="recalc()"
                                       class="input-field" min="0"
                                       style="padding:10px 12px; font-size:14px;">
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Total --}}
        <div class="mb-4 p-4 rounded-2xl" style="background:var(--bg-card); border:1px solid var(--border);">
            <div class="flex justify-between mb-2" style="font-size:14px; color:var(--text-muted);">
                <span>Pajak</span>
                <span>Rp {{ number_format($result['tax'] ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between mb-4" style="font-size:14px; color:var(--text-muted);">
                <span>Diskon</span>
                <span style="color:#10B981;">
                    - Rp {{ number_format($result['discount'] ?? 0, 0, ',', '.') }}
                </span>
            </div>
            <div class="flex justify-between items-center pt-3"
                 style="border-top:1px solid var(--border);">
                <span class="font-display text-xl">Total</span>
                <span class="font-display text-2xl" style="color:var(--amber);"
                      x-text="fmt(total)"></span>
            </div>
            <input type="hidden" name="total_amount" :value="total">
        </div>

        {{-- Kategori --}}
        <div class="mb-4">
            <label class="label-field">Kategori</label>
            <div class="grid grid-cols-2 gap-2">
                @foreach($categories as $cat)
                <label style="cursor:pointer;">
                    <input type="radio" name="category_id" value="{{ $cat->id }}" class="hidden">
                    <div onclick="selectCategory(this, {{ $cat->id }})"
                         id="cat-{{ $cat->id }}"
                         style="background:var(--bg-card); border:1px solid var(--border);
                                border-radius:12px; padding:12px; display:flex;
                                align-items:center; gap:8px; font-size:13px;
                                transition: border-color 0.2s, background 0.2s; cursor:pointer;">
                        <span style="font-size:20px;">{{ $cat->icon }}</span>
                        <span>{{ $cat->name }}</span>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Tanggal --}}
<div class="mb-4" x-data="{
    useToday: true,
    todayDate: '{{ now()->toDateString() }}',
    receiptDate: '{{ $result['receipt_date'] ?? now()->toDateString() }}',
    customDate: '{{ now()->toDateString() }}',
    get selectedDate() {
        return this.useToday ? this.todayDate : this.receiptDate;
    }
}">
    <label class="label-field">Tanggal Transaksi</label>

    {{-- 2 Tombol Pilihan --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:12px;">

        {{-- Tombol Hari Ini --}}
        <button type="button" @click="useToday = true"
                :style="useToday
                    ? 'background:var(--amber); color:#0F0F0F; border-color:var(--amber);'
                    : 'background:var(--bg-card); color:var(--text-muted); border-color:var(--border);'"
                style="padding:12px 8px; border-radius:14px; border:1px solid;
                       font-family:'DM Sans',sans-serif; font-size:13px; font-weight:600;
                       cursor:pointer; transition:all 0.2s; text-align:center;">
            <div style="font-size:18px; margin-bottom:2px;">📅</div>
            <div>Hari Ini</div>
            <div style="font-size:11px; margin-top:2px; opacity:0.8;">
                {{ now()->translatedFormat('d F Y') }}
            </div>
        </button>

        {{-- Tombol Tanggal Struk --}}
        <button type="button" @click="useToday = false"
                :style="!useToday
                    ? 'background:var(--amber); color:#0F0F0F; border-color:var(--amber);'
                    : 'background:var(--bg-card); color:var(--text-muted); border-color:var(--border);'"
                style="padding:12px 8px; border-radius:14px; border:1px solid;
                       font-family:'DM Sans',sans-serif; font-size:13px; font-weight:600;
                       cursor:pointer; transition:all 0.2s; text-align:center;">
            <div style="font-size:18px; margin-bottom:2px;">🧾</div>
            <div>Tanggal Struk</div>
            <div style="font-size:11px; margin-top:2px; opacity:0.8;">
                @if($result['receipt_date'] ?? null)
                    {{ \Carbon\Carbon::parse($result['receipt_date'])->translatedFormat('d F Y') }}
                @else
                    Tidak terdeteksi
                @endif
            </div>
        </button>

    </div>

    {{-- Input manual jika ingin custom --}}
    <div style="margin-top:4px;">
        <p style="font-size:11px; color:var(--text-muted); margin-bottom:6px;
                  letter-spacing:0.06em; text-transform:uppercase;">
            Atau pilih manual:
        </p>
        <input type="date" x-model="customDate"
               @change="useToday = false; receiptDate = customDate"
               class="input-field"
               style="font-size:14px;">
    </div>

    {{-- Hidden input yang dikirim ke server --}}
    <input type="hidden" name="transaction_date"
           :value="useToday ? todayDate : (customDate !== todayDate ? customDate : receiptDate)">
</div>

        {{-- Catatan --}}
        <div class="mb-8">
            <label class="label-field">Catatan (opsional)</label>
            <textarea name="notes" class="input-field" rows="2"
                      placeholder="Misal: belanja mingguan, makan siang kantor..."></textarea>
        </div>

        <button type="submit" class="btn-primary">💾 Simpan Transaksi</button>
    </form>
</div>

<script>
    // Kategori selector (vanilla JS, hindari konflik Alpine)
    function selectCategory(el, id) {
        document.querySelectorAll('[id^="cat-"]').forEach(c => {
            c.style.borderColor = 'var(--border)';
            c.style.background  = 'var(--bg-card)';
        });
        el.style.borderColor = 'var(--amber)';
        el.style.background  = 'rgba(245,158,11,0.08)';

        // Update radio input
        document.querySelectorAll('input[name="category_id"]').forEach(r => {
            r.checked = (r.value == id);
        });
    }

    // Alpine component
    function receiptForm() {
        return {
            items: scanItems,
            total: scanTotal,
            addItem() {
                this.items.push({ item_name: '', qty: 1, unit_price: 0, subtotal: 0 });
            },
            removeItem(i) {
                this.items.splice(i, 1);
                this.recalc();
            },
            recalc() {
                this.total = this.items.reduce((s, it) => s + (Number(it.subtotal) || 0), 0);
            },
            updateSubtotal(i) {
                this.items[i].subtotal = this.items[i].qty * this.items[i].unit_price;
                this.recalc();
            },
            fmt(n) {
                return 'Rp ' + Number(n).toLocaleString('id-ID');
            }
        }
    }
</script>

@endsection