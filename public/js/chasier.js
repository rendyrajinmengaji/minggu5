(function () {
    const config = window.cashierConfig || { products: [], initialItems: [] };
    const byId = (id) => document.getElementById(id);
    const form = byId('cashierForm');
    const products = new Map(config.products.map((product) => [Number(product.id), product]));
    const cart = new Map();
    const storageKey = 'minimarket-pos-held-cart';
    const rupiah = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 });
    const money = (amount) => `Rp ${rupiah.format(Math.max(0, amount || 0))}`;
    const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (character) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;',
    })[character]);
    const numberValue = (id) => Math.max(0, Number(byId(id).value) || 0);

    config.products.forEach((product) => {
        product.id = Number(product.id);
        product.price = Number(product.price);
        product.stock = Number(product.stock);
    });

    (config.initialItems || []).forEach((item) => {
        const product = products.get(Number(item.product_id));
        if (product) cart.set(product.id, { product, qty: Math.min(product.stock, Math.max(1, Number(item.qty) || 1)) });
    });

    function calculate() {
        const subtotal = [...cart.values()].reduce((sum, item) => sum + item.product.price * item.qty, 0);
        const discount = Math.min(subtotal, subtotal * Math.min(100, numberValue('discountPercent')) / 100 + numberValue('discountAmount'));
        const tax = Math.max(0, subtotal - discount) * Math.min(100, numberValue('taxPercent')) / 100;
        const total = Math.max(0, subtotal - discount + tax + numberValue('otherFee'));
        const paid = numberValue('paidAmount');
        const balance = paid - total;

        byId('subtotalDisplay').textContent = money(subtotal);
        byId('grandTotalDisplay').textContent = money(total);
        byId('changeLabel').textContent = balance < 0 ? 'Uang kurang' : 'Kembalian';
        byId('changeDisplay').textContent = money(Math.abs(balance));
        byId('changeDisplay').classList.toggle('is-short', balance < 0);
        byId('paymentMessage').textContent = balance < 0 && paid > 0 ? `Kurang ${money(Math.abs(balance))}` : '';
        byId('payButton').disabled = cart.size === 0 || balance < 0;
        return { subtotal, discount, tax, total, paid, balance };
    }

    function renderCart() {
        const rows = byId('cartRows');
        rows.innerHTML = [...cart.values()].map(({ product, qty }) => `
            <tr data-product-id="${product.id}">
                <td class="product-cell"><strong>${escapeHtml(product.name)}</strong><small>${escapeHtml(product.product_code || product.barcode || `PRD-${product.id}`)}</small></td>
                <td class="price-cell">${money(product.price)}</td>
                <td><div class="qty-control"><button type="button" data-action="decrease" aria-label="Kurangi jumlah ${escapeHtml(product.name)}">−</button><input type="number" min="1" max="${product.stock}" value="${qty}" data-action="quantity" aria-label="Jumlah ${escapeHtml(product.name)}"><button type="button" data-action="increase" aria-label="Tambah jumlah ${escapeHtml(product.name)}">+</button></div></td>
                <td class="line-total">${money(product.price * qty)}</td>
                <td><button type="button" class="remove-item" data-action="remove" aria-label="Hapus ${escapeHtml(product.name)}">×</button></td>
            </tr>`).join('');

        const count = [...cart.values()].reduce((sum, item) => sum + item.qty, 0);
        byId('cartCount').textContent = `${count} item`;
        byId('emptyCart').hidden = cart.size > 0;
        rows.closest('table').classList.toggle('has-items', cart.size > 0);
        if (form) {
            form.querySelectorAll('.cart-payload').forEach((input) => input.remove());
            [...cart.values()].forEach(({ product, qty }, index) => {
                [['product_id', product.id], ['qty', qty]].forEach(([name, value]) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `items[${index}][${name}]`;
                    input.value = value;
                    input.className = 'cart-payload';
                    form.append(input);
                });
            });
        }
        calculate();
    }

    function addProduct(product) {
        if (!product || product.stock < 1) {
            byId('paymentMessage').textContent = product ? 'Stok barang habis.' : 'Produk tidak ditemukan.';
            return;
        }
        const existing = cart.get(product.id);
        if (existing && existing.qty >= product.stock) {
            byId('paymentMessage').textContent = `Stok ${product.name} hanya ${product.stock}.`;
            return;
        }
        cart.set(product.id, { product, qty: existing ? existing.qty + 1 : 1 });
        byId('paymentMessage').textContent = '';
        renderCart();
    }

    function updateProductQuantity(productId, qty) {
        const item = cart.get(productId);
        if (!item) return;
        if (qty <= 0) cart.delete(productId);
        else item.qty = Math.min(item.product.stock, qty);
        renderCart();
    }

    function searchProducts(term) {
        const query = term.trim().toLocaleLowerCase('id-ID');
        if (!query) return [];
        const exact = config.products.find((product) => [product.barcode, product.product_code].some((value) => value && value.toLocaleLowerCase('id-ID') === query));
        if (exact) return [exact];
        return config.products.filter((product) => [product.name, product.product_code, product.barcode].some((value) => value && String(value).toLocaleLowerCase('id-ID').includes(query))).slice(0, 8);
    }

    function renderResults(results) {
        const list = byId('productResults');
        list.innerHTML = results.map((product) => `<button class="product-result" type="button" role="option" data-product-id="${product.id}" ${product.stock < 1 ? 'disabled' : ''}><span><strong>${escapeHtml(product.name)}</strong><small>${escapeHtml(product.product_code || product.barcode || `PRD-${product.id}`)} · Stok ${product.stock}</small></span><b>${money(product.price)}</b></button>`).join('');
        list.hidden = results.length === 0;
        byId('productSearch').setAttribute('aria-expanded', String(results.length > 0));
    }

    function cancelTransaction() {
        if (cart.size === 0) return;
        if (!window.confirm('Batalkan transaksi ini dan kosongkan keranjang?')) return;
        cart.clear();
        byId('discountPercent').value = 0;
        byId('discountAmount').value = 0;
        byId('taxPercent').value = 11;
        byId('otherFee').value = 0;
        byId('paidAmount').value = '';
        byId('paymentMessage').textContent = '';
        renderCart();
        byId('productSearch').focus();
    }

    function restoreHeldCart() {
        try {
            const held = JSON.parse(localStorage.getItem(storageKey) || 'null');
            if (!held || !Array.isArray(held.items)) return;
            held.items.forEach(({ id, qty }) => {
                const product = products.get(Number(id));
                if (product && product.stock > 0) cart.set(product.id, { product, qty: Math.min(product.stock, Math.max(1, Number(qty) || 1)) });
            });
            if (held.totals) {
                byId('discountPercent').value = held.totals.discountPercent || 0;
                byId('discountAmount').value = held.totals.discountAmount || 0;
                byId('taxPercent').value = held.totals.taxPercent ?? 11;
                byId('otherFee').value = held.totals.otherFee || 0;
            }
            localStorage.removeItem(storageKey);
            byId('restoreTransaction').hidden = true;
            renderCart();
            byId('productSearch').focus();
        } catch (error) {
            localStorage.removeItem(storageKey);
        }
    }

    byId('productSearch').addEventListener('input', (event) => renderResults(searchProducts(event.target.value)));
    byId('productSearch').addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') return;
        const results = searchProducts(event.currentTarget.value);
        if (results.length === 1) {
            event.preventDefault();
            addProduct(results[0]);
            event.currentTarget.value = '';
            renderResults([]);
        }
    });
    byId('productResults').addEventListener('click', (event) => {
        const button = event.target.closest('[data-product-id]');
        if (!button) return;
        addProduct(products.get(Number(button.dataset.productId)));
        byId('productSearch').value = '';
        renderResults([]);
        byId('productSearch').focus();
    });
    byId('cartRows').addEventListener('click', (event) => {
        const button = event.target.closest('[data-action]');
        if (!button) return;
        const row = button.closest('[data-product-id]');
        const id = Number(row.dataset.productId);
        const item = cart.get(id);
        if (button.dataset.action === 'remove') cart.delete(id);
        if (button.dataset.action === 'increase') updateProductQuantity(id, item.qty + 1);
        if (button.dataset.action === 'decrease') updateProductQuantity(id, item.qty - 1);
        if (button.dataset.action === 'remove') renderCart();
    });
    byId('cartRows').addEventListener('change', (event) => {
        if (event.target.dataset.action === 'quantity') updateProductQuantity(Number(event.target.closest('tr').dataset.productId), Math.max(1, Number(event.target.value) || 1));
    });
    ['discountPercent', 'discountAmount', 'taxPercent', 'otherFee', 'paidAmount'].forEach((id) => byId(id).addEventListener('input', calculate));
    byId('holdTransaction').addEventListener('click', () => {
        if (!cart.size) return;
        localStorage.setItem(storageKey, JSON.stringify({
            items: [...cart.values()].map(({ product, qty }) => ({ id: product.id, qty })),
            totals: {
                discountPercent: numberValue('discountPercent'),
                discountAmount: numberValue('discountAmount'),
                taxPercent: numberValue('taxPercent'),
                otherFee: numberValue('otherFee'),
            },
        }));
        cart.clear();
        renderCart();
        byId('restoreTransaction').hidden = false;
        byId('paymentMessage').textContent = 'Transaksi ditahan di perangkat ini.';
    });
    byId('restoreTransaction').addEventListener('click', restoreHeldCart);
    byId('cancelTransaction').addEventListener('click', cancelTransaction);
    form.addEventListener('submit', (event) => {
        const totals = calculate();
        if (!cart.size || totals.balance < 0) {
            event.preventDefault();
            byId('paymentMessage').textContent = cart.size ? `Uang masih kurang ${money(Math.abs(totals.balance))}.` : 'Tambahkan barang sebelum membayar.';
            if (cart.size) byId('paidAmount').focus();
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'F2') {
            event.preventDefault();
            byId('productSearch').focus();
        } else if (event.key === 'F4') {
            event.preventDefault();
            if (cart.size && calculate().balance >= 0) form.requestSubmit();
            else byId('paidAmount').focus();
        } else if (event.key === 'Escape') {
            byId('productResults').hidden = true;
            byId('productSearch').setAttribute('aria-expanded', 'false');
            cancelTransaction();
        }
    });

    const held = localStorage.getItem(storageKey);
    byId('restoreTransaction').hidden = !held;
    byId('currentDateTime').textContent = new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date());
    renderCart();

    if (config.completed) {
        window.setTimeout(() => window.print(), 350);
    }
})();