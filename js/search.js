const searchInput = document.querySelector(".search");
const allProducts = document.querySelectorAll(".item");
const noResults = document.getElementById("no-results");

searchInput.addEventListener("input", () => {
  const query = searchInput.value.toLowerCase().trim();
  let anyVisible = false;

  allProducts.forEach(product => {
    const name = product.querySelector("h2").textContent.toLowerCase();
    const keywords = (product.dataset.keywords || "").toLowerCase();

    if (name.includes(query) || keywords.includes(query)) {
      product.style.display = "block";
      anyVisible = true;
    } else {
      product.style.display = "none";
    }
  });

  noResults.style.display = anyVisible ? "none" : "block";
});