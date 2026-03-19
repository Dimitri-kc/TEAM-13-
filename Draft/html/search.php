<?php
session_start();
include '../backend/config/db_connect.php';

$searchQuery = trim((string)($_GET['q'] ?? ''));
$searchQueryLower = mb_strtolower($searchQuery);

if ($searchQuery !== '') {
    $_SESSION['last_search_query'] = $searchQuery;
} elseif (isset($_SESSION['last_search_query'])) {
    unset($_SESSION['last_search_query']);
}

$categoryLabels = [
    1 => 'Living Room',
    2 => 'Kitchen',
    3 => 'Office',
    4 => 'Bathroom',
    5 => 'Bedroom',
];

$categoryAliases = [
    1 => ['living', 'living room', 'lounge', 'livingroom', 'living-room'],
    2 => ['kitchen', 'dining'],
    3 => ['office', 'desk', 'workspace', 'study'],
    4 => ['bathroom', 'bath', 'toilet', 'shower'],
    5 => ['bedroom', 'bed', 'sleep'],
];

function tokenize_search_terms(string $query): array {
    $parts = preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($query)) ?: [];
    $tokens = [];

    foreach ($parts as $part) {
        $part = trim($part);
        if ($part !== '' && mb_strlen($part) >= 2) {
            $tokens[$part] = true;
        }
    }

    return array_keys($tokens);
}

function fuzzy_token_match(string $token, string $source): bool {
    $words = preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($source)) ?: [];
    foreach ($words as $word) {
        if ($word === '') {
            continue;
        }
        if (mb_substr($word, 0, 1) !== mb_substr($token, 0, 1)) {
            continue;
        }
        if (abs(mb_strlen($word) - mb_strlen($token)) > 1) {
            continue;
        }
        if (levenshtein($token, $word) <= 1) {
            return true;
        }
    }
    return false;
}

function has_word_token(string $text, string $token): bool {
    $textLower = mb_strtolower($text);
    $tokenLower = mb_strtolower($token);
    if ($tokenLower === '') {
        return false;
    }

    $pattern = '/(?<![\p{L}\p{N}])' . preg_quote($tokenLower, '/') . '(?![\p{L}\p{N}])/u';
    return preg_match($pattern, $textLower) === 1;
}

function best_search_suggestion(string $query, array $candidates): ?string {
    $query = trim(mb_strtolower($query));
    if ($query === '' || empty($candidates)) {
        return null;
    }

    $best = null;
    $bestDistance = PHP_INT_MAX;

    foreach ($candidates as $candidate) {
        $normalized = trim(mb_strtolower((string)$candidate));
        if ($normalized === '') {
            continue;
        }

        $distance = levenshtein($query, $normalized);
        if ($distance < $bestDistance) {
            $bestDistance = $distance;
            $best = (string)$candidate;
        }
    }

    if ($best === null) {
        return null;
    }

    $threshold = max(2, (int)floor(mb_strlen($query) * 0.45));
    if ($bestDistance > $threshold) {
        return null;
    }

    return $best;
}

$tokens = tokenize_search_terms($searchQuery);

$intentCategoryIds = [];
$intentAliasTokens = [];
if ($searchQueryLower !== '') {
    foreach ($categoryAliases as $categoryId => $aliases) {
        foreach ($aliases as $alias) {
            if (mb_stripos($searchQueryLower, $alias) !== false) {
                $intentCategoryIds[$categoryId] = true;
                $aliasTokens = tokenize_search_terms($alias);
                foreach ($aliasTokens as $aliasToken) {
                    $intentAliasTokens[$aliasToken] = true;
                }
            }
        }
    }
}

$favouriteProductIds = [];
if (isset($_SESSION['user_ID'])) {
    $userId = (int) $_SESSION['user_ID'];
    $favStmt = $conn->prepare('SELECT product_ID FROM favourites WHERE user_ID = ?');
    $favStmt->bind_param('i', $userId);
    $favStmt->execute();
    $favResult = $favStmt->get_result();
    while ($favRow = $favResult->fetch_assoc()) {
        $favouriteProductIds[] = (int) $favRow['product_ID'];
    }
    $favStmt->close();
}

$productRows = [];
$productsResult = mysqli_query($conn, 'SELECT * FROM products');
if ($productsResult) {
    while ($row = mysqli_fetch_assoc($productsResult)) {
        $productRows[] = $row;
    }
}

$suggestionCandidatesMap = [];
foreach ($categoryLabels as $label) {
    $suggestionCandidatesMap[mb_strtolower($label)] = $label;
}

foreach ($productRows as $row) {
    $name = trim((string)($row['name'] ?? ''));
    if ($name !== '') {
        $suggestionCandidatesMap[mb_strtolower($name)] = $name;
    }

    $colour = trim((string)($row['colour'] ?? ''));
    if ($colour !== '') {
        $suggestionCandidatesMap[mb_strtolower($colour)] = $colour;
    }

    $keywords = (string)($row['keywords'] ?? '');
    $keywordParts = preg_split('/[,;|\/]+/', $keywords) ?: [];
    foreach ($keywordParts as $part) {
        $candidate = trim($part);
        if ($candidate !== '' && mb_strlen($candidate) >= 3) {
            $suggestionCandidatesMap[mb_strtolower($candidate)] = $candidate;
        }
    }
}

$results = [];
$hasCategoryIntent = !empty($intentCategoryIds);
$nonCategoryTokens = array_values(array_filter($tokens, static function (string $token) use ($intentAliasTokens): bool {
    return !isset($intentAliasTokens[$token]);
}));
$isStrictSingleTokenSearch = !$hasCategoryIntent && count($tokens) === 1;

foreach ($productRows as $row) {
    $name = (string)($row['name'] ?? '');
    $keywords = (string)($row['keywords'] ?? '');
    $description = (string)($row['description'] ?? '');
    $colour = (string)($row['colour'] ?? '');
    $categoryId = (int)($row['category_id'] ?? 0);
    $categoryName = $categoryLabels[$categoryId] ?? 'Uncategorised';

    if ($hasCategoryIntent && !isset($intentCategoryIds[$categoryId])) {
        continue;
    }

    $nameLower = mb_strtolower($name);
    $keywordsLower = mb_strtolower($keywords);
    $descriptionLower = mb_strtolower($description);
    $colourLower = mb_strtolower($colour);
    $categoryLower = mb_strtolower($categoryName);

    $score = 0;
    $isStrongMatch = false;
    $tokenCoreHitCount = 0;
    $nonCategoryTokenHitCount = 0;
    $fuzzyHitCount = 0;
    $nameTokenHitCount = 0;

    if ($searchQueryLower === '') {
        $score = 0;
    } else {
        $isExactName = ($nameLower === $searchQueryLower);
        $isNameContains = str_contains($nameLower, $searchQueryLower);
        $isKeywordContains = str_contains($keywordsLower, $searchQueryLower);
        $isColourContains = str_contains($colourLower, $searchQueryLower);
        $isCategoryContains = str_contains($categoryLower, $searchQueryLower);

        if ($isExactName) {
            $score += 140;
            $isStrongMatch = true;
        }
        if ($isNameContains) {
            $score += 90;
            $isStrongMatch = true;
        }
        if ($isKeywordContains) {
            $score += 70;
            $isStrongMatch = true;
        }
        if ($isColourContains) {
            $score += 70;
            $isStrongMatch = true;
        }
        if ($isCategoryContains) {
            $score += 60;
            $isStrongMatch = true;
        }

        foreach ($tokens as $token) {
            $isTokenInName = has_word_token($nameLower, $token);
            $isTokenInKeywords = has_word_token($keywordsLower, $token);
            $isTokenInColour = has_word_token($colourLower, $token);
            $isTokenInCategory = has_word_token($categoryLower, $token);
            $isTokenInDescription = has_word_token($descriptionLower, $token);

            $isCoreTokenHit = $isTokenInName || $isTokenInKeywords || $isTokenInColour || $isTokenInCategory;

            if ($isTokenInName) {
                $score += 32;
                $nameTokenHitCount++;
            }
            if ($isTokenInKeywords) {
                $score += 24;
            }
            if ($isTokenInColour) {
                $score += 24;
            }
            if ($isTokenInCategory) {
                $score += 22;
            }

            if ($isCoreTokenHit) {
                $tokenCoreHitCount++;
            }

            if (in_array($token, $nonCategoryTokens, true) && ($isTokenInName || $isTokenInKeywords || $isTokenInColour)) {
                $nonCategoryTokenHitCount++;
            }

            if (!$isCoreTokenHit && mb_strlen($token) >= 5 && fuzzy_token_match($token, $name)) {
                $score += 10;
                $fuzzyHitCount++;
            }
        }

        if (isset($intentCategoryIds[$categoryId])) {
            $score += 45;
        }

        if (!$hasCategoryIntent) {
            $isStrongMatch = $isStrongMatch || $tokenCoreHitCount > 0 || $fuzzyHitCount > 0;
        } else {
            if (count($nonCategoryTokens) === 0) {
                $isStrongMatch = true;
            } else {
                $isStrongMatch = ($nonCategoryTokenHitCount === count($nonCategoryTokens));
            }
        }

        if ($isStrictSingleTokenSearch) {
            $isStrongMatch = $nameTokenHitCount > 0
                || $isExactName
                || has_word_token($nameLower, $tokens[0])
                || has_word_token($colourLower, $tokens[0]);
        }
    }

    if ($isStrongMatch && $score >= 18) {
        $row['category_label'] = $categoryName;
        $row['_score'] = $score;
        $results[] = $row;
    }
}

usort($results, static function (array $a, array $b): int {
    $scoreCompare = ($b['_score'] ?? 0) <=> ($a['_score'] ?? 0);
    if ($scoreCompare !== 0) {
        return $scoreCompare;
    }
    return strcmp((string)($a['name'] ?? ''), (string)($b['name'] ?? ''));
});

$didYouMean = null;
if ($searchQuery !== '' && count($results) === 0) {
    $didYouMean = best_search_suggestion($searchQuery, array_values($suggestionCandidatesMap));
    if ($didYouMean !== null && mb_strtolower($didYouMean) === $searchQueryLower) {
        $didYouMean = null;
    }
}

if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json');

    $payloadResults = [];
    foreach ($results as $row) {
        $payloadResults[] = [
            'product_ID' => (int)$row['product_ID'],
            'name' => (string)$row['name'],
            'price' => (string)$row['price'],
            'image' => (string)$row['image'],
            'category_label' => (string)$row['category_label'],
            'isFavourite' => in_array((int)$row['product_ID'], $favouriteProductIds, true),
        ];
    }

    echo json_encode([
        'success' => true,
        'query' => $searchQuery,
        'resultsCount' => count($payloadResults),
        'didYouMean' => $didYouMean,
        'results' => $payloadResults,
    ]);
    exit;
}

header('Location: homepage.php');
exit;

$pageTitle = 'Search | LOFT &amp; LIVING';
$extraHeadContent = <<<'HTML'
<link rel="stylesheet" href="../css/favourites-toggle.css">
<style>
main.search-page {
    max-width: 1200px;
    margin: 130px auto 70px;
    padding: 0 20px;
}

.search-header h1 {
    font-size: 34px;
    margin-bottom: 14px;
}

.search-bar {
    display: flex;
    gap: 10px;
    margin-bottom: 18px;
}

.search-bar input {
    flex: 1;
    border: 1px solid #d9d9d9;
    border-radius: 10px;
    padding: 12px 14px;
    font-size: 16px;
}

.search-bar button {
    border: none;
    border-radius: 10px;
    padding: 12px 20px;
    font-weight: 600;
    background: #111;
    color: #fff;
    cursor: pointer;
}

.search-meta {
    margin-bottom: 20px;
    color: #444;
}

.search-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
}

.search-card {
    border: 1px solid #ececec;
    border-radius: 14px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
}

.search-card a {
    text-decoration: none;
    color: inherit;
}

.search-card img {
    width: 100%;
    height: 210px;
    object-fit: cover;
    display: block;
}

.search-card-body {
    padding: 12px;
    display: flex;
    flex: 1;
    flex-direction: column;
}

.search-card-body h2 {
    font-size: 17px;
    margin: 0 0 8px;
}

.search-card-body .category {
    color: #666;
    font-size: 13px;
    margin-bottom: 6px;
}

.search-card-body .price {
    font-weight: 700;
    margin: 0 0 10px;
}

.card-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
    padding-top: 14px;
}

.search-page .card-actions .favourite-toggle-form {
    position: static;
    top: auto;
    left: auto;
    z-index: auto;
}

.search-page .card-actions .favourite-toggle-btn {
    font-size: 20px;
    width: 34px;
    height: 34px;
}

.search-page .basket-add-btn {
    border: 1.5px solid #6f675c !important;
    border-radius: 32px !important;
    padding: 10px 22px !important;
    background: #6f675c !important;
    color: #fff !important;
    cursor: pointer;
    font-weight: 500 !important;
    font-size: 15px !important;
    line-height: 1.2 !important;
    font-family: 'mr-eaves-modern', 'Mr Eaves Modern', Arial, sans-serif !important;
    letter-spacing: 0.03em !important;
    box-shadow: 0 2px 12px rgba(43, 43, 43, 0.08) !important;
    transition: background 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease;
    white-space: nowrap;
}

.search-page .basket-add-btn:hover {
    background: #595247 !important;
    border-color: #595247 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 18px rgba(43, 43, 43, 0.13) !important;
}

html.dark-mode .search-page .basket-add-btn {
    background: #8c8376 !important;
    border-color: #8c8376 !important;
    color: #fff !important;
}

html.dark-mode .search-page .basket-add-btn:hover {
    background: #a1978a !important;
    border-color: #a1978a !important;
}

.no-results {
    padding: 28px 14px;
    border: 1px dashed #cfcfcf;
    border-radius: 12px;
    color: #333;
}

.did-you-mean {
    margin-top: 12px;
    font-size: 15px;
}

.did-you-mean a {
    color: #0d4fd6;
    text-decoration: none;
    font-weight: 600;
}

.did-you-mean a:hover {
    text-decoration: underline;
}

.search-placeholder {
    padding: 24px 12px;
    border: 1px dashed #d3d3d3;
    border-radius: 12px;
    color: #444;
}

.basket-modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(22, 18, 15, 0.42);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    z-index: 9999;
}

.basket-modal.active {
    display: flex;
}

.basket-modal-content {
    background: rgba(255, 255, 255, 0.96);
    border: 1px solid rgba(111, 103, 92, 0.14);
    border-radius: 24px;
    width: min(92vw, 380px);
    padding: 28px 28px 24px;
    text-align: center;
    box-shadow: 0 18px 42px rgba(44, 31, 22, 0.16);
}

.basket-modal-content p {
    margin: 0 0 18px;
    font-family: 'ivybodoni', serif;
    font-size: 28px;
    font-weight: 500;
    line-height: 1.12;
    color: #1f1a17;
}

.basket-modal-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
    flex-wrap: wrap;
}

.basket-modal-buttons button {
    padding: 12px 18px;
    border: 1.5px solid #8c8376;
    border-radius: 999px;
    background: #8c8376;
    color: #fff;
    font-family: 'mr-eaves-modern', sans-serif;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.18s ease, border-color 0.18s ease, transform 0.18s ease, color 0.18s ease;
}

#go-to-basket {
    background: #8c8376;
    color: #fff;
}

#go-to-basket:hover {
    background: #6f675c;
    border-color: #6f675c;
    transform: translateY(-1px);
}

#continue-shopping {
    background: transparent;
    color: #6f675c;
    border-color: rgba(111, 103, 92, 0.26);
}

#continue-shopping:hover {
    background: rgba(111, 103, 92, 0.08);
    color: #4d463d;
    border-color: rgba(111, 103, 92, 0.4);
    transform: translateY(-1px);
}
</style>
HTML;

include 'header.php';
?>

<main class="search-page">
    <section class="search-header">
        <h1>Search Products</h1>
        <form class="search-bar" method="get" action="search.php" id="live-search-form">
            <input
                type="text"
                name="q"
                id="live-search-input"
                value="<?php echo htmlspecialchars($searchQuery); ?>"
                placeholder="Search by name, category, keyword..."
                autocomplete="off"
            >
            <button type="submit">Search</button>
        </form>
        <div class="search-meta" id="search-meta">
            <?php if ($searchQuery !== ''): ?>
                <?php echo count($results); ?> result(s) for "<?php echo htmlspecialchars($searchQuery); ?>"
            <?php else: ?>
                Start typing to search products live.
            <?php endif; ?>
        </div>
    </section>

    <section id="live-results-region">
        <?php if ($searchQuery === ''): ?>
            <div class="search-placeholder">Start typing to search by product, category, or keyword.</div>
        <?php elseif (count($results) === 0): ?>
            <div class="no-results">
                No products matched your search. Try a broader term.
                <?php if ($didYouMean !== null): ?>
                    <div class="did-you-mean">
                        Did you mean
                        <a href="search.php?q=<?php echo urlencode($didYouMean); ?>">
                            <?php echo htmlspecialchars($didYouMean); ?>
                        </a>
                        ?
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <section class="search-grid">
                <?php foreach ($results as $row): ?>
                    <?php $isFavourite = in_array((int)$row['product_ID'], $favouriteProductIds, true); ?>
                    <article class="search-card">
                        <a href="product.php?id=<?php echo (int)$row['product_ID']; ?>">
                            <img src="../images/<?php echo htmlspecialchars((string)$row['image']); ?>" alt="<?php echo htmlspecialchars((string)$row['name']); ?>">
                        </a>
                        <div class="search-card-body">
                            <a href="product.php?id=<?php echo (int)$row['product_ID']; ?>">
                                <h2><?php echo htmlspecialchars((string)$row['name']); ?></h2>
                            </a>
                            <p class="category"><?php echo htmlspecialchars((string)$row['category_label']); ?></p>
                            <p class="price">£<?php echo htmlspecialchars((string)$row['price']); ?></p>

                            <div class="card-actions">
                                <form method="post" action="favourite_toggle.php" class="favourite-toggle-form js-favourite-form">
                                    <input type="hidden" name="product_id" value="<?php echo (int)$row['product_ID']; ?>">
                                    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                                    <button
                                        type="submit"
                                        class="favourite-toggle-btn js-favourite-button<?php echo $isFavourite ? ' is-active' : ''; ?>"
                                        data-favourite-state="<?php echo $isFavourite ? 'true' : 'false'; ?>"
                                        aria-pressed="<?php echo $isFavourite ? 'true' : 'false'; ?>"
                                        title="<?php echo $isFavourite ? 'Remove from favourites' : 'Add to favourites'; ?>"
                                    ><?php echo $isFavourite ? '♥' : '♡'; ?></button>
                                </form>

                                <button
                                    type="button"
                                    class="basket-add-btn"
                                    onclick="addToBasket(<?php echo (int)$row['product_ID']; ?>, 1, this)"
                                >Add to Basket</button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </section>
</main>

<div id="basket-modal" class="basket-modal">
    <div class="basket-modal-content">
        <p>Item added to basket!</p>
        <div class="basket-modal-buttons">
            <button id="go-to-basket" type="button">Proceed to Basket</button>
            <button id="continue-shopping" type="button">Continue Shopping</button>
        </div>
    </div>
</div>

<script src="../javascript/favourites-toggle.js"></script>
<script>
(() => {
    const input = document.getElementById('live-search-input');
    const form = document.getElementById('live-search-form');
    const meta = document.getElementById('search-meta');
    const region = document.getElementById('live-results-region');
    const pageUrl = 'search.php';
    let debounceId = null;

    if (!input || !form || !meta || !region) {
        return;
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderPlaceholder() {
        meta.textContent = 'Start typing to search products live.';
        region.innerHTML = '<div class="search-placeholder">Start typing to search by product, category, or keyword.</div>';
    }

    function renderNoResults(query, didYouMean) {
        let html = 'No products matched your search. Try a broader term.';
        if (didYouMean) {
            html += `<div class="did-you-mean">Did you mean <a href="${pageUrl}?q=${encodeURIComponent(didYouMean)}">${escapeHtml(didYouMean)}</a>?</div>`;
        }
        meta.textContent = `0 result(s) for "${query}"`;
        region.innerHTML = `<div class="no-results">${html}</div>`;
    }

    function cardMarkup(item) {
        const heart = item.isFavourite ? '♥' : '♡';
        const activeClass = item.isFavourite ? ' is-active' : '';
        const pressed = item.isFavourite ? 'true' : 'false';
        const title = item.isFavourite ? 'Remove from favourites' : 'Add to favourites';

        return `
            <article class="search-card">
                <a href="product.php?id=${item.product_ID}">
                    <img src="../images/${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}">
                </a>
                <div class="search-card-body">
                    <a href="product.php?id=${item.product_ID}">
                        <h2>${escapeHtml(item.name)}</h2>
                    </a>
                    <p class="category">${escapeHtml(item.category_label)}</p>
                    <p class="price">£${escapeHtml(item.price)}</p>
                    <div class="card-actions">
                        <form method="post" action="favourite_toggle.php" class="favourite-toggle-form js-favourite-form">
                            <input type="hidden" name="product_id" value="${item.product_ID}">
                            <input type="hidden" name="redirect" value="${escapeHtml(window.location.pathname + window.location.search)}">
                            <button type="submit" class="favourite-toggle-btn js-favourite-button${activeClass}" data-favourite-state="${item.isFavourite ? 'true' : 'false'}" aria-pressed="${pressed}" title="${title}">${heart}</button>
                        </form>
                        <button type="button" class="basket-add-btn" onclick="addToBasket(${item.product_ID}, 1, this)">Add to Basket</button>
                    </div>
                </div>
            </article>
        `;
    }

    function bindFavouriteForms() {
        const forms = region.querySelectorAll('.js-favourite-form');
        forms.forEach((formEl) => {
            if (formEl.dataset.bound === 'true') {
                return;
            }
            formEl.dataset.bound = 'true';

            const button = formEl.querySelector('.js-favourite-button');
            if (!button) {
                return;
            }

            formEl.addEventListener('submit', async (event) => {
                event.preventDefault();
                if (button.disabled) {
                    return;
                }

                button.disabled = true;
                try {
                    const response = await fetch(formEl.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new FormData(formEl)
                    });

                    const payload = await response.json();
                    if (!response.ok || !payload.success) {
                        if (payload.redirect) {
                            window.location.href = payload.redirect;
                            return;
                        }
                        throw new Error(payload.message || 'Could not update favourites.');
                    }

                    const isFav = !!payload.isFavourite;
                    button.dataset.favouriteState = isFav ? 'true' : 'false';
                    button.classList.toggle('is-active', isFav);
                    button.setAttribute('aria-pressed', isFav ? 'true' : 'false');
                    button.setAttribute('title', isFav ? 'Remove from favourites' : 'Add to favourites');
                    button.textContent = isFav ? '♥' : '♡';
                } catch (error) {
                    formEl.submit();
                    return;
                } finally {
                    button.disabled = false;
                }
            });
        });
    }

    async function runLiveSearch() {
        const query = input.value.trim();
        const nextUrl = query ? `${pageUrl}?q=${encodeURIComponent(query)}` : pageUrl;
        window.history.replaceState({}, '', nextUrl);

        if (!query) {
            renderPlaceholder();
            return;
        }

        meta.textContent = 'Searching...';
        try {
            const response = await fetch(`${pageUrl}?ajax=1&q=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json' }
            });
            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error('Search failed');
            }

            if (!payload.results || payload.results.length === 0) {
                renderNoResults(query, payload.didYouMean || null);
                return;
            }

            meta.textContent = `${payload.resultsCount} result(s) for "${query}"`;
            region.innerHTML = `<section class="search-grid">${payload.results.map(cardMarkup).join('')}</section>`;
            bindFavouriteForms();
        } catch (error) {
            meta.textContent = 'Could not update search right now.';
            region.innerHTML = '<div class="no-results">Something went wrong. Please try again.</div>';
        }
    }

    input.addEventListener('input', () => {
        window.clearTimeout(debounceId);
        debounceId = window.setTimeout(runLiveSearch, 220);
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        runLiveSearch();
    });

    if (input.value.trim() !== '') {
        bindFavouriteForms();
    }
})();
</script>

<?php include 'footer.php'; ?>
