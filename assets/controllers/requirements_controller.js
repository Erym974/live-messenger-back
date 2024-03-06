import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  connect() {
    const requirementsFields = this.element;

    const buttons = requirementsFields.querySelectorAll(
      ".requirement-fields-remove"
    );
    buttons.forEach((button) => activeDeleteButton(button));

    const wrapper = requirementsFields.querySelector(
      "#requirement-fields-list"
    );
    const prototype = wrapper.dataset.prototype;
    let index = parseInt(wrapper.dataset.index);

    requirementsFields
      .querySelector("button.requirement-fields-add")
      .addEventListener("click", () => {
        const li = document.createElement("li");
        li.classList.add("list-group-item", "mb-2");
        li.setAttribute("data-index", index);
        const row = document.createElement("div");
        row.classList.add("row");
        const prototypeContainer = document.createElement("div");
        prototypeContainer.classList.add("mb-0", "col-md-10");
        const deleteButton = document.createElement("button");
        deleteButton.setAttribute("type", "button");
        deleteButton.classList.add(
          "btn",
          "btn-danger",
          "requirement-fields-remove",
          "col-md-2"
        );
        deleteButton.textContent = "Remove";

        activeDeleteButton(deleteButton);

        row.appendChild(prototypeContainer);
        row.appendChild(deleteButton);
        li.appendChild(row);

        prototypeContainer.innerHTML = prototype.replace(/__name__/g, index);
        wrapper.setAttribute("data-index", index++);

        wrapper.appendChild(li);
      });

    function activeDeleteButton(button) {
      button.addEventListener("click", () => {
        const li = button.closest("li");
        li.remove();
      });
    }
  }
}
