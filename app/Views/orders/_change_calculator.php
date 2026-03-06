<?php
/* Partial: _change_calculator.php
 * Include di form.php dan show.php
 * Requires JS: onPaymentChange(), updateCalcTotal(), parseCash(), getCurrentTotal()
 */
?>

<div id="changeCalc" class="change-calc" style="display:none">
    <div class="cc-header">
        <span id="ccIcon">💵</span>
        <span id="ccTitle">Hitung Kembalian</span>
    </div>
    <div class="cc-body">
        <!-- Baris total tagihan -->
        <div class="cc-row">
            <span class="cc-lbl">Total Tagihan</span>
            <span class="cc-val" id="ccTotal">Rp 0</span>
        </div>
        <!-- Input uang diterima -->
        <div class="cc-row">
            <label class="cc-lbl" for="cashPaid">Uang Diterima</label>
            <div class="cc-input-wrap">
                <span class="cc-prefix">Rp</span>
                <input type="text" id="cashPaid" class="cc-input"
                       placeholder="0" oninput="calcChange()" autocomplete="off">
            </div>
        </div>
        <!-- Nominal cepat -->
        <div class="cc-quick" id="ccQuick"></div>
        <div class="cc-divider"></div>
        <!-- Hasil kembalian -->
        <div class="cc-row cc-result">
            <span class="cc-lbl" id="ccChangeLabel">Kembalian</span>
            <span class="cc-change" id="ccChangeVal">—</span>
        </div>
    </div>
</div>

<style>
.change-calc {
    border-radius: 12px;
    overflow: hidden;
    border: 1.5px solid #86efac;
    background: #f0fdf4;
    margin-bottom: 16px;
    transition: border-color .25s, background .25s;
}
.change-calc.cc-noncash { border-color: #bfdbfe; background: #eff6ff; }
.change-calc.cc-noncash .cc-header { background: rgba(59,130,246,.07); }

.cc-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 11px 16px;
    background: rgba(22,163,74,.07);
    border-bottom: 1px solid rgba(0,0,0,.06);
    font-size: 13px;
    font-weight: 700;
    color: #374151;
}
.cc-body { padding: 16px; }
.cc-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}
.cc-lbl { font-size: 13px; color: #6b7280; font-weight: 600; }
.cc-val { font-size: 15px; font-weight: 700; color: #1e293b; }

/* Input uang */
.cc-input-wrap {
    display: flex;
    align-items: center;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    background: #fff;
    overflow: hidden;
    transition: border-color .2s, box-shadow .2s;
}
.cc-input-wrap:focus-within { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.1); }
.cc-prefix {
    padding: 8px 10px;
    background: #f9fafb;
    color: #6b7280;
    font-size: 13px;
    font-weight: 600;
    border-right: 1px solid #d1d5db;
    user-select: none;
}
.cc-input {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    padding: 8px 12px;
    font-size: 15px;
    font-weight: 700;
    width: 150px;
    color: #1e293b;
    background: transparent;
}

/* Nominal cepat */
.cc-quick { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; }
.cc-quick-btn {
    padding: 5px 12px;
    border: 1.5px solid #d1d5db;
    border-radius: 100px;
    background: #fff;
    font-size: 12px;
    font-weight: 700;
    color: #374151;
    cursor: pointer;
    transition: all .15s;
}
.cc-quick-btn:hover { border-color: #3b82f6; color: #2563eb; background: #eff6ff; }
.cc-quick-btn.cc-exact { border-color: #16a34a; color: #15803d; background: #f0fdf4; }

.cc-divider { border: none; border-top: 1.5px dashed #d1d5db; margin-bottom: 12px; }

/* Hasil */
.cc-result { margin-bottom: 0; }
.cc-change {
    font-size: 24px;
    font-weight: 800;
    letter-spacing: -.01em;
}
.cc-green  { color: #16a34a; }
.cc-red    { color: #dc2626; }
.cc-gray   { color: #9ca3af; }
</style>

<script>
// ── State ─────────────────────────────────────────
let _calcTotal = 0;

function updateCalcTotal(total) {
    _calcTotal = total;
    document.getElementById('ccTotal').textContent = 'Rp ' + fmtN(total);
    buildQuickBtns(total);
    calcChange();
}

function getCurrentTotal() { return _calcTotal; }

function parseCash() {
    const raw = document.getElementById('cashPaid').value
        .replace(/\./g, '').replace(/,/g, '').replace(/\s/g, '');
    return parseFloat(raw) || 0;
}

// ── Tampilkan / sembunyikan kalkulator ────────────
function onPaymentChange() {
    const status = document.getElementById('paymentStatus').value;
    const method = document.getElementById('paymentMethod').value;
    const box    = document.getElementById('changeCalc');

    if (status !== 'paid') { box.style.display = 'none'; return; }

    box.style.display = 'block';

    const isCash = (method === 'Tunai' || method === '');
    box.classList.toggle('cc-noncash', !isCash);

    const icons  = { 'Tunai': '💵', 'Transfer': '🏦', 'Kartu Kredit': '💳', 'Qris': '📲', '': '💵' };
    const titles = {
        'Tunai':       'Hitung Kembalian Tunai',
        'Transfer':    'Konfirmasi Transfer',
        'Kartu Kredit':'Konfirmasi Kartu Kredit',
        'Qris':        'Konfirmasi QRIS',
        '':            'Hitung Kembalian',
    };
    document.getElementById('ccIcon').textContent  = icons[method]  ?? '💵';
    document.getElementById('ccTitle').textContent = titles[method] ?? 'Pembayaran';
    document.getElementById('cashPaid').placeholder = isCash ? '0' : '0 (opsional)';

    buildQuickBtns(_calcTotal);
    calcChange();
}

// ── Tombol nominal cepat ──────────────────────────
function buildQuickBtns(total) {
    if (!total) return;
    const wrap   = document.getElementById('ccQuick');
    const method = document.getElementById('paymentMethod')?.value ?? '';
    const isCash = (method === 'Tunai' || method === '');
    if (!isCash) { wrap.innerHTML = ''; return; }

    const amts = genAmounts(total);
    wrap.innerHTML = amts.map(a =>
        `<button type="button"
            class="cc-quick-btn ${a === total ? 'cc-exact' : ''}"
            onclick="setAmount(${a})">
            ${a === total ? '✓ Pas · ' : ''}Rp ${fmtN(a)}
         </button>`
    ).join('');
}

function genAmounts(total) {
    const set  = new Set([total]);
    const steps = [1000, 2000, 5000, 10000, 20000, 50000, 100000, 200000, 500000];
    steps.forEach(s => {
        const r = Math.ceil(total / s) * s;
        if (r >= total) set.add(r);
    });
    return [...set].sort((a, b) => a - b).slice(0, 6);
}

function setAmount(val) {
    document.getElementById('cashPaid').value = fmtN(val);
    calcChange();
}

// ── Hitung kembalian ──────────────────────────────
function calcChange() {
    const paid    = parseCash();
    const total   = _calcTotal;
    const change  = paid - total;
    const method  = document.getElementById('paymentMethod')?.value ?? '';
    const isCash  = (method === 'Tunai' || method === '');

    const lbl = document.getElementById('ccChangeLabel');
    const val = document.getElementById('ccChangeVal');

    // Belum isi
    if (paid === 0) {
        lbl.textContent = isCash ? 'Kembalian' : 'Selisih';
        val.textContent = '—';
        val.className   = 'cc-change cc-gray';
        return;
    }

    if (isCash) {
        if (change > 0) {
            lbl.textContent = 'Kembalian';
            val.textContent = 'Rp ' + fmtN(change);
            val.className   = 'cc-change cc-green';
        } else if (change === 0) {
            lbl.textContent = 'Kembalian';
            val.textContent = '✓ Pas';
            val.className   = 'cc-change cc-green';
        } else {
            lbl.textContent = 'Kurang';
            val.textContent = 'Rp ' + fmtN(Math.abs(change));
            val.className   = 'cc-change cc-red';
        }
    } else {
        // Non-tunai: tampilkan selisih saja
        lbl.textContent = 'Selisih';
        if (change === 0) {
            val.textContent = '✓ Pas';
            val.className   = 'cc-change cc-green';
        } else if (change > 0) {
            val.textContent = '+Rp ' + fmtN(change);
            val.className   = 'cc-change cc-green';
        } else {
            val.textContent = '−Rp ' + fmtN(Math.abs(change));
            val.className   = 'cc-change cc-red';
        }
    }

    // Format input setelah hitung
    if (paid > 0) {
        document.getElementById('cashPaid').value = fmtN(paid);
    }
}

function fmtN(n) { return new Intl.NumberFormat('id-ID').format(n); }
</script>
