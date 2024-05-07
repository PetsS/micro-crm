/**
 * JavaScript functions to handle form operations
 */

var buttonDiv = document.getElementById("formButtons");
var addButton = document.getElementById("btn-add-persons");
var addButtonUpd = document.getElementById("btn-add-persons-update");
var pageElement = document.getElementById("scroll_here");

// Using window.onload event to ensure the entire page is loaded
window.onload = function () {
  if (pageElement) {
    // Call scrollToTop function after the page is fully loaded
    scrollToTop(pageElement);
  }
  scrollToTop(pageElement); // call again to ensure it is loaded
};

// Using DOMContentLoaded event to ensure DOM is fully loaded while executing the scripts
document.addEventListener("DOMContentLoaded", function () {
  // Call the functions when the page is loaded
  if (buttonDiv) {
    showFormsType(); // call only if div exists in the template
  }
  handleFormdata();
  if (addButton) {
    clonePersons(addButton); // call the function if the button with the id exists
  } else if (addButtonUpd) {
    clonePersons(addButtonUpd); // call the function if the button with the id exists in the update form
  }
  restoreElements();
  showVisitetypeOptions();
  showDisplayInfo();

  // Call scrollToTop function again in DOMContentLoaded event
  if (pageElement) {
    // Call scrollToTop function after the DOM is fully loaded
    scrollToTop(pageElement);
  }
});

/**
 * List of form related functions below
 */

// function for pressing on the 2 buttons to reveal and hid forms
function showFormsType() {
  var formQuestion = document.getElementById("formQuestion");
  var formQuotation = document.getElementById("formQuotation");

  document
    .getElementById("buttonQuestion")
    .addEventListener("click", function () {
      clearErrorMessages();

      formQuestion.classList.remove("hidden");
      formQuestion.style.animationName = "fadeIn";

      formQuotation.classList.add("hidden");
      formQuotation.style.animationName = "fadeOut";
    });

  document
    .getElementById("buttonQuotation")
    .addEventListener("click", function () {
      clearErrorMessages();

      formQuotation.classList.remove("hidden");
      formQuotation.style.animationName = "fadeIn";

      formQuestion.classList.add("hidden");
      formQuestion.style.animationName = "fadeOut";
    });
}

// Function to clear error messages from UI
function clearErrorMessages() {
  // Get error message elements and remove them from the DOM
  var errorElements = document.querySelectorAll(".error");
  errorElements.forEach(function (element) {
    element.parentNode.removeChild(element);
  });
}

// Remove hidden class from elements with error messages when the window loads
function handleFormdata() {
  var errorElements = document.querySelectorAll(".error");

  errorElements.forEach(function (element) {
    var closestHidden = element.closest(".hidden");
    if (closestHidden !== null) {
      closestHidden.classList.remove("hidden");
    }
  });
}

function scrollToTop(element) {
  element.scrollIntoView({ behavior: "smooth", block: "start" }); // Scroll to the tag if found with delay
}

// Function to clone container with persons and ages
function clonePersons(buttonElement) {
  buttonElement.addEventListener("click", () => {
    // clone the container div
    var container = document.querySelector(".containerClone");
    var clonedContainer = container.cloneNode(true);

    // Increment container ID by 1
    var lastContainerId =
      document.querySelectorAll(".containerClone").length - 1;
    var newContainerId = "container-" + (parseInt(lastContainerId) + 1);

    // Check if the new container ID already exists, if yes, increment by 1 until it's unique
    while (document.getElementById(newContainerId)) {
      lastContainerId++;
      newContainerId = "container-" + lastContainerId;
    }

    clonedContainer.id = newContainerId;

    // Increment 'for' attributes for labels and IDs for inputs and selects by 1
    var inputs = clonedContainer.querySelectorAll("label, input, select");
    inputs.forEach((input) => {
      var inputId = input.getAttribute("id");
      if (inputId) {
        var newInputId =
          inputId.replace(/-\d+$/, "") + "-" + (parseInt(lastContainerId) + 1);
        // Check if the new input ID already exists, if yes, increment by 1 until it's unique
        while (document.getElementById(newInputId)) {
          lastContainerId++;
          newInputId = inputId.replace(/-\d+$/, "") + "-" + lastContainerId;
        }
        input.setAttribute("id", newInputId);
      }

      if (input.tagName === "LABEL") {
        var forAttribute = input.getAttribute("for");
        if (forAttribute) {
          var newForAttribute =
            forAttribute.replace(/-\d+$/, "") +
            "-" +
            (parseInt(lastContainerId) + 1);
          // Check if the new 'for' attribute already exists, if yes, increment by 1 until it's unique
          while (document.getElementById(newForAttribute)) {
            lastContainerId++;
            newForAttribute =
              forAttribute.replace(/-\d+$/, "") + "-" + lastContainerId;
          }
          input.setAttribute("for", newForAttribute);
        }
      }
    });

    // reset input values
    var inputs = clonedContainer.querySelectorAll("input, select");
    inputs.forEach((input) => {
      if (input.tagName === "INPUT") {
        input.value = "";
      } else if (input.tagName === "SELECT") {
        input.value = "default";
      }
    });

    // Find the last cloned container
    var lastClonedContainer =
      document.querySelectorAll(".containerClone")[
        document.querySelectorAll(".containerClone").length - 1
      ];

    // Append the cloned container div after the last cloned container
    lastClonedContainer.parentNode.insertBefore(
      clonedContainer,
      lastClonedContainer.nextSibling
    );

    // Add dynamic display info for the cloned container
    // addDynamicDisplayInfo(clonedContainer);

    // // add a delete button the each cloned container
    addDeleteButton(clonedContainer);
  });
}

// Function to add a delete button icon to the cloned container
function addDeleteButton(clonedContainer) {
  var deleteButton = document.createElement("button");
  deleteButton.innerHTML = '<i class="fas fa-minus"></i>';
  deleteButton.classList.add("btn-delete");
  deleteButton.addEventListener("click", () => {
    // Remove the cloned container when delete button is clicked
    clonedContainer.parentNode.removeChild(clonedContainer);
  });

  // Append the buttonas a child of the cloned container
  clonedContainer.appendChild(deleteButton);
}

// Function to restore delete buttons for cloned containers and reassign field attributes
function restoreElements() {
  var containers = document.querySelectorAll(".containerClone");

  containers.forEach((container, index) => {
    if (index !== 0) {
      // Skip the original container
      // Increment container ID by 1
      var newContainerId = "container-" + index;
      container.id = newContainerId;

      // Update 'for' attributes for labels and IDs for inputs and selects
      var inputs = container.querySelectorAll("label, input, select");
      inputs.forEach((input) => {
        var inputId = input.getAttribute("id");
        if (inputId) {
          var newInputId = inputId.replace(/\d+$/, "") + "-" + index;
          input.setAttribute("id", newInputId);
        }

        if (input.tagName === "LABEL") {
          var forAttribute = input.getAttribute("for");
          if (forAttribute) {
            var newForAttribute =
              forAttribute.replace(/\d+$/, "") + "-" + index;
            input.setAttribute("for", newForAttribute);
          }
        }
      });

      // Restore display info
      // addDynamicDisplayInfo(container);

      // Restore delete button
      addDeleteButton(container);
    }
  });
}

// Function to show info on selected visit type
function showVisitetypeOptions() {
  var visitetypeSelect = document.getElementById("visitetype");
  var visitetypeInfo = document.getElementById("info-visiteType");

  // Check the initial value of visitetypeSelect on page load
  if (visitetypeSelect) {
    if (visitetypeSelect.value === "2") {
      // If the initial value is '2', remove the 'hidden' class from visitetypeInfo
      visitetypeInfo.classList.remove("hidden");
    }
  }

  // Attach a change event listener to the select, when the option is selected do the action
  if (visitetypeSelect) {
    visitetypeSelect.addEventListener("change", function () {
      if (visitetypeSelect.value === "2") {
        visitetypeInfo.classList.remove("hidden");
        visitetypeInfo.style.animationName = "fadeIn";
      } else {
        visitetypeInfo.classList.add("hidden");
        visitetypeInfo.style.animationName = "fadeOut";
      }
    });
  }
}

// Function to show info based on number of persons
function showDisplayInfo() {
  var infoPersons = document.getElementById("info-persons");
  var infoPersonsDiscount = document.getElementById("info-persons-discount");

  // Function to calculate total number of persons and update display info
  var updateDisplayInfo = function () {
    var totalNbPersons = 0;
    var containers = document.querySelectorAll(".containerClone");

    // Iterate over each container to sum up nbPersons inputs
    containers.forEach((container) => {
      var nbPersonsInput = container.querySelector('input[name="nbPersons[]"]');
      var agesSelect = container.querySelector('select[name="ages[]"]');
      var selectedAge = agesSelect.value;

      if (selectedAge !== "1" && selectedAge < "5") {
        totalNbPersons += parseInt(nbPersonsInput.value) || 0; // if the selected category is true, it sets the total of nbPersons to 0
      }
    });

    // Check if the total number of persons is greater than 14 and there have been changes
    if (infoPersons || infoPersonsDiscount) {
      if (totalNbPersons > 14) {
        infoPersonsDiscount.classList.remove("hidden");
        infoPersonsDiscount.style.animationName = "fadeIn";

        infoPersons.classList.add("hidden");
        infoPersons.style.animationName = "fadeOut";
      } else {
        infoPersonsDiscount.classList.add("hidden");
        infoPersonsDiscount.style.animationName = "fadeOut";

        infoPersons.classList.remove("hidden");
        infoPersons.style.animationName = "fadeIn";
      }

      if (totalNbPersons === 0) {
        infoPersons.classList.add("hidden");
        infoPersons.style.animationName = "fadeOut";
      }
    }
  };

  // Call updateDisplayInfo initially
  updateDisplayInfo();

  // Add event listener to dynamically added containerClone divs
  document.addEventListener("input", function (event) {
    var target = event.target;
    if (
      target &&
      target.matches(
        '.containerClone input[name="nbPersons[]"], .containerClone select[name="ages[]"]'
      )
    ) {
      updateDisplayInfo();
    }

    // Check if the input is nbPersons and reveal infoPersons if value is entered
    if (target && target.matches('.containerClone input[name="nbPersons[]"]')) {
      updateDisplayInfo();
    }
  });
}
