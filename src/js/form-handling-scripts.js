/**
 * JavaScript functions to handle form operations
 */

var buttonDiv = document.getElementById("formButtons");
var addButton = document.getElementById("btn-add-persons");
var addButtonUpd = document.getElementById("btn-add-persons-update");

// Use DOMContentLoaded event to ensure DOM is fully loaded while executing the scripts
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
  // addDynamicDisplayInfoToOriginal();
  showVisitetypeOptions();
  showDisplayInfo();
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
  if (visitetypeSelect.value === "2") {
    // If the initial value is '2', remove the 'hidden' class from visitetypeInfo
    visitetypeInfo.classList.remove("hidden");
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

      if (selectedAge !== "1") {
        totalNbPersons += parseInt(nbPersonsInput.value) || 0;
      }
    });
    
    // Check if the total number of persons is greater than 14 and there have been changes
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

    if (totalNbPersons === 0 || isNaN(parseInt(nbPersonsInput))) {
      infoPersons.classList.add("hidden");
      infoPersons.style.animationName = "fadeOut";
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


// Add event listener to clear form data on page exit or refresh using JavaScript
window.addEventListener('beforeunload', function() {
    // Call an AJAX request to clear form data on page exit or refresh
    var xhr = new XMLHttpRequest();
    xhr.open("GET", ajax_object.ajaxurl + "?action=clear_form_data", false);
    xhr.send();
});


// Function to add dynamic display info based on selected category and number of persons
// function addDynamicDisplayInfo(container) {
//   var displayInfo = container.querySelector('[name="display-info"]');

//   var nbPersonsInput = container.querySelector('input[name="nbPersons[]"]');
//   var agesSelect = container.querySelector('select[name="ages[]"]');

//   nbPersonsInput.addEventListener("change", updateDisplayInfo);
//   agesSelect.addEventListener("change", updateDisplayInfo);

//   updateDisplayInfo();

//   function updateDisplayInfo() {
//     var nbPersons = parseInt(nbPersonsInput.value) || 0;
//     var selectedAge = agesSelect.options[agesSelect.selectedIndex].text;

//     var dynamicText = "";
//     if (selectedAge !== "Choisissez âge..." && nbPersons > 0) {
//       dynamicText =
//         "You have selected " +
//         nbPersons +
//         " person(s) with category: " +
//         selectedAge;
//     } else {
//       // dynamicText = "Please select a category and enter the number of persons.";
//       dynamicText = "sdfsdf " + "fsd";
//     }

//     if (displayInfo) {
//       displayInfo.textContent = dynamicText;
//     } else {
//       // If display info doesn't exist, create it and append it to the container
//       displayInfo = document.createElement("div");
//       displayInfo.setAttribute("name", "display-info");
//       displayInfo.textContent = dynamicText;
//       container.appendChild(displayInfo);
//     }
//   }
// }

// // function to load dynamic display info text to the original container on page load
// function addDynamicDisplayInfoToOriginal() {
//   container = document.getElementById("container-0");

//   if (!container.querySelector('[name="display-info"]')) {
//     addDynamicDisplayInfo(container);
//   }
// }

// function to fetch data from the REST API
// function fetchAgeData() {
//     fetch('/wp-json/custom/v1/age-data')
//         .then(response => response.json())
//         .then(data => {
//             // Work with the fetched age data
//             console.log(data);

//             // Access individual data items
//             data.forEach(ageItem => {
//                 console.log(ageItem.id); // Access the ID of each age item
//                 console.log(ageItem.category); // Access the category of each age item
//                 console.log(ageItem.price); // Access the price of each age item

//                 // Perform operations with individual data items
//                 // For example, update the DOM with the fetched data
//                 // Example:
//                 // var ageListElement = document.createElement('li');
//                 // ageListElement.textContent = `Category: ${ageItem.category}, Price: ${ageItem.price}`;
//                 // document.getElementById('age-list').appendChild(ageListElement);
//             });
//         })
//         .catch(error => {
//             console.error('Error fetching age data:', error);
//         });
// }
