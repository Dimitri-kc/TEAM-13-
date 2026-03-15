
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing &amp; Payments | LOFT &amp; LIVING</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/account_settings.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="settings-layout">

    <!--Sidebar similar to account-settings -->
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <button class="nav-item active" onclick="switchPanel('cards', this)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Payment Methods
            </button>
            <button class="nav-item" onclick="switchPanel('add', this)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                Add New Card
            </button>
            <button class="nav-item" onclick="switchPanel('history', this)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Billing History
            </button>
        </nav>
    </aside>

    <!-- Panels > sections > all cards, add card, past bills  -->
    <main class="main-panel">

        <!-- Payment Ethods > to show hold multiple card details -->
        <section class="panel active" id="panel-cards">
            <div class="panel-header">
                <h2>Payment Methods</h2>
                <p>Your saved cards — up to 5 can be stored on your account.</p>
            </div>
            <div class="panel-body">
                <div class="info-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <span>Your card details are stored securely. Your full card number is never retained.</span>
                </div>
                <div class="cards-list" id="cardsList">
                    <div style="text-align:center;padding:30px;color:var(--mid);font-size:14px;">Loading…</div> <!-- if not found -->
                </div>
                <button class="btn-primary" onclick="switchPanel('add', document.querySelectorAll('.nav-item')[1])" style="margin-top:4px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="16" height="16"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add New Card
                </button>
            </div>
        </section>

        <!-- Add Card -->
        <section class="panel" id="panel-add">
            <div class="panel-header">
                <h2>Add New Card</h2>
                <p>Your details are encrypted and stored securely.</p>
            </div>
            <div class="panel-body">
                <div class="add-card-form">
                    <div class="form-row full">
                        <div class="field">
                            <label>Cardholder Name</label>
                            <input type="text" id="cardholderName" placeholder="Name as it appears on card" autocomplete="cc-name" oninput="updatePreviewName(this.value)">
                        </div>
                    </div>
                    <div class="form-row full">
                        <div class="field">
                            <label>Card Number</label>
                            <input type="text" id="cardNumber" class="card-input" placeholder="1234 5678 9012 3456" maxlength="19" inputmode="numeric" autocomplete="cc-number" oninput="formatCardNumber(this)">
                        </div>
                    </div>
                    <div class="form-row thirds">
                        <div class="field">
                            <label>Expiry Date</label>
                            <input type="text" id="cardExpiry" placeholder="MM/YY" maxlength="5" inputmode="numeric" autocomplete="cc-exp" oninput="formatExpiry(this)">
                        </div>
                        <div class="field">
                            <label>CVV</label>
                            <input type="text" id="cardCvv" placeholder="•••" maxlength="3" inputmode="numeric" autocomplete="cc-csc">
                        </div>
                    </div>

                    <label class="field-checkbox">
                        <input type="checkbox" id="cardDefault">
                        <div>
                            <label for="cardDefault">Set as default payment method</label>
                            <span>Used automatically at checkout</span>
                        </div>
                    </label>

                    <div class="form-actions">
                        <button class="btn-primary" id="saveCardBtn" onclick="saveCard()">Save Card</button>
                        <button class="btn-ghost" onclick="switchPanel('cards', document.querySelectorAll('.nav-item')[0]); clearCardForm();">Cancel</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- PAst Bills -->
        <section class="panel" id="panel-history">
            <div class="panel-header">
                <h2>Billing History</h2>
                <p>A record of all payments made on your account.</p>
            </div>
            <div class="panel-body" style="padding: 0;">
                <div id="historyContainer">
                    <div style="text-align:center;padding:40px;color:var(--mid);font-size:14px;">Loading…</div>
                </div>
            </div>
        </section>

    </main>

</div>

<!-- Modal for remove card-->
<div class="modal-overlay" id="removeModal">
    <div class="modal-box">
        <div class="modal-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        </div>
        <h3>Remove this card?</h3>
        <p>This card will be removed from your saved payment methods. You can always add it again later.</p>
        <div class="modal-actions">
            <button class="btn-danger" id="confirmRemoveBtn" onclick="confirmRemove()">Remove Card</button>
            <button class="btn-ghost" onclick="closeRemoveModal()">Cancel</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    <span id="toastMsg"></span>
</div>

<?php include 'footer.php'; ?>
</body>
</html>