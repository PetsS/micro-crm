
// Function for developing table row
function toggleDetails(row) {
  var nextRow = row.nextElementSibling;
  if (nextRow && nextRow.classList.contains("additional-row")) {
    nextRow.classList.toggle("expanded");
  }
}
