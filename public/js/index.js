
const modal = document.getElementById("createTaskModal");
const newTaskButton = document.getElementById("newTaskButton");
console.log("hello");

// Function to close the modal
function closeModal() {
  modal.classList.add("hidden");
}

// Function to open the modal
function openModal() {
  modal.classList.remove("hidden");
}

// Add click event to new task button
newTaskButton.addEventListener("click", openModal);

// Close modal when clicking outside
modal.addEventListener("click", function (e) {
  if (e.target === modal) {
    closeModal();
  }
});

// Handle task type change
document
  .querySelector('select[name="taskType"]')
  .addEventListener("change", (e) => {
    const featureFields = document.getElementById("featureFields");
    const bugFields = document.getElementById("bugFields");

    if (e.target.value === "feature") {
      featureFields.classList.remove("hidden");
      bugFields.classList.add("hidden");
    } else {
      featureFields.classList.add("hidden");
      bugFields.classList.remove("hidden");
    }
  });

// Add escape key listener
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape" && !modal.classList.contains("hidden")) {
    closeModal();
  }
});
