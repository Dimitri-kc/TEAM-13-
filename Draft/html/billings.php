<?php // billings.php > Billing & Payment Methods — view/add/remove saved cards, view payment history etc

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../backend/config/db_connect.php';
require_once '../backend/models/paymentModel.php';

$user_ID = $_SESSION['user_ID'] ?? null; //check user signed in
if (!$user_ID) {
    header("Location: signin.php");
    exit();
}

//api call
if ($_SERVER['REQUEST_METHOD'] === 'POST') { //handle all API calls in one endpoint
    header('Content-Type: application/json');
    $raw    = file_get_contents("php://input");
    $data   = json_decode($raw, true);
    $action = $data['action'] ?? '';

    //check if any cards & fetch
    if ($action === 'get_cards') {
        $stmt = $conn->prepare(
            "SELECT card_ID, last_four, expiry_month, expiry_year, cardholder_name, is_default
             FROM user_payment_cards
             WHERE user_ID = ?
             ORDER BY is_default DESC, card_ID ASC"
        );
        $stmt->bind_param("i", $user_ID);
        $stmt->execute();
        $cards = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        echo json_encode(["success" => true, "cards" => $cards]);
        exit();
    }

    //add a card (also checking for duplication and setting default)
    if ($action === 'add_card') {
        $cardNumber     = preg_replace('/\s+/', '', trim($data['card_number'] ?? ''));
        $expiry         = trim($data['expiry'] ?? '');
        $cvv            = trim($data['cvv'] ?? '');
        $cardholderName = trim($data['cardholder_name'] ?? '');
        $setDefault     = !empty($data['is_default']);

        //paymentModel validation
        $paymentModel = new Payment();
        if (!$cardholderName) {
            echo json_encode(["success" => false, "message" => "Cardholder name is required."]);
            exit();
        }
        if (!$paymentModel->validatePayment($cardNumber, $expiry, $cvv)) {
            echo json_encode(["success" => false, "message" => "Invalid. Please check your card details."]);
            exit();
        }

        //count existing cards (max 5)
        $cnt = $conn->prepare("SELECT COUNT(*) AS c FROM user_payment_cards WHERE user_ID = ?"); //count cards user already has
        $cnt->bind_param("i", $user_ID); //bind
        $cnt->execute();
        $count = (int)$cnt->get_result()->fetch_assoc()['c'];
        $cnt->close();

        if ($count >= 5) { //if moretthan 5
            echo json_encode(["success" => false, "message" => "You can save a maximum of 5 payment cards."]);
            exit();
        }
        //check for duplication if card is already in saved
        $last4 = substr($cardNumber, -4); //same last 4
        $expiryParts = explode('/', $expiry); //same expiry > split into month/year for DB comparison
        $expMonth = (int)($expiryParts[0] ?? 0);
        $expYear = (int)('20' . ($expiryParts[1] ?? '00'));
        //if user has card with same last 4 or expiry then error
        $dup = $conn->prepare( "SELECT card_ID FROM user_payment_cards WHERE user_ID = ? AND last_four = ? AND expiry_month = ? AND expiry_year = ?");
        $dup->bind_param("isii", $user_ID, $last4, $expMonth, $expYear);
        $dup->execute();
        if ($dup->get_result()->fetch_assoc()) {
            $dup->close();
            echo json_encode(["success" => false, "message" => "This card is already saved to your account."]);
            exit();
        }
        $dup->close();

        // If first card, force default
        if ($count === 0) $setDefault = true;
        if ($setDefault) {
            $clr = $conn->prepare("UPDATE user_payment_cards SET is_default = 0 WHERE user_ID = ?"); //if setting new card as default, unset previous default
            $clr->bind_param("i", $user_ID);
            $clr->execute();
            $clr->close();
        }
        $isDefault = $setDefault ? 1 : 0; //convert to int for DB
        $stmt = $conn->prepare( //inserty into DB > only store last 4 digits, expiry, name, default status
            "INSERT INTO user_payment_cards (user_ID, last_four, expiry_month, expiry_year, cardholder_name, is_default)
             VALUES (?, ?, ?, ?, ?, ?)" //never store full card number or CVV for security
        );
        $stmt->bind_param("isiisi", $user_ID, $last4, $expMonth, $expYear, $cardholderName, $isDefault);
        $ok = $stmt->execute();
        $stmt->close(); //json
        echo json_encode($ok ? ["success" => true,  "message" => "Card saved successfully." ] : ["success" => false, "message" => "Could not save card. Please try again."]);
        exit();
    }
    //set the card as default if option selected
    if ($action === 'set_default_card') {
        $cardID = (int)($data['card_ID'] ?? 0);
        $chk = $conn->prepare("SELECT card_ID FROM user_payment_cards WHERE card_ID = ? AND user_ID = ?");
        $chk->bind_param("ii", $cardID, $user_ID);
        $chk->execute();
        if (!$chk->get_result()->fetch_assoc()) {
            $chk->close();
            echo json_encode(["success" => false, "message" => "Card not found."]);
            exit();
        }
        $chk->close();
        //update default in db > unset previous default and set new one 
        $clr = $conn->prepare("UPDATE user_payment_cards SET is_default = 0 WHERE user_ID = ?");
        $clr->bind_param("i", $user_ID);
        $clr->execute();
        $clr->close();
        //set selected card as default
        $stmt = $conn->prepare("UPDATE user_payment_cards SET is_default = 1 WHERE card_ID = ? AND user_ID = ?");
        $stmt->bind_param("ii", $cardID, $user_ID);
        $ok = $stmt->execute();
        $stmt->close();

        echo json_encode($ok ? ["success" => true,  "message" => "Default payment method updated."] : ["success" => false, "message" => "Could not update default card."]);
        exit();
    }
    //remove card from saved payment methods
    if ($action === 'remove_card') {
        $cardID = (int)($data['card_ID'] ?? 0);

        $chk = $conn->prepare("SELECT is_default FROM user_payment_cards WHERE card_ID = ? AND user_ID = ?");
        $chk->bind_param("ii", $cardID, $user_ID); //check if card exists and belongs to user, also get if it's default
        $chk->execute();
        $existing = $chk->get_result()->fetch_assoc();
        $chk->close();

        if (!$existing) {
            echo json_encode(["success" => false, "message" => "Card not found."]);
            exit();
        }
        
        $stmt = $conn->prepare("DELETE FROM user_payment_cards WHERE card_ID = ? AND user_ID = ?");
        $stmt->bind_param("ii", $cardID, $user_ID);
        $ok = $stmt->execute();
        $stmt->close();
        echo json_encode($ok 
            ? ["success" => true,  "message" => "Card removed."]
            : ["success" => false, "message" => "Could not remove card. Please try again."]);
        exit();
    }
    echo json_encode(["success" => false, "message" => "Unknown action."]);
    exit();
}
?>

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
                    <span>Your card details are stored securely. Your full card number is never retained.</span>
                </div>
                <div class="cards-list" id="cardsList">
                    <div style="text-align:center;padding:30px;color:var(--mid);font-size:14px;">No cards saved.</div> <!-- if not found -->
                </div>
                <button class="btn-primary" onclick="switchPanel('add', document.querySelectorAll('.nav-item')[1])" style="margin-top:4px;">
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
                    <div style="text-align:center;padding:40px;color:var(--mid);font-size:14px;">No billing history available.</div>
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

<script>
    const SELF = 'billings.php';
    let removingCardID = null;

    //panel switcher for cards/history/add
    function switchPanel(name, btn) {
        document.querySelectorAll('.panel').forEach(p => p.classList.remove('active')); //hide all panels first
        document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active')); //deactivate all buttons visually
        document.getElementById('panel-' + name).classList.add('active'); //show selected panel
        btn.classList.add('active'); //activate button visually
        if (name === 'history') loadHistory();
        if (name === 'cards')   loadCards();
    }
    function showToast(msg, type = 'success') { 
        const t = document.getElementById('toast'); 
        document.getElementById('toastMsg').textContent = msg; 
        t.className = 'toast ' + type + ' show';
        setTimeout(() => t.classList.remove('show'), 3500); //hide after 3.5s
    }
    //apicall helper for all API interactions in this panel
    async function apiCall(action, payload = {}) {
        const res = await fetch(SELF, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action, ...payload })
        });
        return res.json();
    }
    //save card details to database and validate using paymentModel
    async function saveCard() {
        const cardholderName = document.getElementById('cardholderName').value.trim();
        const cardNumber     = document.getElementById('cardNumber').value.replace(/\s/g,'');
        const expiry         = document.getElementById('cardExpiry').value.trim();
        const cvv            = document.getElementById('cardCvv').value.trim();
        const isDefault      = document.getElementById('cardDefault').checked;

        if (!cardholderName) return showToast('Please enter the cardholder name.', 'error');
        if (cardNumber.length !== 16) return showToast('Card number must be 16 digits.', 'error');
        if (!/^(0[1-9]|1[0-2])\/[0-9]{2}$/.test(expiry)) return showToast('Expiry must be MM/YY format.', 'error');
        if (!/^[0-9]{3}$/.test(cvv)) return showToast('CVV must be 3 digits.', 'error');

        const btn = document.getElementById('saveCardBtn');
        btn.disabled = true; btn.textContent = 'Saving...'; //show loading state while processing > keep user informed
        const data = await apiCall('add_card', { card_number: cardNumber, expiry, cvv, cardholder_name: cardholderName, is_default: isDefault });
        btn.disabled = false; btn.textContent = 'Save Card';

        if (data.success) {
            showToast('Card saved successfully.'); //success message
            clearCardForm(); //clear
            switchPanel('cards', document.querySelectorAll('.nav-item')[0]); //switch to cards panel to show updated list
        } else {
            showToast(data.message || 'Could not save card.', 'error');
        }
    }

    function clearCardForm() { //reset form fields and preview after save
        ['cardholderName','cardNumber','cardExpiry','cardCvv'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('cardDefault').checked = false;
        document.getElementById('previewNumber').textContent = '•••• •••• •••• ••••'; //reset preview to default
        document.getElementById('previewName').textContent   = 'FULL NAME'; //reset 
        document.getElementById('previewExpiry').textContent = 'MM / YY';
    }

    async function setDefaultCard(id) { //if selected then set chosen card as default and unset previous
        const data = await apiCall('set_default_card', { card_ID: id });
        if (data.success) {
            showToast('Default payment method updated.');
            loadCards();
        } else {
            showToast(data.message || 'Could not update default.', 'error');
        }
    }

    async function confirmRemove() {
        if (!removingCardID) return;
        const btn = document.getElementById('confirmRemoveBtn');
        btn.disabled = true; btn.textContent = 'Removing...';

        const data = await apiCall('remove_card', { card_ID: removingCardID });

        btn.disabled = false; btn.textContent = 'Remove Card';
        closeRemoveModal();

        if (data.success) {
            showToast('Card removed.');
            loadCards();
        } else {
            showToast(data.message || 'Could not remove card.', 'error');
        }
    }

    document.getElementById('removeModal').addEventListener('click', function(e) {
        if (e.target === this) closeRemoveModal();
    });

    //note: 
    //complete visual preview block
    //billing history panel + api call to fetch past bills >orderconfirmation? or recentorders?
</script>

<?php include 'footer.php'; ?>
</body>
</html>