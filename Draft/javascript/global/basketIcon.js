//basketIcon.js > for basket counter icon within navigation bar

const basketCountEl = document.getElementById('basket-count'); //element to display count
const basketModal = document.getElementById('basket-modal'); 
const BASKET_API = "/TEAM-13-/Draft/backend/routes/basketRoutes.php"; //centralized API endpoint 
const bToastStyle = document.createElement('style'); //style element for toast notifications
const basketIcon = document.querySelector('.basket-icon'); 

//added stylings for toast notifications to document head
function basketToastStyle() {
    if (document.getElementById('basket-toast')) return; //prevent duplicates
    bToastStyle.id = 'basket-toast-style';
    bToastStyle.textContent = `
        .basket-icon { position: relative;}
        .basket-toast {
            position:absolute;
            top: calc(100% + 8px);
            right 0;
            background: #333; 
            color: #fff;
            font-size: 12px;
            padding: 8px 12px;
            border-radius: 4px;
            opacity: 0;
            transform: translateY(-10px) scale(0.8);
            transform-origin: top right;
            transition: opacity 0.3s ease, transform 0.3s ease;
            pointer-events: none;
            z-index: 10000;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
            .basket-toast.show { animation: toastFadeIn 0.3s ease-out forwards;
            .basket-toast.hide { animation: toastFadeOut 0.3s ease-in forwards;
            @keyframes toastFadeIn {
            to {opacity: 1; transform: translateY(0) scale(1);}}
            @keyframes toastFadeOut {
            to {opacity: 0; transform: translateY(-10px) scale(0.8);}}
    `;
    document.head.appendChild(bToastStyle); //add styles to document head
}

function basketToastEl() {
    if (!basketIcon) return null; //ensure basket icon exists
    if (basketToastEl) return basketToastEl; //return existing toast element if already created
    basketToastStyle(); //ensure styles are added before creating toast element
}

function showBasketToast() {}

//fetch curent basket and update counter icon
async function updateBasketCounter() { //asynchronous as waiting for response from server
    try {
        const res = await fetch(`${BASKET_API}?action=count`, {
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' }
        });
        const data = await res.json(); //console.log("Basket data:", data);
        if (!data.success) return; //console.error("Failed to fetch basket data:", data.message);
        let count = 0;
        if (data.success && data.data && typeof data.data.count !== 'undefined') {
            count = parseInt(data.data.count);
        } else {
            count = 0; //default to 0 if count not provided by server
        }
        
        /* data.data.items.forEach(item => { 
            count += Number(item.quantity) || 0; //update count with quantity of each item in basket
        }); */

        if (basketCountEl) basketCountEl.textContent = count;
    } catch (error) {
        console.error("Error updating basket icon: ", error);
    }
}

async function addToBasket(productID, quantity = 1, button = null) {
    try {
        const res = await fetch(BASKET_API, {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'add', product_ID: productID, quantity })
        });
        const data = await res.json();
        if (!data.success) {
            alert(data.message); // show server error msg
            return false;
        }
        await updateBasketCounter(); //update counter after adding to basket

        if(button) button.classList.add('added'); //visual feedback for added item
        if (basketModal) basketModal.style.display = 'flex'; //confirmation modal
        return true;
    } catch (error) {
        console.error("Error adding to basket: ", error);
        alert("An error occurred while adding to basket. Please try again.");
        return false;
    }
}
document.addEventListener('DOMContentLoaded', () => {
    updateBasketCounter(); //update counter upon page load
    const goToBasketBtn = document.getElementById('go-to-basket');
    const continueShoppingBtn = document.getElementById('continue-shopping');
    if (goToBasketBtn) {
        goToBasketBtn.addEventListener('click', () => {
            window.location.href = 'basket.php'; //navigate to basket page
        });
    }
    if (continueShoppingBtn) {
        continueShoppingBtn.addEventListener('click', () => {
            basketModal.style.display = 'none'; //hide modal
        });
    }
    if (basketModal) {
        basketModal.addEventListener('click', (e) => {
            if (e.target === basketModal) basketModal.style.display = 'none'; //hide modal when clicking outside content
        });
    }
});
window.addToBasket = addToBasket; //make function globally accessible for product pages
//document.addEventListener('DOMContentLoaded', updateBasketCounter); //update counter upon page load
