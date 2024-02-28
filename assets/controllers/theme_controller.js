import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    connect() {
        console.log('connected');
    }
    dark() {
        console.log('dark');
    }
    light() {
        console.log('light');
    }
}
