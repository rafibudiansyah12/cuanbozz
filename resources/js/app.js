import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.posScreen = ({ products = [], sampleMode = false, initialItems = [], initialCash = '', initialServiceType = 'take_away' } = {}) => {
    const sampleProducts = [
        { id: 1, sku: 'COF-001', name: 'Cappuccino', category: 'Kopi', price: 28000, description: 'Espresso dengan susu lembut dan foam.', unit: 'gelas', stock: 20, image_url: '/images/products/cappuccino.jpg', track_stock: true },
        { id: 2, sku: 'COF-002', name: 'Coffee Latte', category: 'Kopi', price: 30000, description: 'Kopi susu dengan rasa ringan dan seimbang.', unit: 'gelas', stock: 18, image_url: '/images/products/coffee-latte.jpg', track_stock: true },
        { id: 3, sku: 'COF-003', name: 'Americano', category: 'Kopi', price: 22000, description: 'Espresso dengan tambahan air panas.', unit: 'gelas', stock: 25, image_url: '/images/products/americano.jpg', track_stock: true },
        { id: 4, sku: 'COF-004', name: 'V60', category: 'Kopi', price: 26000, description: 'Kopi seduh manual dengan rasa yang bersih.', unit: 'gelas', stock: 12, image_url: '/images/products/v60.jpg', track_stock: true },
        { id: 5, sku: 'NCO-001', name: 'Matcha Latte', category: 'Non-kopi', price: 28000, description: 'Matcha dengan susu yang creamy.', unit: 'gelas', stock: 15, image_url: '/images/products/matcha-latte.jpg', track_stock: true },
        { id: 6, sku: 'NCO-002', name: 'Teh Lemon', category: 'Non-kopi', price: 18000, description: 'Teh segar dengan lemon dan sedikit madu.', unit: 'gelas', stock: 25, image_url: '/images/products/lemon-tea.jpg', track_stock: true },
        { id: 7, sku: 'FOD-001', name: 'Croissant', category: 'Makanan', price: 20000, description: 'Pastry renyah untuk teman minum kopi.', unit: 'pcs', stock: 8, image_url: '/images/products/croissant.jpg', track_stock: true },
        { id: 8, sku: 'SNA-001', name: 'Donat Cokelat', category: 'Camilan', price: 12000, description: 'Donat lembut dengan lapisan cokelat.', unit: 'pcs', stock: 10, image_url: '/images/products/donat-cokelat.jpg', track_stock: true },
        { id: 9, sku: 'DES-001', name: 'Brownies', category: 'Dessert', price: 16000, description: 'Brownies cokelat dengan tekstur fudgy.', unit: 'pcs', stock: 6, image_url: '/images/products/brownies.jpg', track_stock: true },
    ];

    return {
    sampleMode,
    activeCategory: 'Semua',
    search: '',
    showFilters: false,
    availableOnly: false,
    products: sampleMode ? sampleProducts : products,
    cart: sampleMode ? [] : initialItems.map((item) => {
        const product = products.find((entry) => Number(entry.id) === Number(item.product_id));
        if (!product) return null;

        const options = (item.options ?? []).map((optionId) => product.option_groups?.flatMap((group) => group.options).find((option) => Number(option.id) === Number(optionId))).filter(Boolean);

        return { ...product, key: `${product.id}:${options.map((option) => option.id).sort().join(',')}`, quantity: Number(item.quantity), note: item.note ?? '', options };
    }).filter(Boolean),
    cashReceived: initialCash,
    serviceType: initialServiceType,
    configureProduct: null,
    optionSelections: {},
    optionError: '',
    get categories() {
        return ['Semua', ...new Set(this.products.map((product) => product.category).filter(Boolean))];
    },
    get filteredProducts() {
        const query = this.search.trim().toLocaleLowerCase('id');

        return this.products.filter((product) => {
            const matchesCategory = this.activeCategory === 'Semua' || product.category === this.activeCategory;
            const matchesSearch = !query || `${product.name} ${product.category} ${product.sku}`.toLocaleLowerCase('id').includes(query);
            const matchesStock = !this.availableOnly || !product.track_stock || product.stock > 0;

            return matchesCategory && matchesSearch && matchesStock;
        });
    },
    get itemCount() {
        return this.cart.reduce((count, item) => count + item.quantity, 0);
    },
    get subtotal() {
        return this.cart.reduce((total, item) => total + this.unitPrice(item) * item.quantity, 0);
    },
    get changeDue() {
        return Math.max(0, Number(this.cashReceived || 0) - this.subtotal);
    },
    get canCheckout() {
        return this.cart.length > 0 && Number(this.cashReceived || 0) >= this.subtotal;
    },
    unitPrice(item) {
        return item.price + (item.options ?? []).reduce((total, option) => total + Number(option.price_delta), 0);
    },
    formatDelta(amount) {
        const value = Number(amount);
        return `${value < 0 ? '−' : '+'}${this.formatRupiah(Math.abs(value))}`;
    },
    addToCart(product, options = []) {
        const key = `${product.id}:${options.map((option) => option.id).sort().join(',')}`;
        const item = this.cart.find((entry) => entry.key === key);

        if (item) {
            if (product.track_stock && item.quantity >= product.stock) return;
            item.quantity += 1;
            return;
        }

        if (product.track_stock && product.stock < 1) return;
        this.cart.push({ ...product, key, quantity: 1, note: '', options });
    },
    beginConfigure(product) {
        if (!product.option_groups?.length) {
            this.addToCart(product);
            return;
        }

        this.configureProduct = product;
        this.optionError = '';
        this.optionSelections = Object.fromEntries(product.option_groups.map((group) => [
            group.id,
            group.selection_type === 'multiple'
                ? []
                : (group.is_required ? group.options[0]?.id ?? '' : ''),
        ]));
    },
    addConfiguredProduct() {
        const product = this.configureProduct;
        if (!product) return;

        const selectedOptions = [];
        for (const group of product.option_groups) {
            const raw = this.optionSelections[group.id];
            const ids = group.selection_type === 'multiple' ? (raw ?? []).map(Number) : (raw ? [Number(raw)] : []);
            const min = group.is_required ? Math.max(1, Number(group.min_select ?? 0)) : Number(group.min_select ?? 0);
            const max = group.max_select === null ? Infinity : Number(group.max_select);

            if (ids.length < min || ids.length > max) {
                this.optionError = `Pilih ${min}${max !== Infinity && max !== min ? ` sampai ${max}` : ''} opsi pada ${group.name}.`;
                return;
            }

            for (const id of ids) {
                const option = group.options.find((entry) => Number(entry.id) === id);
                if (option) selectedOptions.push({ ...option, group_id: group.id, group_name: group.name });
            }
        }

        this.addToCart(product, selectedOptions);
        this.configureProduct = null;
        this.optionError = '';
    },
    changeQuantity(key, amount) {
        const item = this.cart.find((entry) => entry.key === key);

        if (!item) return;
        if (amount > 0 && item.track_stock && item.quantity >= item.stock) return;
        item.quantity += amount;

        if (item.quantity <= 0) this.removeFromCart(key);
    },
    removeFromCart(key) {
        this.cart = this.cart.filter((item) => item.key !== key);
    },
    formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(amount);
    },
    };
};

window.productOptionsForm = ({ initialGroups = [] } = {}) => ({
    optionGroups: initialGroups,
    addGroup() {
        this.optionGroups.push({ name: '', selection_type: 'single', is_required: false, min_select: 0, max_select: 1, options: [{ name: '', price_delta: 0 }] });
    },
    removeGroup(index) {
        this.optionGroups.splice(index, 1);
    },
    addOption(group) {
        group.options.push({ name: '', price_delta: 0 });
    },
    removeOption(group, index) {
        group.options.splice(index, 1);
    },
});

Alpine.start();

window.themePreview = ({ presets, defaultPrimary, defaultAccent = '#D97706' }) => ({
    presets,
    primary: defaultPrimary,
    accent: defaultAccent,
    contrastRatio: 0,
    isReadable: true,
    init() {
        this.apply(this.primary);
        this.applyAccent(this.accent);
    },
    choose(color) {
        this.primary = color;
        this.apply(color);
    },
    chooseAccent(color) {
        this.accent = color;
        this.applyAccent(color);
    },
    applyAccent(hex) {
        const raw = hex.replace('#', '');
        if (!/^[\da-f]{6}$/i.test(raw)) return;

        const channels = raw.match(/.{2}/g).map((channel) => parseInt(channel, 16));
        document.documentElement.style.setProperty('--accent', channels.join(' '));
    },
    apply(hex) {
        const raw = hex.replace('#', '');
        if (!/^[\da-f]{6}$/i.test(raw)) {
            this.contrastRatio = 0;
            this.isReadable = false;
            return;
        }

        const base = raw.match(/.{2}/g).map((channel) => parseInt(channel, 16));
        const mix = (target, amount) => base.map((channel, index) => Math.round(channel + (target[index] - channel) * amount));
        const rgb = (channels) => channels.join(' ');
        const luminance = (channels) => channels.map((channel) => {
            const value = channel / 255;
            return value <= 0.04045 ? value / 12.92 : ((value + 0.055) / 1.055) ** 2.4;
        }).reduce((total, value, index) => total + value * [0.2126, 0.7152, 0.0722][index], 0);
        const dark = [17, 24, 39];
        const white = [255, 255, 255];
        const contrast = (color) => {
            const values = [luminance(base), luminance(color)].sort((a, b) => b - a);
            return (values[0] + 0.05) / (values[1] + 0.05);
        };
        const foreground = contrast(white) > contrast(dark) ? white : dark;
        this.contrastRatio = Math.max(contrast(white), contrast(dark));
        this.isReadable = this.contrastRatio >= 4.5;
        const root = document.documentElement;

        [50, 100, 200, 300, 400, 500, 600, 700, 800, 900].forEach((shade) => {
            const amount = shade <= 600 ? (600 - shade) / 600 : (shade - 600) / 300;
            const shadeColor = shade <= 600 ? mix(white, amount) : mix([0, 0, 0], amount);
            root.style.setProperty(`--primary-${shade}`, rgb(shade === 500 ? base : shadeColor));
        });
        root.style.setProperty('--text-on-primary', rgb(foreground));
    },
});
