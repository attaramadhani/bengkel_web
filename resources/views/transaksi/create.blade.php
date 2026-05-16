@extends('layouts.app')

@section('title', 'Transaksi Baru')
@section('header_title', 'Kasir / Transaksi Baru')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 380px; gap: 2rem;">
    <!-- POS Area -->
    <div class="data-card" style="padding: 2rem;">
        <h2 class="section-title"><i data-lucide="shopping-cart"></i> Pilih Item & Jasa</h2>
        
        <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
            <div style="flex: 1;">
                <label class="form-label">Cari Barang/Jasa</label>
                <select id="item-selector" class="form-control">
                    <option value="">-- Pilih Barang atau Jasa --</option>
                    <optgroup label="Barang (Suku Cadang)">
                        @foreach($barangs as $b)
                            <option value="barang|{{ $b->id_barang }}|{{ $b->nama_barang }}|{{ $b->harga_jual }}|{{ $b->stok }}">
                                {{ $b->nama_barang }} - Rp {{ number_format($b->harga_jual, 0, ',', '.') }} (Stok: {{ $b->stok }})
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Layanan Jasa">
                        @foreach($jasas as $j)
                            <option value="jasa|{{ $j->id_jasa }}|{{ $j->nama_jasa }}|{{ $j->harga_jasa }}|999">
                                {{ $j->nama_jasa }} - Rp {{ number_format($j->harga_jasa, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
            <div style="display: flex; align-items: flex-end;">
                <button type="button" id="add-item-btn" class="btn btn-primary" style="height: 45px;">
                    <i data-lucide="plus"></i> Tambah
                </button>
            </div>
        </div>

        <table id="cart-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="width: 100px;">Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                    <th style="width: 50px;"></th>
                </tr>
            </thead>
            <tbody id="cart-body">
            </tbody>
        </table>
    </div>

    <!-- Summary & Payment -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="stat-card" style="margin-bottom: 0;">
            <div class="stat-label">Total Pembayaran</div>
            <div class="stat-value" style="color: var(--accent-primary);" id="grand-total">Rp 0</div>
        </div>

        <div class="data-card" style="padding: 1.5rem;">
            <!-- Payment Method -->
            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">Metode Pembayaran</label>
                <div style="display: flex; gap: 0.75rem;">
                    <label style="flex: 1; cursor: pointer;">
                        <input type="radio" name="metode" value="cash" checked style="display:none;" onchange="togglePayment()">
                        <div class="payment-option active" id="opt-cash">
                            <i data-lucide="banknote" style="margin-bottom: 4px;"></i>
                            <span>Cash</span>
                        </div>
                    </label>
                    <label style="flex: 1; cursor: pointer;">
                        <input type="radio" name="metode" value="midtrans" style="display:none;" onchange="togglePayment()">
                        <div class="payment-option" id="opt-midtrans">
                            <i data-lucide="credit-card" style="margin-bottom: 4px;"></i>
                            <span>Midtrans</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Cash Section -->
            <div id="cash-section">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label">Bayar (Cash)</label>
                    <input type="number" id="cash-amount" class="form-control" placeholder="0" oninput="updateChange()">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <div class="stat-label">Kembalian</div>
                    <div class="stat-value" style="font-size: 1.2rem;" id="change-amount">Rp 0</div>
                </div>
            </div>

            <!-- Midtrans Section -->
            <div id="midtrans-section" style="display: none;">
                <div style="padding: 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; margin-bottom: 1rem; text-align: center;">
                    <p style="color: #166534; font-weight: 500; font-size: 0.9rem;">
                        <i data-lucide="shield-check" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                        Pembayaran via QRIS, GoPay, Bank Transfer, dll.
                    </p>
                    <p style="color: #64748b; font-size: 0.8rem; margin-top: 4px;">Popup Midtrans Snap akan muncul setelah klik simpan.</p>
                </div>
            </div>

            <button type="button" id="submit-btn" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 15px;" onclick="submitTransaction()">
                <i data-lucide="check-circle"></i> Simpan Transaksi
            </button>
        </div>
    </div>
</div>

<style>
    .payment-option {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 12px;
        border-radius: 12px;
        border: 2px solid var(--card-border);
        transition: all 0.3s ease;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
    }
    .payment-option.active {
        border-color: var(--accent-primary);
        color: var(--accent-primary);
        background: rgba(59, 130, 246, 0.05);
    }
</style>

<!-- Midtrans Snap JS (Sandbox) -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $midtransClientKey }}"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
    const itemSelector = document.getElementById('item-selector');
    const addItemBtn = document.getElementById('add-item-btn');
    const cartBody = document.getElementById('cart-body');
    const grandTotalEl = document.getElementById('grand-total');
    const cashAmountEl = document.getElementById('cash-amount');
    const changeAmountEl = document.getElementById('change-amount');

    let cart = [];

    function togglePayment() {
        const metode = document.querySelector('input[name="metode"]:checked').value;
        document.getElementById('cash-section').style.display = metode === 'cash' ? 'block' : 'none';
        document.getElementById('midtrans-section').style.display = metode === 'midtrans' ? 'block' : 'none';
        document.getElementById('opt-cash').classList.toggle('active', metode === 'cash');
        document.getElementById('opt-midtrans').classList.toggle('active', metode === 'midtrans');
    }

    addItemBtn.addEventListener('click', () => {
        const val = itemSelector.value;
        if (!val) return;
        const [type, id, name, price, stock] = val.split('|');
        const existing = cart.find(i => i.id === id && i.type === type);
        if (existing) {
            if (existing.qty < parseInt(stock)) existing.qty++;
            else alert('Stok tidak cukup!');
        } else {
            cart.push({ type, id, name, price: parseFloat(price), qty: 1, stock: parseInt(stock) });
        }
        renderCart();
    });

    function renderCart() {
        cartBody.innerHTML = '';
        let total = 0;
        cart.forEach((item, index) => {
            const subtotal = item.price * item.qty;
            total += subtotal;
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>${item.name}</strong><br><small style="color:var(--text-muted)">${item.type.toUpperCase()}</small></td>
                <td><input type="number" class="form-control" value="${item.qty}" min="1" max="${item.stock}" onchange="updateQty(${index}, this.value)" style="padding:8px;"></td>
                <td>Rp ${item.price.toLocaleString('id-ID')}</td>
                <td>Rp ${subtotal.toLocaleString('id-ID')}</td>
                <td><button type="button" class="btn-icon" style="color:#ef4444" onclick="removeItem(${index})"><i data-lucide="x"></i></button></td>
            `;
            cartBody.appendChild(row);
        });
        grandTotalEl.innerText = 'Rp ' + total.toLocaleString('id-ID');
        updateChange();
        lucide.createIcons();
    }

    window.updateQty = (index, val) => {
        const qty = parseInt(val);
        if (qty > cart[index].stock) { alert('Stok tidak cukup!'); cart[index].qty = cart[index].stock; }
        else cart[index].qty = qty;
        renderCart();
    }

    window.removeItem = (index) => { cart.splice(index, 1); renderCart(); }

    function updateChange() {
        const total = cart.reduce((acc, i) => acc + (i.price * i.qty), 0);
        const cash = parseFloat(cashAmountEl.value) || 0;
        changeAmountEl.innerText = 'Rp ' + (cash > total ? (cash - total).toLocaleString('id-ID') : '0');
    }

    function submitTransaction() {
        if (cart.length === 0) { alert('Keranjang masih kosong!'); return; }

        const metode = document.querySelector('input[name="metode"]:checked').value;
        const items = cart.map((item, i) => ({ type: item.type, id: item.id, qty: item.qty }));

        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<i data-lucide="loader"></i> Memproses...';

        fetch('{{ route("transaksi.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ items, metode_bayar: metode }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'midtrans' && data.snap_token) {
                // Open Midtrans Snap Popup
                window.snap.pay(data.snap_token, {
                    onSuccess: () => {
                        alert('Pembayaran berhasil!');
                        window.location.href = '/transaksi/' + data.transaksi_id;
                    },
                    onPending: () => {
                        alert('Pembayaran pending, menunggu konfirmasi.');
                        window.location.href = '/transaksi';
                    },
                    onError: () => {
                        alert('Pembayaran gagal!');
                        window.location.href = '/transaksi';
                    },
                    onClose: () => {
                        alert('Popup ditutup. Transaksi masih pending.');
                        window.location.href = '/transaksi';
                    }
                });
            } else if (data.status === 'bypass') {
                // Midtrans offline, fallback ke cash
                alert(data.message);
                window.location.href = '/transaksi/' + data.transaksi_id;
            } else {
                // Cash success
                alert(data.message || 'Transaksi berhasil!');
                window.location.href = '/transaksi/' + data.transaksi_id;
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="check-circle"></i> Simpan Transaksi';
            lucide.createIcons();
        });
    }
</script>
@endsection
