document.addEventListener('DOMContentLoaded', () => {
    const favouriteForms = document.querySelectorAll('.js-favourite-form');

    if (!favouriteForms.length) {
        return;
    }

    const toast = createToast();

    favouriteForms.forEach((form) => {
        const button = form.querySelector('.js-favourite-button');

        if (!button) {
            return;
        }

        updateButtonState(button, button.dataset.favouriteState === 'true');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (button.disabled) {
                return;
            }

            button.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new FormData(form)
                });

                const payload = await response.json();

                if (!response.ok || !payload.success) {
                    if (payload.redirect) {
                        window.location.href = payload.redirect;
                        return;
                    }

                    throw new Error(payload.message || 'Could not update favourites.');
                }

                updateButtonState(button, payload.isFavourite);
                showToast(toast, payload.message || (payload.isFavourite ? 'Added to favourites' : 'Removed from favourites'));
            } catch (error) {
                form.submit();
                return;
            } finally {
                button.disabled = false;
            }
        });
    });
});

function updateButtonState(button, isFavourite) {
    button.dataset.favouriteState = isFavourite ? 'true' : 'false';
    button.classList.toggle('is-active', isFavourite);
    button.setAttribute('aria-pressed', isFavourite ? 'true' : 'false');
    button.setAttribute('title', isFavourite ? 'Remove from favourites' : 'Add to favourites');
    button.textContent = isFavourite ? '♥' : '♡';
}

function createToast() {
    let toast = document.getElementById('favourites-toast');

    if (toast) {
        return toast;
    }

    toast = document.createElement('div');
    toast.id = 'favourites-toast';
    toast.className = 'favourites-toast';
    document.body.appendChild(toast);

    return toast;
}

function showToast(toast, message) {
    toast.textContent = message;
    toast.classList.add('is-visible');

    window.clearTimeout(showToast.timeoutId);
    showToast.timeoutId = window.setTimeout(() => {
        toast.classList.remove('is-visible');
    }, 1800);
}
