document.addEventListener("DOMContentLoaded", () => {
    const items = document.querySelectorAll(".basket-item");

    function updateTotals() {
        let basketTotal = 0;
        items.forEach(item => {
            const quant = parseInt(item.querySelector(".quant-number").textContent);
            const unitPrice = parseFloat(item.dataset.price);
            const itemTotal = unitPrice * quant;
            basketTotal += itemTotal;
            item.querySelector(".price").textContent = `£${itemTotal.toFixed(2)}`;
        });
        document.querySelector(".summary-box p:nth-child(2)").innerHTML = `Basket: £${basketTotal.toFixed(2)}`;
        document.querySelector(".summary-box p:nth-child(4)").innerHTML = `<strong>Total: £${basketTotal.toFixed(2)}</strong>`;
    }

    items.forEach(item => {
        const minusBtn = item.querySelector(".minus");
        const plusBtn = item.querySelector(".plus");
        const quantNumber = item.querySelector(".quant-number");

        minusBtn.addEventListener("click", () => {
            let quant = parseInt(quantNumber.textContent);
            if (quant > 1) {
                quantNumber.textContent = quant - 1;
                updateTotals();


            }
        });

        plusBtn.addEventListener("click", () => {
            let quant = parseInt(quantNumber.textContent);
            quantNumber.textContent = quant + 1;
            updateTotals();
        });
    });

    updateTotals();
});
