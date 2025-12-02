function scrollReviews(direction) {
    const container = document.getElementById('reviewsContainer');
    const scrollAmount = 320; // Approximately the width of one card + gap

    if (direction === 'left') {
        container.scrollBy({
            left: -scrollAmount,
            behavior: 'smooth'
        });
    } else {
        container.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
        });
    }
}