//checkout.js - handles fetching basket items and processing checkout form submission

//fetch and display basket items on checkout page
async function loadBasketItems() {
    try {
        const response = await fetch('../backend/routes/basketRoutes.php?action=view', {
            method: 'GET'
        });
        const result = await response.json();
        
        if (result.success && result.data && result.data.length > 0) {//if basket has items, display them
            const container = document.getElementById('basket-items-container');
            container.innerHTML = ''; // Clear existing content
            
            let subtotal = 0; //Initialize subtotal
            result.data.forEach(item => { //Calculate item total and add to subtotal
                const itemTotal = (parseFloat(item.price) || 0) * (parseInt(item.quantity) || 1);
                subtotal += itemTotal;
                //create HTML for each item and use item img + details
                const itemHTML = `
                    <div class="product-item">
                        <img src="${item.image || '../images/basket-images/sofa.jpg'}" alt="${item.name || 'Item'}"/>
                        <div class="product-text">
                            <p>${item.name || ''}</p>
                            <p class="price">£${parseFloat(item.price || 0).toFixed(2)}</p>
                            <p class="quantity">Quantity: ${item.quantity || 1}</p>
                            <p class="item-total">£${itemTotal.toFixed(2)}</p>
                        </div>
                    </div>
                `;
                container.innerHTML += itemHTML;
            });
            
            //update totals
            const delivery = 0; //free delivery or calculate based on rules
            const total = subtotal + delivery;
            //display totals in the summary section
            document.getElementById('subtotal').textContent = '£' + subtotal.toFixed(2);
            document.getElementById('delivery').textContent = '£' + delivery.toFixed(2);
            document.getElementById('total').textContent = '£' + total.toFixed(2);
            
        } else { //If basket is empty, show message and reset totals
            document.getElementById('basket-items-container').innerHTML = '<p>Your basket is empty.</p>';
            document.getElementById('subtotal').textContent = '£0.00';
            document.getElementById('delivery').textContent = '£0.00';
            document.getElementById('total').textContent = '£0.00';
        }
    } catch (error) { //handle errors
        console.error('Error loading basket:', error);
        document.getElementById('basket-items-container').innerHTML = '<p>Error loading basket.</p>';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadBasketItems(); //load basket items when page loads
    //form handler for checkout
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', async function(e) {
    e.preventDefault(); // Prevent default form submission
    const formData = new FormData(this);

    //combine address fields into one string for BE
    const address = [formData.get('address1'), formData.get('address2'), formData.get('city'), formData.get('state'), formData.get('postcode')].filter(Boolean).join(', '); 
    const data = { 
        action: 'checkout',
        address: address,
        card_number: formData.get('card_number'),
        expiry: formData.get('expiry'),
        cvv: formData.get('cvv')
      };
      //send to BE controller
      try {
        const response = await fetch('../backend/routes/checkoutRoutes.php', { //
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        const result = await response.json();
        if (result.success) {
            //redirect to confirmation with order ID
          window.location.href = `order_confirmation.php?order_id=${result.data.order_ID}`; // Redirect on success with order ID
        } else {
          alert('Checkout failed: ' + result.message); // Show error message
          console.error('Stock issues: ', result.data?.stock_issues); // Log stock issues if any > DEBUG
        }
      } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while processing your payment. Please try again.');
      }
    });
}
});