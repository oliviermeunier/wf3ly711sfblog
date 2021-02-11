export class ReferenceList {
    constructor(element) {
        this.element = element;
        this.element.addEventListener('click', this.onClickReferenceList.bind(this));
    }

    onClickReferenceList(event)
    {
        if (event.target.classList.contains('js-reference-delete')) {
            const id = Number(event.target.dataset.id);
            const item = document.getElementById('reference-' + id);
            item.classList.add('disabled');

            fetch('/admin/article/references/' + id, {
                method: 'DELETE'
            }).then(() => {
                item.remove();
            });
        }
    }

    addReference(reference) {
        this.renderNewReference(reference);
    }

    renderNewReference(reference) {
        const html = `
<li class="list-group-item d-flex justify-content-between align-items-center" id="reference-${reference.id}">
    ${reference.originalFilename}
    <span>
        <a href="/admin/article/references/${reference.id}/download"><span class="fa fa-download"></span></a>
        <button class="js-reference-delete btn btn-link fa fa-trash" data-id="${reference.id}"></button>
    </span>
</li>
`;

        this.element.innerHTML += html;
    }

}

// export class ReferenceList {
//     constructor(element) {
//         this.element = element;
//         this.references = [];
//         this.render();
//
//         this.element.addEventListener('click', this.onClickReferenceList.bind(this));
//
//         this.init();
//     }
//
//     onClickReferenceList(event)
//     {
//         if (event.target.classList.contains('js-reference-delete')) {
//             const id = Number(event.target.dataset.id);
//             const item = document.getElementById('reference-' + id);
//             item.classList.add('disabled');
//
//             fetch('/admin/article/references/' + id, {
//                 method: 'DELETE'
//             }).then(() => {
//                 this.references = this.references.filter(reference => {
//                     return reference.id !== id;
//                 });
//                 this.render();
//             });
//         }
//     }
//
//     async init() {
//         this.references = await this.getReferences();
//         this.render();
//     }
//
//     async getReferences() {
//         const response = await fetch(this.element.dataset.url);
//         return await response.json();
//     }
//
//     addReference(reference) {
//         this.references.push(reference);
//         this.render();
//     }
//
//     render() {
//         const itemsHtml = this.references.map(reference => {
//             return `
// <li class="list-group-item d-flex justify-content-between align-items-center" >
//     ${reference.originalFilename}
//     <span>
//         <a href="/admin/article/references/${reference.id}/download"><span class="fa fa-download"></span></a>
//         <button class="js-reference-delete btn btn-link fa fa-trash" data-id="${reference.id}"></button>
//     </span>
// </li>
// `;
//         });
//
//         this.element.innerHTML = itemsHtml.join('');
//     }
//
// }