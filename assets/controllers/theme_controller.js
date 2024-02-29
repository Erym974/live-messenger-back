import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    connect() {
        const toggler = this.element;
        if (localStorage.getItem('theme') == "dark") document.body.classList.toggle('dark-theme')
        toggler.addEventListener('click', () => {
            let theme = localStorage.getItem('theme') ?? "light"
            document.body.classList.toggle('dark-theme')
            theme == "dark" ? localStorage.setItem('theme', 'light') : localStorage.setItem('theme', 'dark')
        })
    }
}
