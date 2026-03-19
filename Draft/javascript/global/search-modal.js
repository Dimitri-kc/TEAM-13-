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
                        <button type="button" class="search-modal-basket-btn" data-product-id="${item.product_ID}">+ Add to Basket</button>
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
                    <h2>Search</h2>
                    <button type="button" class="search-modal-close" aria-label="Close search">×</button>
                </div>
                <div class="search-modal-toolbar">
                    <div class="search-modal-input-wrap">
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
            background: rgba(17, 17, 17, 0.45);
            display: none;
            align-items: flex-start;
            justify-content: center;
            z-index: 11000;
            padding: 90px 18px 24px;
        }
        .search-modal-overlay.is-open { display: flex; }
        .search-modal-dialog {
            width: min(1100px, 100%);
            max-height: calc(100vh - 120px);
            overflow: hidden;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
        }
        .search-modal-header {
            position: relative;
            display: flex;
            align-items: center;
            padding: 18px 22px 12px;
            border-bottom: 1px solid #ececec;
        }
        .search-modal-header h2 {
            margin: 0;
            font-size: 24px;
        }
        .search-modal-close {
            position: absolute;
            top: 10px;
            right: 14px;
            border: none;
            background: transparent;
            font-size: 32px;
            line-height: 1;
            cursor: pointer;
            color: #333;
        }
        .search-modal-toolbar {
            padding: 16px 22px;
            border-bottom: 1px solid #f0f0f0;
        }
        .search-modal-input-wrap {
            display: block;
            width: 100%;
        }
        #global-search-input {
            width: 100%;
            min-width: 0;
            border: 1px solid #d7d7d7;
            border-radius: 12px;
            padding: 13px 15px;
            font-size: 16px;
        }
        .search-modal-meta {
            margin-top: 10px;
            color: #555;
            font-size: 14px;
        }
        .search-modal-feedback {
            position: absolute;
            top: 18px;
            left: 50%;
            transform: translate(-50%, -12px);
            background: rgba(17, 17, 17, 0.92);
            color: #fff;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
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
            padding: 22px;
        }
        .search-modal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 18px;
        }
        .search-modal-card {
            border: 1px solid #ececec;
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
        }
        .search-modal-card-link {
            text-decoration: none;
            color: inherit;
        }
        .search-modal-card-image {
            width: 100%;
            height: 210px;
            object-fit: cover;
            display: block;
        }
        .search-modal-card-body {
            padding: 12px;
            display: flex;
            flex: 1;
            flex-direction: column;
        }
        .search-modal-card-body h3 {
            margin: 0 0 8px;
            font-size: 17px;
        }
        .search-modal-card-category {
            margin: 0 0 6px;
            font-size: 13px;
            color: #666;
        }
        .search-modal-card-price {
            margin: 0 0 12px;
            font-weight: 700;
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
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.08);
            color: #444;
            cursor: pointer;
            font-size: 20px;
        }
        .search-modal-favourite-btn.is-active {
            background: #111;
            color: #fff;
        }
        .search-modal-basket-btn {
            border: 1.5px solid #6f675c;
            border-radius: 32px;
            padding: 10px 20px;
            background: #6f675c;
            color: #fff;
            cursor: pointer;
            font-weight: 500;
            font-size: 15px;
            line-height: 1.2;
            font-family: 'mr-eaves-modern', 'Mr Eaves Modern', Arial, sans-serif;
            letter-spacing: 0.03em;
            box-shadow: 0 2px 12px rgba(43, 43, 43, 0.08);
            transition: background 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease;
            white-space: nowrap;
        }
        .search-modal-basket-btn:hover {
            background: #595247;
            border-color: #595247;
            transform: translateY(-2px);
            box-shadow: 0 4px 18px rgba(43, 43, 43, 0.13);
        }
        .search-modal-basket-btn:disabled {
            opacity: 1;
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
            border: 1px dashed #d8d8d8;
            border-radius: 14px;
            padding: 26px 16px;
            color: #444;
        }
        .search-modal-did-you-mean {
            margin-top: 10px;
        }
        .search-modal-suggestion-btn {
            border: none;
            background: transparent;
            color: #0d4fd6;
            cursor: pointer;
            font-weight: 600;
            padding: 0;
        }
        @media (max-width: 640px) {
            .search-modal-overlay {
                padding-top: 70px;
            }
            .search-modal-dialog {
                max-height: calc(100vh - 90px);
            }
            .search-modal-grid {
                grid-template-columns: 1fr;
            }
        }
    `;
    document.head.appendChild(style);
}
