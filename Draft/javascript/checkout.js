 //checkout.js - handles fetching basket items and processing checkout form submission

//fetch and display basket items on checkout page
async function loadBasketItems() {
    try {
        const response = await fetch('../backend/routes/basketRoutes.php?action=view', {
            method: 'GET'
        });

        //check if response is ok - debugging
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const result = await response.json();
        
        if (result.success && result.data && Array.isArray(result.data) && result.data.length > 0) {//if basket has items, display them
            console.log('Found', result.data.length, 'items'); //debugging
            const container = document.getElementById('basket-items-container');
            if (!container) { //debuggin
                console.error('ERROR: basket-items-container element not found!');
                return;
            }
            container.innerHTML = ''; // Clear existing content
            
            let subtotal = 0; //Initialize subtotal
            result.data.forEach((item, index) => { 
                console.log(`Processing item ${index}:`, item); //log for debugging
                //Calculate item total and add to subtotal
                const price = parseFloat(item.price) || 0;
                const quantity = parseInt(item.quantity) || 1;
                const itemTotal = price * quantity;
                subtotal += itemTotal;

                //create HTML for each item and use item img + details
                const itemHTML = `
                    <div class="product-item">
                        <img src="${item.image || '../images/basket-images/sofa.jpg'}" alt="${item.name || 'Item'}"/>
                        <div class="product-text">
                            <p>${item.name || 'Unknown Item'}</p>
                            <p class="price">£${price.toFixed(2)}</p>
                            <p class="quantity">Quantity: ${quantity}</p>
                            <p class="item-total">£${itemTotal.toFixed(2)}</p>
                        </div>
                    </div>
                `;
                container.innerHTML += itemHTML;
            });
            console.log('Subtotal calculated successfully:', subtotal); //debug
            
            //update totals
            const delivery = 0; //free delivery or calculate based on rules
            const total = subtotal + delivery;
            //display totals in the summary section
            const subtotalEl = document.getElementById('subtotal');
            const deliveryEl = document.getElementById('delivery');
            const totalEl = document.getElementById('total');
            //format to 2 decimal places and add £ symbol, also check if elements exist before updating
            if (subtotalEl) subtotalEl.textContent = '£' + subtotal.toFixed(2);
            if (deliveryEl) deliveryEl.textContent = '£' + delivery.toFixed(2);
            if (totalEl) totalEl.textContent = '£' + total.toFixed(2);
            console.log('Basket loaded successfully');
            
        } else { //If basket is empty, show message and reset totals
            const container = document.getElementById('basket-items-container');
            if (container) {
                container.innerHTML = '<p>Your basket is empty.</p>';
            }
            //reset totals to £0.00
            const subtotalEl = document.getElementById('subtotal'); 
            const deliveryEl = document.getElementById('delivery');
            const totalEl = document.getElementById('total');
            if (subtotalEl) subtotalEl.textContent = '£0.00';
            if (deliveryEl) deliveryEl.textContent = '£0.00';
            if (totalEl) totalEl.textContent = '£0.00';
        }
    } catch (error) { //handle errors
        console.error('Error loading basket:', error);
        console.error('Error stack:', error.stack);
        const container = document.getElementById('basket-items-container');
        if (container) {
            container.innerHTML = '<p>Error loading basket. Please try refreshing the page.</p>';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadBasketItems(); //load basket items when page loads

    //form handler for checkout
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', async function(e) {
    e.preventDefault(); // Prevent default form submission
    console.log('Form submitted');
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
          window.location.href = `orderconfirmation.php?order_id=${result.data.order_ID}`; // Redirect on success with order ID
        } else {
          alert('Checkout failed: ' + result.message); // Show error message
          console.error('Stock issues: ', result.data?.stock_issues); // Log stock issues if any > DEBUG
        }
      } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while processing your payment. Please try again.');
      }
    });
} else {
    console.error('Form not found!');
}
}); 