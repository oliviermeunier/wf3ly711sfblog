/**
 * Intercepter le click sur le bouton "AJouter aux favoris" et sur le bouton "Supprimer des favoris"
 */
const buttons = document.querySelectorAll('.bookmark-button');

for (const button of buttons) {

    button.addEventListener('click', async event => {

        event.preventDefault();

        const url = event.currentTarget.href;
        const options = {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        const httpResponse = await fetch(url, options);
        const reponse = await httpResponse.json();

        for (const button of buttons) {
            button.classList.toggle('d-none');
        }
    });
}

