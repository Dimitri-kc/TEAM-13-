document.addEventListener("DOMContentLoaded", function() {
    console.log("Search.js loaded!");
    
    const searchInput = document.querySelector(".search");
    const noResults = document.getElementById("no-results");
    
    console.log("Search elements:", { searchInput, noResults });

    if (!searchInput) {
        console.error("Search input not found!");
        return;
    }

    searchInput.addEventListener("input", () => {
      console.log("Search triggered, query:", searchInput.value);
      const allProducts = document.querySelectorAll(".item");
      
      const query = searchInput.value.toLowerCase().trim();
      let anyVisible = false;

      allProducts.forEach(product => {
        const name = product.querySelector("h2").textContent.toLowerCase();
        const keywords = (product.dataset.keywords || "").toLowerCase();

        if (name.includes(query) || keywords.includes(query)) {
          product.style.display = "";
          anyVisible = true;
        } else {
          product.style.display = "none";
        }
      });

      noResults.style.display = anyVisible ? "none" : "block";
      console.log("Search complete, visible products:", anyVisible);
    });
    
    console.log("Search event listener attached!");
});
