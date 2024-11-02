<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Project</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

    <!-- Form to add new user -->
    <form id="userForm" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" class="form-control" placeholder="Name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" placeholder="Phone" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control" placeholder="Description"></textarea>
        </div>
        <div class="mb-3">
            <label for="role_id" class="form-label">Role</label>
            <select name="role_id" class="form-select" required>
                <!-- Roles will be populated here by JavaScript -->
            </select>
        </div>
        <div class="mb-3">
            <label for="profile_image" class="form-label">Profile Image</label>
            <input type="file" name="profile_image" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <!-- Display error messages -->
    <div id="errorMessages" class="text-danger mb-3"></div>

    <!-- Table to display users -->
    <table id="userTable" class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Description</th>
                <th>Role</th>
                <th>Profile Image</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // CSRF token setup for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Submit form and add user
        document.getElementById('userForm').addEventListener('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);
            formData.append('_token', csrfToken); // Add CSRF token to FormData

            fetch('/api/users', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken, // Set CSRF token in headers for fetch request
                },
                body: formData,
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.errors) {
                    // Display errors
                    document.getElementById('errorMessages').innerHTML = Object.values(data.errors)
                        .map(err => `<p>${err[0]}</p>`)
                        .join('');
                } else {
                    // Add user to the table and reset form
                    addUserToTable(data.user);
                    document.getElementById('userForm').reset();
                    document.getElementById('errorMessages').innerHTML = ''; // Clear error messages
                }
            })
            .catch(error => console.error('Error:', error));
        });

        // Add a user row to the table
        function addUserToTable(user) {
            const tbody = document.getElementById('userTable').getElementsByTagName('tbody')[0];
            const row = tbody.insertRow();
            row.innerHTML = `
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td>${user.phone}</td>
                <td>${user.description}</td>
                <td>${user.role.name}</td>
                <td><img src="/storage/app/public/profile_images/${user.profile_image}" width="50" alt="Profile Image"></td>
            `;
        }

        // Fetch roles and populate the role select dropdown
        fetch('/api/roles')
            .then(response => response.json())
            .then(roles => {
                const roleSelect = document.querySelector('select[name="role_id"]');
                roles.forEach(role => {
                    const option = document.createElement('option');
                    option.value = role.id;
                    option.textContent = role.name;
                    roleSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching roles:', error));

        // Load existing users on page load
        function loadUsers() {
            fetch('/api/users')
                .then(response => response.json())
                .then(users => {
                    users.forEach(user => addUserToTable(user));
                })
                .catch(error => console.error('Error loading users:', error));
        }

        // Load users initially
        loadUsers();
    </script>
</body>
</html>
