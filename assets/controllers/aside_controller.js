import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    connect() {
        const aside = document.querySelector('aside')
        const status = localStorage.getItem('asideStatus', 'expanded')
        const toggleNavBtn = document.querySelector('#toggle-nav-btn')

        aside.classList.add(status)
        toggleNavBtn.classList.add(status)

    }

    toggleAside() {
        const aside = document.querySelector('aside')
        aside.classList.toggle('show')
    }

    compactExpand() {
        
        const aside = document.querySelector('aside')
        const toggleNavBtn = document.querySelector('#toggle-nav-btn')
        let asideStatus = localStorage.getItem('asideStatus', null)

        if(aside.classList.contains('compact')) {
            aside.classList.remove('compact')
            aside.classList.add('expanded')
            toggleNavBtn.classList.remove('compact')
            toggleNavBtn.classList.add('expanded')
            asideStatus = 'expanded'
            
        } else {
            aside.classList.add('compact')
            aside.classList.remove('expanded')
            toggleNavBtn.classList.remove('expanded')
            toggleNavBtn.classList.add('compact')
            asideStatus = 'compact'
        }

        localStorage.setItem('asideStatus', asideStatus)

    }
}
