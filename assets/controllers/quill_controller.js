import { Controller } from '@hotwired/stimulus';
import Quill from 'quill';
export default class extends Controller {
    connect() {

        const textarea = this.element;
        const initialText = textarea.innerHTML ?? "";

        const editor = document.createElement('div')
        textarea.parentNode.insertBefore(editor, textarea.nextSibling)

        const quill = new Quill(editor, {
            modules: {
              toolbar: [
                [{ header: [1, 2, false] }],
                ['bold', 'italic', 'underline']
              ],
            },
            theme: 'snow',
        });

        const decodedText = new DOMParser().parseFromString(initialText, "text/html").documentElement.textContent;

        quill.clipboard.dangerouslyPasteHTML(decodedText);

        // on quill change
        quill.on('text-change', () => {
            textarea.innerHTML = quill.root.innerHTML;
        });

    }
}
