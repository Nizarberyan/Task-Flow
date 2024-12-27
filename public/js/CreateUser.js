$(document).ready(function () {
  $("#addUserBtn").click(function () {
    $("#addUserModal").removeClass("hidden");
  });

  $("#cancelAddUserBtn").click(function () {
    $("#addUserModal").addClass("hidden");
  });

  $("#addUserForm").submit(function (e) {
    e.preventDefault();

    var formData = $(this).serialize();

    $.ajax({
      type: "POST",
      url: "../Controllers/add-user.php",
      data: formData,
      success: function (response) {
        if (response.success) {
          location.reload();
        } else {
          alert("Failed to add user. Please try again.");
        }
      },
      error: function () {
        alert("An error occurred. Please try again.");
      },
    });
  });
});
