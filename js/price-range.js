const minSlider = document.getElementById("price-min");
const maxSlider = document.getElementById("price-max");
const label  = document.getElementById("price-num");
const rangeDisplay = document.getElementById("range-display");


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
}

minSlider.addEventListener("input", updatePrice);
maxSlider.addEventListener("input", updatePrice);

updatePrice();