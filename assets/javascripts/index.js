const $ = require('jquery');
require("datatables.net");
require("datatables.net-dt");

window.addEventListener('DOMContentLoaded', () => {

    loadThemeToggler()
    loadAsideToggler()
    loadDataTable()
    loadRequirementsCollectionType()
    loadCreateJobModal()

})

const loadThemeToggler = () => {

    if (localStorage.getItem('theme') == "dark") document.body.classList.toggle('dark-theme')

    document.querySelectorAll('.theme-toggler').forEach((themeToggler) => {
        themeToggler.addEventListener('click', () => {
            let theme = localStorage.getItem('theme') ?? "light"
            document.body.classList.toggle('dark-theme')
            theme == "dark" ? localStorage.setItem('theme', 'light') : localStorage.setItem('theme', 'dark')
        })
    })

}

const loadAsideToggler = () => {

    $("#menu-btn").on('click', function() {$('aside').toggleClass('show')});

    $("#close-btn").on('click', function() {$('aside').toggleClass('show')});

    $("#toggle-nav-btn").on('click', function() {

      if ( $('aside').hasClass("compact") ) {
          $('aside').removeClass("compact");
          $('aside').addClass("expanded");
          $("#toggle-nav-btn").removeClass("compact");
          $("#toggle-nav-btn").addClass("expanded");

          document.cookie = "asideClass=expanded; path=/;";
      } else {
          $('aside').addClass("compact");
          $('aside').removeClass("expanded");
          $("#toggle-nav-btn").removeClass("expanded");
          $("#toggle-nav-btn").addClass("compact");

          document.cookie = "asideClass=compact; path=/;";
      }
    });

}

const loadDataTable = () => {

    $('#table').DataTable({
        responsive: true,
        select: {
            info: true,
            style: 'single',
            selector: 'td:nth-child(2)'
        },
        // language: {
        //     "decimal":        Utils.Translate('datatable.decimal'), // "",
        //     "emptyTable":     Utils.Translate('datatable.emptyTable'), // "No data available in table",
        //     "info":           Utils.Translate('datatable.info'), // "Showing _START_ to _END_ of _TOTAL_ entries",
        //     "infoEmpty":      Utils.Translate('datatable.infoEmpty'), // "Showing 0 to 0 of 0 entries",
        //     "infoFiltered":   Utils.Translate('datatable.infoFiltered'), // "(filtered from _MAX_ total entries)",
        //     "infoPostFix":    Utils.Translate('datatable.infoPostFix'), // "",
        //     "thousands":      Utils.Translate('datatable.thousands'), // ",",
        //     "lengthMenu":     Utils.Translate('datatable.lengthMenu'), // "Show _MENU_ entries",
        //     "loadingRecords": Utils.Translate('datatable.loadingRecords'), // "Loading...",
        //     "processing":     Utils.Translate('datatable.processing'), // "",
        //     "search":         Utils.Translate('datatable.search'), // "Search:",
        //     "zeroRecords":    Utils.Translate('datatable.zeroRecords'), // "No matching records found",
        //     "paginate": {
        //         "first":      Utils.Translate('datatable.paginate.first'), // "First",
        //         "last":       Utils.Translate('datatable.paginate.last'), // "Last",
        //         "next":       Utils.Translate('datatable.paginate.next'), // "Next",
        //         "previous":   Utils.Translate('datatable.paginate.previous'), // "Previous"
        //     },
        //     "aria": {
        //         "sortAscending":  Utils.Translate('datatable.aria.sortAscending'),
        //         "sortDescending": Utils.Translate('datatable.aria.sortDescending')
        //     }
        // }
    })

}

const loadRequirementsCollectionType = () => {

    const requirementsFields = document.querySelector('[data-requirement-form]');
    if(!requirementsFields) return;

    
    const buttons = requirementsFields.querySelectorAll('.requirement-fields-remove');
    buttons.forEach(button => activeDeleteButton(button));
    
    const wrapper = requirementsFields.querySelector('#requirement-fields-list');
    const prototype = wrapper.dataset.prototype;
    let index = parseInt(wrapper.dataset.index);

    requirementsFields.querySelector('button.requirement-fields-add').addEventListener('click', () => {
        const li = document.createElement('li');
            li.classList.add('list-group-item','mb-2');
            li.setAttribute('data-index', index);
        const row = document.createElement('div');
            row.classList.add('row');
        const prototypeContainer = document.createElement('div');
            prototypeContainer.classList.add('mb-0','col-md-10');
        const deleteButton = document.createElement('button');
            deleteButton.setAttribute('type', 'button');
            deleteButton.classList.add('btn', 'btn-danger','requirement-fields-remove','col-md-2')
            deleteButton.textContent = 'Remove'

        activeDeleteButton(deleteButton);

        row.appendChild(prototypeContainer);
        row.appendChild(deleteButton);
        li.appendChild(row);

        prototypeContainer.innerHTML = prototype.replace(/__name__/g, index);
        wrapper.setAttribute('data-index', index++);

        wrapper.appendChild(li);

    })

    function activeDeleteButton(button) {
        button.addEventListener('click', () => {
            const li = button.closest('li');
            li.remove();
        })
    }

}

const loadCreateJobModal = () => {

    const modal = document.querySelector('#createJob');
    if(!modal) return

    modal.addEventListener('hide.bs.modal', function (event) {
        const params = new URLSearchParams(window.location.search);
        const status = params.get('id');
        if(status) {
            params.delete('id');
            window.location.search = params.toString();
        }
    })

}