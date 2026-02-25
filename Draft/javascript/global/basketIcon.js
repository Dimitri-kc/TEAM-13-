//basketIcon.js > for basket counter icon within navigation bar

const basketCountEl = document.getElementById('basket-count'); //element to display count
const basketModal = document.getElementById('basket-modal'); 

//fetch curent basket and update counter icon
async function updateBasketCounter() { //asynchronous as waiting for response from server
    try {
        const res = await fetch("../backend/routes/basketRoute.php?action=view", {
            method: 'GET',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' }
        });
        const data = await res.json(); //console.log("Basket data:", data);
        if (!data.success) return; //console.error("Failed to fetch basket data:", data.message);
        const count = data.totalItems || 0;
        data.data.items.forEach(item => { 
            count += item.quantity; //update count with quantity of each item in basket
        });

        if (basketCountEl) basketCountEl.textContent = count;
    } catch (error) {
        console.error("Error updating basket icon:", error);
    }
}

async function addToBasket(productID, quantity = 1, button = null) {
    try {
        const res = await fetch("../backend/routes/basketRoute.php", {
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
        console.error("Error adding to basket:", error);
        alert("An error occurred while adding to basket. Please try again.");
        return false;
    }
}
document.addEventListener('DOMContentLoaded', updateBasketCounter); //update counter upon page load