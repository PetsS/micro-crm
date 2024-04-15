/**
 * JavaScript functions to handle form operations
 */

var buttonDiv = document.getElementById("formButtons");
var addButton = document.getElementById("btn-add-persons");
var addButtonUpd = document.getElementById("btn-add-persons-update");

// Use DOMContentLoaded event to ensure DOM is fully loaded while executing the scripts
document.addEventListener("DOMContentLoaded", function () {
  // Call the functions
  if (buttonDiv) {
    showFormsType(); // call only if div exists in the template
  }
  handleFormdata();
  if (addButton) {
    clonePersons(addButton); // call the function if the button with the id exists
  } else if (addButtonUpd) {
    clonePersons(addButtonUpd); // call the function if the button with the id exists in the update form
  }
  restoreDeleteButtons();
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
  // Clear URL parameters with history.replaceState() method to replace the current URL with a new one that doesn't contain the query parameters.
  // history.replaceState({}, document.title, window.location.pathname);
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

  // Call the function to scroll to the top of the form if there are error data
  if (document.querySelectorAll(".error").length > 0) {
    // delay the execution of scrollToFormTop to execute by a few milliseconds the scroll function after the redirection of the page after onloading the window
    setTimeout(scrollToFormTop, 200);
  }
}

function scrollToFormTop() {
  // const formElement = document.querySelector("form"); // Select the first <form> element
  const formElement = document.getElementById("scrollHereIfErrors");
  const formElementUpdate = document.getElementById(
    "scrollHereIfErrorsInUpdate"
  );

  if (formElement) {
    formElement.scrollIntoView({ behavior: "smooth", block: "start" }); // Scroll to the form if found
  } else if (formElementUpdate) {
    formElementUpdate.scrollIntoView({ behavior: "smooth", block: "start" });
  } else {
    console.log("Form not found"); // Log an error if form not found
  }
}

// Function to clone container with persons and ages
function clonePersons(buttonElement) {
  buttonElement.addEventListener("click", () => {
    // clone the container div
    var container = document.querySelector(".containerClone");
    var clonedContainer = container.cloneNode(true);

    // Increment container ID by 1
    var lastContainerId = document.querySelectorAll(".containerClone").length - 1;
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
    // Remove the stored information about delete button for this container from local storage
    // deleteFromLocalStorage(clonedContainer);
  });
  clonedContainer.appendChild(deleteButton);
}

// Function to store the presence of delete button for a container in local storage
// function saveToLocalStorage(container) {
//   var containerData = {
//     id: container.id, // the id of the cloned containers
//     inputs: [], // inputs array to load it with the attribute values
//   };

//   // Save container's ID
//   // localStorage.setItem(container.id, "true");

//   // Save input IDs, values, and their corresponding 'for' attribute values
//   container.querySelectorAll("label, input, select").forEach((element) => {
//     if (element.tagName === "LABEL") {
//       var forAttribute = element.getAttribute("for");
//       containerData.inputs.push({ for: forAttribute });
//     } else if (element.tagName === "INPUT" || element.tagName === "SELECT") {
//       var inputId = element.getAttribute("id");
//       containerData.inputs.push({ id: inputId });
//     }
//   });

//   // Serialize and save container data
//   localStorage.setItem(container.id + "_data", JSON.stringify(containerData));
// }

// Function to remove the stored information about delete button for a container from local storage
// function deleteFromLocalStorage(container) {
//   localStorage.removeItem(container.id + "_data");
// }

// Function to restore delete buttons for cloned containers from local storage
// function restoreDeleteButtons() {
//   document.querySelectorAll(".containerClone").forEach((container) => {
//     var containerData = JSON.parse(localStorage.getItem(container.id + "_data"));

//     if (containerData) {
//       if (containerData.id === container.id) {
//         containerData.inputs.forEach((inputData) => {
//           if (inputData.hasOwnProperty("id")) {
//             var inputId = inputData.id;
//             var inputElement = container.querySelector(`#${inputId}`);
//             if (inputElement) {
//               inputElement.setAttribute("id", container.id + "-" + inputId.split("-")[1]);
//             }
//           }
//           if (inputData.hasOwnProperty("for")) {
//             var forAttribute = inputData.for;
//             var labelElement = container.querySelector(`label[for="${forAttribute}"]`);
//             if (labelElement) {
//               labelElement.setAttribute("for", container.id + "-" + forAttribute.split("-")[1]);
//             }
//           }
//         });
//       }

//       // Restore delete button
//       if (container.id !== "container-0") {
//         addDeleteButton(container);
//       }
//     }
//   });
// }

// Function to restore delete buttons for cloned containers and reassign field attributes
function restoreDeleteButtons() {
  var containers = document.querySelectorAll(".containerClone");

  containers.forEach((container, index) => {
    if (index !== 0) { // Skip the original container
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
            var newForAttribute = forAttribute.replace(/\d+$/, "") + "-" + index;
            input.setAttribute("for", newForAttribute);
          }
        }
      });

      // Restore delete button
      addDeleteButton(container);
    }
  });
}




