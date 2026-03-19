document.addEventListener('DOMContentLoaded', () => {
    const searchTriggers = document.querySelectorAll('.mini-search');

    if (!searchTriggers.length) {
        return;
    }

    ensureSearchModalStyles();
    const elements = ensureSearchModalMarkup();
    const {
        overlay,
        dialog,
        input,
        closeButton,
        meta,
        resultsRegion,
        feedback,
    } = elements;

    let debounceId = null;
    let activeQuery = '';

    const escapeHtml = (value) => String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    function showFeedback(message) {
        if (!feedback) {
            return;
        }

        feedback.textContent = message;
        feedback.classList.add('is-visible');

        window.clearTimeout(showFeedback.timeoutId);
        showFeedback.timeoutId = window.setTimeout(() => {
            feedback.classList.remove('is-visible');
        }, 1500);
    }

    function renderPlaceholder() {
        meta.textContent = 'Start typing to search products live.';
        resultsRegion.innerHTML = '<div class="search-modal-placeholder">Start typing to search by product, category, or keyword.</div>';
    }

    function renderLoading() {
        meta.textContent = 'Searching...';
        resultsRegion.innerHTML = '<div class="search-modal-placeholder">Searching products...</div>';
    }

    function renderNoResults(query, didYouMean) {
        meta.textContent = `0 result(s) for "${query}"`;
        let suggestionHtml = '';
        if (didYouMean) {
            suggestionHtml = `
                <div class="search-modal-did-you-mean">
                    Did you mean <button type="button" class="search-modal-suggestion-btn" data-suggestion="${escapeHtml(didYouMean)}">${escapeHtml(didYouMean)}</button>?
                </div>
            `;
        }
        resultsRegion.innerHTML = `
            <div class="search-modal-empty">
                No products matched your search. Try a different term.
                ${suggestionHtml}
            </div>
        `;

        resultsRegion.querySelectorAll('.search-modal-suggestion-btn').forEach((button) => {
            button.addEventListener('click', () => {
                input.value = button.dataset.suggestion || '';
                runSearch();
            });
        });
    }

    function favouriteButtonMarkup(item) {
        const isFavourite = !!item.isFavourite;
        return `
            <form method="post" action="favourite_toggle.php" class="search-modal-favourite-form">
                <input type="hidden" name="product_id" value="${item.product_ID}">
                <input type="hidden" name="redirect" value="${escapeHtml(window.location.pathname + window.location.search)}">
                <button
                    type="submit"
                    class="search-modal-favourite-btn${isFavourite ? ' is-active' : ''}"
                    data-favourite-state="${isFavourite ? 'true' : 'false'}"
                    aria-pressed="${isFavourite ? 'true' : 'false'}"
                    title="${isFavourite ? 'Remove from favourites' : 'Add to favourites'}"
                >${isFavourite ? '♥' : '♡'}</button>
            </form>
        `;
    }

    function cardMarkup(item) {
        return `
            <article class="search-modal-card">
                <a href="product.php?id=${item.product_ID}" class="search-modal-card-link">
                    <img src="../images/${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}" class="search-modal-card-image">
                </a>
                <div class="search-modal-card-body">
                    <a href="product.php?id=${item.product_ID}" class="search-modal-card-link">
                        <h3>${escapeHtml(item.name)}</h3>
                    </a>
                    <p class="search-modal-card-category">${escapeHtml(item.category_label)}</p>
                    <p class="search-modal-card-price">£${escapeHtml(item.price)}</p>
                    <div class="search-modal-card-actions">
                        ${favouriteButtonMarkup(item)}
                        <button type="button" class="search-modal-basket-btn" data-product-id="${item.product_ID}">Add to Basket</button>
                    </div>
                </div>
            </article>
        `;
    }

    async function addToBasketDirect(productId) {
        try {
            const response = await fetch('/TEAM-13-/Draft/backend/routes/basketRoutes.php', {
                method: 'POST',
                credentials: 'include',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'add', product_ID: productId, quantity: 1 })
            });
            const payload = await response.json();
            if (!payload.success) {
                throw new Error(payload.message || 'Could not add item to basket.');
            }

            if (typeof window.updateBasketCounter === 'function') {
                window.updateBasketCounter();
            }

            return true;
        } catch (error) {
            alert(error.message || 'Could not add item to basket.');
            return false;
        }
    }

    function bindDynamicActions() {
        resultsRegion.querySelectorAll('.search-modal-basket-btn').forEach((button) => {
            if (button.dataset.bound === 'true') {
                return;
            }
            button.dataset.bound = 'true';
            button.addEventListener('click', async () => {
                const productId = Number(button.dataset.productId || '0');
                if (!productId) {
                    return;
                }

                const originalText = button.textContent;
                button.disabled = true;
                button.textContent = 'Adding...';

                const wasAdded = await addToBasketDirect(productId);

                if (wasAdded) {
                    button.textContent = 'Added';
                    showFeedback('Added to basket');
                    window.setTimeout(() => {
                        button.textContent = originalText;
                    }, 1100);
                } else {
                    button.textContent = originalText;
                }

                button.disabled = false;
            });
        });

        resultsRegion.querySelectorAll('.search-modal-favourite-form').forEach((form) => {
            if (form.dataset.bound === 'true') {
                return;
            }
            form.dataset.bound = 'true';
            const button = form.querySelector('.search-modal-favourite-btn');
            if (!button) {
                return;
            }

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                if (button.disabled) {
                    return;
                }

                button.disabled = true;
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new FormData(form)
                    });
                    const payload = await response.json();
                    if (!response.ok || !payload.success) {
                        if (payload.redirect) {
                            window.location.href = payload.redirect;
                            return;
                        }
                        throw new Error(payload.message || 'Could not update favourites.');
                    }

                    const isFavourite = !!payload.isFavourite;
                    button.dataset.favouriteState = isFavourite ? 'true' : 'false';
                    button.classList.toggle('is-active', isFavourite);
                    button.setAttribute('aria-pressed', isFavourite ? 'true' : 'false');
                    button.setAttribute('title', isFavourite ? 'Remove from favourites' : 'Add to favourites');
                    button.textContent = isFavourite ? '♥' : '♡';
                    showFeedback(isFavourite ? 'Added to favourites' : 'Removed from favourites');
                } catch (error) {
                    form.submit();
                    return;
                } finally {
                    button.disabled = false;
                }
            });
        });
    }

    function renderResults(payload) {
        meta.textContent = `${payload.resultsCount} result(s) for "${payload.query}"`;
        resultsRegion.innerHTML = `<section class="search-modal-grid">${payload.results.map(cardMarkup).join('')}</section>`;
        bindDynamicActions();
    }

    async function runSearch() {
        const query = input.value.trim();
        activeQuery = query;

        if (!query) {
            renderPlaceholder();
            return;
        }

        renderLoading();

        try {
            const response = await fetch(`search.php?ajax=1&q=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json' }
            });
            const payload = await response.json();

            if (query !== activeQuery) {
                return;
            }

            if (!response.ok || !payload.success) {
                throw new Error('Search failed');
            }

            if (!payload.results || payload.results.length === 0) {
                renderNoResults(query, payload.didYouMean || null);
                return;
            }

            renderResults(payload);
        } catch (error) {
            meta.textContent = 'Search is unavailable right now.';
            resultsRegion.innerHTML = '<div class="search-modal-empty">Something went wrong. Please try again.</div>';
        }
    }

    function openModal(prefill = '') {
        overlay.classList.add('is-open');
        document.body.classList.add('search-modal-open');

        if (prefill && !input.value.trim()) {
            input.value = prefill;
        }

        window.setTimeout(() => input.focus(), 40);

        if (input.value.trim()) {
            runSearch();
        } else {
            renderPlaceholder();
        }
    }

    function closeModal() {
        overlay.classList.remove('is-open');
        document.body.classList.remove('search-modal-open');
    }

    searchTriggers.forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            const href = trigger.getAttribute('href') || '';
            let prefill = '';
            try {
                const url = new URL(href, window.location.href);
                prefill = url.searchParams.get('q') || '';
            } catch (error) {
                prefill = '';
            }
            openModal(prefill);
        });
    });

    input.addEventListener('input', () => {
        window.clearTimeout(debounceId);
        debounceId = window.setTimeout(runSearch, 220);
    });

    closeButton.addEventListener('click', closeModal);

    dialog.addEventListener('click', (event) => {
        event.stopPropagation();
    });

    overlay.addEventListener('mousedown', (event) => {
        if (event.target === overlay) {
            event.preventDefault();
        }
    });

    renderPlaceholder();
});

function ensureSearchModalMarkup() {
    const existingOverlay = document.getElementById('global-search-modal');
    if (existingOverlay) {
        return {
            overlay: existingOverlay,
            dialog: existingOverlay.querySelector('.search-modal-dialog'),
            input: existingOverlay.querySelector('#global-search-input'),
            closeButton: existingOverlay.querySelector('.search-modal-close'),
            meta: existingOverlay.querySelector('#global-search-meta'),
            resultsRegion: existingOverlay.querySelector('#global-search-results'),
            feedback: existingOverlay.querySelector('.search-modal-feedback'),
        };
    }

    const wrapper = document.createElement('div');
    wrapper.innerHTML = `
        <div id="global-search-modal" class="search-modal-overlay" aria-hidden="true">
            <div class="search-modal-dialog" role="dialog" aria-modal="true" aria-label="Search products">
                <div class="search-modal-header">
                    <div class="search-modal-header-copy">
                        <span class="search-modal-kicker">Loft & Living Search</span>
                        <h2>Find something worth bringing home.</h2>
                    </div>
                    <button type="button" class="search-modal-close" aria-label="Close search">×</button>
                </div>
                <div class="search-modal-toolbar">
                    <div class="search-modal-input-wrap">
                        <span class="search-modal-input-icon" aria-hidden="true">⌕</span>
                        <input type="text" id="global-search-input" placeholder="Search by product, category, or keyword..." autocomplete="off">
                    </div>
                    <div id="global-search-meta" class="search-modal-meta"></div>
                </div>
                <div class="search-modal-feedback" aria-live="polite"></div>
                <div id="global-search-results" class="search-modal-results"></div>
            </div>
        </div>
    `;

    document.body.appendChild(wrapper.firstElementChild);
    const overlay = document.getElementById('global-search-modal');

    return {
        overlay,
        dialog: overlay.querySelector('.search-modal-dialog'),
        input: overlay.querySelector('#global-search-input'),
        closeButton: overlay.querySelector('.search-modal-close'),
        meta: overlay.querySelector('#global-search-meta'),
        resultsRegion: overlay.querySelector('#global-search-results'),
        feedback: overlay.querySelector('.search-modal-feedback'),
    };
}

function ensureSearchModalStyles() {
    if (document.getElementById('global-search-modal-style')) {
        return;
    }

    const style = document.createElement('style');
    style.id = 'global-search-modal-style';
    style.textContent = `
        body.search-modal-open { overflow: hidden; }
        .search-modal-overlay {
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at top, rgba(255, 255, 255, 0.08), transparent 35%),
                rgba(15, 13, 12, 0.56);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            display: none;
            align-items: flex-start;
            justify-content: center;
            z-index: 11000;
            padding: 78px 20px 28px;
        }
        .search-modal-overlay.is-open { display: flex; }
        .search-modal-dialog {
            position: relative;
            width: min(1220px, 100%);
            max-height: calc(100vh - 106px);
            overflow: hidden;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 244, 239, 0.98) 100%);
            border-radius: 32px;
            border: 1px solid rgba(118, 106, 94, 0.14);
            box-shadow: 0 36px 90px rgba(16, 12, 9, 0.26);
            display: flex;
            flex-direction: column;
        }
        .search-modal-header {
            position: relative;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 22px;
            padding: 28px 30px 16px;
            border-bottom: 1px solid rgba(118, 106, 94, 0.12);
            background:
                radial-gradient(circle at top left, rgba(196, 186, 173, 0.24), transparent 36%);
        }
        .search-modal-header-copy {
            max-width: 640px;
        }
        .search-modal-kicker {
            display: inline-block;
            margin-bottom: 8px;
            color: #7a7067;
            font-family: 'mr-eaves-modern', 'Mr Eaves Modern', Arial, sans-serif;
            font-size: 13px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        .search-modal-header h2 {
            margin: 0;
            color: #171411;
            font-family: 'ivybodoni', serif;
            font-size: clamp(32px, 4vw, 48px);
            font-weight: 400;
            line-height: 0.98;
            letter-spacing: -0.03em;
        }
        .search-modal-close {
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            border: 1px solid rgba(118, 106, 94, 0.16);
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.7);
            color: #3a342f;
            font-size: 34px;
            line-height: 0.9;
            cursor: pointer;
            transition: background 0.18s ease, transform 0.18s ease, border-color 0.18s ease;
        }
        .search-modal-close:hover {
            background: rgba(196, 186, 173, 0.2);
            border-color: rgba(118, 106, 94, 0.28);
            transform: translateY(-1px);
        }
        .search-modal-toolbar {
            padding: 18px 30px 22px;
            border-bottom: 1px solid rgba(118, 106, 94, 0.1);
        }
        .search-modal-input-wrap {
            position: relative;
            display: block;
            width: 100%;
        }
        .search-modal-input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #7a7067;
            font-size: 20px;
            line-height: 1;
            pointer-events: none;
        }
        #global-search-input {
            width: 100%;
            min-width: 0;
            min-height: 58px;
            border: 1px solid rgba(118, 106, 94, 0.18);
            border-radius: 999px;
            padding: 0 22px 0 52px;
            font-family: 'mr-eaves-modern', 'Mr Eaves Modern', Arial, sans-serif;
            font-size: 18px;
            color: #171411;
            background: rgba(255, 253, 250, 0.96);
            outline: none;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }
        #global-search-input:focus {
            border-color: rgba(112, 96, 81, 0.48);
            box-shadow: 0 0 0 4px rgba(186, 174, 161, 0.18);
            background: #fff;
        }
        #global-search-input::placeholder {
            color: #9d9388;
        }
        .search-modal-meta {
            margin-top: 12px;
            color: #6e655d;
            font-family: 'mr-eaves-modern', 'Mr Eaves Modern', Arial, sans-serif;
            font-size: 16px;
            line-height: 1.45;
        }
        .search-modal-feedback {
            position: absolute;
            top: 22px;
            left: 50%;
            transform: translate(-50%, -12px);
            background: rgba(23, 20, 17, 0.96);
            color: #fff;
            padding: 10px 16px;
            border-radius: 999px;
            font-family: 'mr-eaves-modern', 'Mr Eaves Modern', Arial, sans-serif;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.03em;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.18s ease, transform 0.18s ease;
            z-index: 2;
        }
        .search-modal-feedback.is-visible {
            opacity: 1;
            transform: translate(-50%, 0);
        }
        .search-modal-results {
            overflow: auto;
            padding: 26px 30px 30px;
        }
        .search-modal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(245px, 1fr));
            gap: 22px;
        }
        .search-modal-card {
            border: 1px solid rgba(118, 106, 94, 0.12);
            border-radius: 24px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.88);
            box-shadow: 0 18px 42px rgba(44, 31, 22, 0.08);
            display: flex;
            flex-direction: column;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }
        .search-modal-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 48px rgba(44, 31, 22, 0.1);
            border-color: rgba(118, 106, 94, 0.18);
        }
        .search-modal-card-link {
            text-decoration: none;
            color: inherit;
        }
        .search-modal-card-image {
            width: 100%;
            height: 232px;
            object-fit: cover;
            display: block;
        }
        .search-modal-card-body {
            padding: 16px 16px 18px;
            display: flex;
            flex: 1;
            flex-direction: column;
        }
        .search-modal-card-body h3 {
            margin: 0 0 8px;
            color: #171411;
            font-family: 'ivybodoni', serif;
            font-size: 20px;
            font-weight: 400;
            line-height: 1.15;
        }
        .search-modal-card-category {
            margin: 0 0 8px;
            color: #8a8075;
            font-family: 'mr-eaves-modern', 'Mr Eaves Modern', Arial, sans-serif;
            font-size: 15px;
            line-height: 1.4;
        }
        .search-modal-card-price {
            margin: 0 0 6px;
            color: #1f1a17;
            font-family: 'mr-eaves-modern', 'Mr Eaves Modern', Arial, sans-serif;
            font-size: 17px;
            font-weight: 500;
        }
        .search-modal-card-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: auto;
            padding-top: 14px;
        }
        .search-modal-favourite-form {
            margin: 0;
        }
        .search-modal-favourite-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(118, 106, 94, 0.12);
            border-radius: 50%;
            background: #f4efe8;
            color: #4d463d;
            cursor: pointer;
            font-size: 22px;
            transition: background 0.18s ease, color 0.18s ease, transform 0.18s ease, border-color 0.18s ease;
        }
        .search-modal-favourite-btn:hover {
            transform: translateY(-1px);
            background: #ebe4da;
        }
        .search-modal-favourite-btn.is-active {
            background: #171411;
            color: #fff;
            border-color: #171411;
        }
        .search-modal-basket-btn {
            border: 1.5px solid #9c9488;
            border-radius: 32px;
            padding: 10px 18px;
            background: #9c9488;
            color: #fff;
            cursor: pointer;
            font-weight: 500;
            font-size: 16px;
            line-height: 1.2;
            font-family: 'mr-eaves-modern', 'Mr Eaves Modern', Arial, sans-serif;
            letter-spacing: 0.03em;
            box-shadow: 0 2px 12px rgba(43, 43, 43, 0.08);
            transition: background 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease;
            white-space: nowrap;
        }
        .search-modal-basket-btn:hover {
            background: #7f786d;
            border-color: #7f786d;
            transform: translateY(-2px);
            box-shadow: 0 4px 18px rgba(43, 43, 43, 0.13);
        }
        .search-modal-basket-btn:disabled {
            opacity: 1;
        }
        .search-modal-basket-btn[disabled] {
            cursor: wait;
        }
        html.dark-mode .search-modal-overlay {
            background:
                radial-gradient(circle at top, rgba(255, 255, 255, 0.04), transparent 35%),
                rgba(7, 7, 7, 0.68);
        }
        html.dark-mode .search-modal-dialog {
            background: linear-gradient(180deg, rgba(25, 25, 25, 0.98) 0%, rgba(31, 31, 31, 0.98) 100%);
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 36px 90px rgba(0, 0, 0, 0.42);
        }
        html.dark-mode .search-modal-header {
            border-bottom-color: rgba(255, 255, 255, 0.08);
            background: radial-gradient(circle at top left, rgba(255, 255, 255, 0.06), transparent 36%);
        }
        html.dark-mode .search-modal-kicker,
        html.dark-mode .search-modal-meta,
        html.dark-mode .search-modal-card-category {
            color: #c6bcae;
        }
        html.dark-mode .search-modal-header h2,
        html.dark-mode .search-modal-card-body h3,
        html.dark-mode .search-modal-card-price {
            color: #f5eee7;
        }
        html.dark-mode .search-modal-close {
            background: rgba(255, 255, 255, 0.06);
            color: #f0e7de;
            border-color: rgba(255, 255, 255, 0.08);
        }
        html.dark-mode #global-search-input {
            background: #252525;
            color: #f0e7de;
            border-color: #4a4a4a;
        }
        html.dark-mode #global-search-input:focus {
            border-color: #8c8376;
            box-shadow: 0 0 0 4px rgba(140, 131, 118, 0.18);
            background: #2a2a2a;
        }
        html.dark-mode #global-search-input::placeholder,
        html.dark-mode .search-modal-input-icon {
            color: #a99f93;
        }
        html.dark-mode .search-modal-card {
            background: rgba(29, 29, 29, 0.94);
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 22px 48px rgba(0, 0, 0, 0.28);
        }
        html.dark-mode .search-modal-favourite-btn {
            background: #242424;
            border-color: rgba(255, 255, 255, 0.08);
            color: #f0e7de;
        }
        html.dark-mode .search-modal-basket-btn {
            background: #8c8376;
            border-color: #8c8376;
            color: #fff;
        }
        html.dark-mode .search-modal-basket-btn:hover {
            background: #a1978a;
            border-color: #a1978a;
        }
        .search-modal-placeholder,
        .search-modal-empty {
            border: 1px dashed rgba(118, 106, 94, 0.24);
            border-radius: 22px;
            padding: 34px 24px;
            color: #5f564d;
            font-family: 'mr-eaves-modern', 'Mr Eaves Modern', Arial, sans-serif;
            font-size: 18px;
            line-height: 1.55;
            background: rgba(255, 255, 255, 0.54);
        }
        .search-modal-did-you-mean {
            margin-top: 12px;
        }
        .search-modal-suggestion-btn {
            border: none;
            background: transparent;
            color: #6f675c;
            cursor: pointer;
            font-weight: 600;
            padding: 0;
            text-decoration: underline;
            text-underline-offset: 3px;
        }
        html.dark-mode .search-modal-placeholder,
        html.dark-mode .search-modal-empty {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.12);
            color: #cfc6bc;
        }
        html.dark-mode .search-modal-suggestion-btn {
            color: #d4c8b7;
        }
        @media (max-width: 640px) {
            .search-modal-overlay {
                padding: 64px 12px 18px;
            }
            .search-modal-dialog {
                max-height: calc(100vh - 82px);
                border-radius: 24px;
            }
            .search-modal-header,
            .search-modal-toolbar,
            .search-modal-results {
                padding-left: 18px;
                padding-right: 18px;
            }
            .search-modal-header {
                padding-top: 22px;
            }
            .search-modal-grid {
                grid-template-columns: 1fr;
            }
            .search-modal-card-image {
                height: 220px;
            }
            .search-modal-card-body h3 {
                font-size: 19px;
            }
        }
    `;
    document.head.appendChild(style);
}
