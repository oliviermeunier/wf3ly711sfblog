import {ReferenceList} from './module/ReferenceList.js';

Dropzone.autoDiscover = false;

document.addEventListener('DOMContentLoaded', function() {
    const referenceList = new ReferenceList(document.querySelector('.js-reference-list'));
    initializeDropzone(referenceList);
});

function initializeDropzone(referenceList)
{
    const form = document.querySelector('.js-reference-dropzone');
    if (!form) {
        return;
    }

    const dropzone = new Dropzone(form, {
        paramName: 'reference',
        init: function () {
            this.on('error', function(file, data) {
                this.emit('error', file, data.detail);
            });
            this.on('success', function(file, data) {
                referenceList.addReference(data);
            });
        }
    });
}