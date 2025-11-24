const minSlider = document.getElementById("price-min");
const maxSlider = document.getElementById("price-max");
const label  = document.getElementById("price-num");
const rangeDisplay = document.getElementById("range-display");
const products =document.querySelectorAll(".item");

function updatePrice() {
    let min = Number(minSlider.value);
    let max = Number(maxSlider.value);

    if(min > max) {
        [min, max ] = [max, min]
    }
    label.textContent = `£${min} - £${max}`;

      const percentMin = (min / minSlider.max) * 100;
      const percentMax = (max / maxSlider.max) * 100;

  rangeDisplay.style.left = percentMin + "%";
  rangeDisplay.style.width = (percentMax - percentMin) + "%";

products.forEach(product =>{
  const price = Number(product.dataset.price);
  if(price>= min && price <=max) {
    product.style.display ="block";
  
  } else {
    product.style.display = "none";
  }
})}
minSlider.addEventListener("input", updatePrice);
maxSlider.addEventListener("input", updatePrice);

updatePrice();