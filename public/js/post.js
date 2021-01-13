/**
 * Intercepter le click sur le bouton "AJouter aux favoris" et sur le bouton "Supprimer des favoris"
 */
const buttons = document.querySelectorAll('.bookmark-button');

function onAjaxGetResponse(data)
{
    for (const button of buttons) {
        button.classList.toggle('d-none');
    }
}

// Code principal
for (const button of buttons) {

    button.addEventListener('click', event => {

        event.preventDefault();

        const url = event.currentTarget.href;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(onAjaxGetResponse);

    });
}

