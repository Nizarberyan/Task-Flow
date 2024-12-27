document.addEventListener("DOMContentLoaded", function () {
  var form = document.getElementById("createTaskForm");

  form.addEventListener("submit", function (event) {
    

    var formData = new FormData(form);

    fetch("/create-task", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          console.log("Task created successfully"); 
          alert("Task created successfully!");
          form.reset(); 
          console.log("Form reset executed"); 
          
        } else {
          console.log("Error occurred:", data.error);
          alert("An error occurred: " + data.error);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("An error occurred. Please try again.");
      });
  });
});
