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

//Toast notification for favourites
function showFavouriteToast(message) {
    let toast = document.getElementById('fav-toggle-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'fav-toggle-toast';
        toast.style.cssText = `
            position: fixed;
            top: 110px;
            right: 24px;
            background: rgba(33, 33, 33, 0.95);
            color: #fff;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            z-index: 5000;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            animation: fadeInOut 2s ease-in-out;
        `;
        document.body.appendChild(toast);
        
        // Add animation styles if not already present
        if (!document.getElementById('fav-toast-styles')) {
            const style = document.createElement('style');
            style.id = 'fav-toast-styles';
            style.textContent = `
                @keyframes fadeInOut {
                    0% { opacity: 0; transform: translateY(-10px); }
                    10% { opacity: 1; transform: translateY(0); }
                    90% { opacity: 1; transform: translateY(0); }
                    100% { opacity: 0; transform: translateY(-10px); }
                }
            `;
            document.head.appendChild(style);
        }
    }
    
    toast.textContent = message;
    setTimeout(() => {
        if (toast.parentNode) toast.parentNode.removeChild(toast);
    }, 2000);
}

//Toggle favourite function for category pages
async function toggleFavourite(button, event) {
    event.preventDefault();
    event.stopPropagation();
    
    const productId = button.getAttribute('data-product-id');
    const isFavourite = button.classList.contains('is-favourite');
    const heart = button.querySelector('.fav-heart');
    
    if (!productId) {
        console.error("No product ID found on button");
        return;
    }
    
    try {
        // Determine if we're in a subdirectory by checking current location
        const pathParts = window.location.pathname.split('/');
        let basePath = '';
        for (let i = 0; i < pathParts.length - 1; i++) {
            if (pathParts[i]) basePath += '/' + pathParts[i];
        }
        
        const endpoint = isFavourite ? 
            basePath + '/favourite_remove.php' : 
            basePath + '/favourites_add.php';
        
        console.log('Toggling favourite for product:', productId, 'Endpoint:', endpoint);
        
        const response = await fetch(endpoint, {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'product_id=' + encodeURIComponent(productId) + '&redirect=false'
        });
        
        console.log('Response status:', response.status);
        
        if (response.ok) {
            if (isFavourite) {
                button.classList.remove('is-favourite');
                heart.textContent = '♡';
                showFavouriteToast('Removed from favourites');
            } else {
                button.classList.add('is-favourite');
                heart.textContent = '♥';
                showFavouriteToast('Added to favourites');
            }
        } else {
            console.error('Failed to toggle favourite:', response.status, response.statusText);
            showFavouriteToast('Error updating favourites');
        }
    } catch (error) {
        console.error("Error toggling favourite: ", error);
        showFavouriteToast('Error updating favourites');
    }
}
window.toggleFavourite = toggleFavourite; //make function globally accessible

//document.addEventListener('DOMContentLoaded', updateBasketCounter); //update counter upon page load
