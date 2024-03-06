import { Controller } from "@hotwired/stimulus";

const $ = require('jquery');
require("datatables.net");
require("datatables.net-dt");

export default class extends Controller {
  connect() {
    $('#table').DataTable({
        responsive: true,
        bLengthChange: false,
        select: {
            info: true,
            style: "single",
            selector: "td:nth-child(2)",
        },
    });
  }
}
