// 1. Get a reference to the form element
const myForm = document.getElementById('myForm');

// 2. Add an event listener for the 'submit' event
myForm.addEventListener('submit', function (event) {
    // 3. Prevent the default browser behavior (page refresh)

    // 4. Access form data easily
    const usernameInput = document.getElementById('username').value;

    // 5. Perform your custom actions (validation, AJAX request, etc.)
    if (usernameInput.trim() === "") {
        alert("Please enter a username.");
    } else {
        alert("Form submitted successfully! Username:", usernameInput);

        // Example: You can now send this data to an API using fetch()
    }
    const nameInput = document.getElementById('fname').value;

    // 5. Perform your custom actions (validation, AJAX request, etc.)
    if (nameInput.trim() === "") {
        alert("Please enter full name.");
    } else {
        alert("Form submitted successfully! Full Name:", nameInput);

        // Example: You can now send this data to an API using fetch()
    }
});
