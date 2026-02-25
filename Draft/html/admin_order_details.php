<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Order Details | Admin</title>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 40px;
    color: #1a1a1a;
  }

  .container {
    display: flex;
    gap: 40px;
    max-width: 900px;
    margin: 0 auto;
    align-items: flex-start;
  }

  .left-section {
    flex: 1;
  }

  .right-section {
    flex: 1;
  }

  /* Order info */
  .order-info h2 {
    margin-bottom: 4px;
    font-weight: 700;
  }

  .order-info p {
    margin-top: 0;
    margin-bottom: 20px;
    font-weight: 500;
  }

  /* Image placeholder */
  .order-image {
    width: 300px;
    height: 300px;
    background-color: #d3d3d3;
    border-radius: 8px;
  }

  /* Shipping address */
  .shipping-address {
    margin-top: 100px;
    font-weight: 600;
    margin-bottom: 12px;
  }

  .shipping-details b {
    display: block;
    margin-bottom: 6px;
  }

  /* Controls */
  label {
    font-weight: 500;
    margin-bottom: 6px;
    display: inline-block;
  }

  select {
    width: 100%;
    padding: 8px 12px;
    margin-bottom: 20px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
  }

  button {
    width: 100%;
    background-color: #2C2C2C;
    color: white;
    border: none;
    padding: 12px 0;
    margin-bottom: 12px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s;
    font-size: 14px;
  }

  button:hover {
    background-color: #1a1a1a;
  }
</style>
</head>
<body>

<div class="container">
  <div class="left-section">
    <div class="order-info">
      <h2>Order #UK12345</h2>
      <p>Customer: FirstName LastName</p>
    </div>
    <div class="order-image" aria-label="Order product image placeholder"></div>
  </div>

  <div class="right-section">
    <div class="shipping-address">Shipping Address:</div>
    <div class="shipping-details">
      <b>FirstName LastName</b>
      <b>Address Line 1</b>
      <b>Address Line 2</b>
      <b>Postcode</b>
      <b>County</b>
      <b>Country</b>
    </div>

    <!-- <label for="cancel-lines">Cancel Select Lines</label>
    <select id="cancel-lines" name="cancel-lines">
      <option value="" disabled selected>Value</option>
         Populate options dynamically here -->
      <!-- <option value="line1">Line 1</option>
      <option value="line2">Line 2</option>
      <option value="line3">Line 3</option>
    </select> --> 

    <button type="button">Edit Details</button>
    <button type="button">Confirm Shipment Processing</button>
    <button type="button">Cancel Order</button>
  </div>
</div>

</body>
</html>