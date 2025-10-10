<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Registrations</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style> body { font-family: 'Jost', sans-serif; } </style>
</head>
<body class="bg-gray-100 text-gray-800">
    <header class="bg-white shadow-md">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-800">SPSF Symposium - Admin Panel</h1>
            <a href="logout.php" class="bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition duration-300">Logout</a>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold">Registrations</h2>
            <a href="export_to_excel.php" class="bg-green-600 text-white py-2 px-5 rounded-lg hover:bg-green-700 transition duration-300 inline-flex items-center">
                <i data-lucide="sheet" class="w-4 h-4 mr-2"></i>Export as Excel (CSV)
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-4 font-semibold">Reg. ID</th>
                        <th class="py-3 px-4 font-semibold">Name</th>
                        <th class="py-3 px-4 font-semibold">Email</th>
                        <th class="py-3 px-4 font-semibold">Affiliation</th>
                        <th class="py-3 px-4 font-semibold">Category</th>
                        <th class="py-3 px-4 font-semibold">Special Requirements</th>
                        <th class="py-3 px-4 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody id="registrations-table-body"></tbody>
            </table>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tableBody = document.getElementById('registrations-table-body');
            lucide.createIcons();

            // FIXED: Renamed the function to be reusable
            const loadRegistrations = async () => {
                const response = await fetch('fetch_registrations.php');
                const registrations = await response.json();
                
                tableBody.innerHTML = ''; // Clear existing table data
                
                if (registrations.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4">No registrations found.</td></tr>';
                    return;
                }

                registrations.forEach(reg => {
                    const row = document.createElement('tr');
                    row.className = 'border-b hover:bg-gray-50';
                    row.innerHTML = `
                        <td class="py-3 px-4 font-mono text-sm">${reg.reg_id || 'N/A'}</td>
                        <td class="py-3 px-4">${reg.full_name}</td>
                        <td class="py-3 px-4">${reg.email}</td>
                        <td class="py-3 px-4">${reg.affiliation}</td>
                        <td class="py-3 px-4">${reg.category}</td>
                        <td class="py-3 px-4 text-sm text-gray-600">${reg.special_requirements || 'N/A'}</td>
                        <td class="py-3 px-4">
                            <button class="delete-btn text-red-600 hover:text-red-800" data-id="${reg.id}">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                lucide.createIcons();
            };

            tableBody.addEventListener('click', async (event) => {
                const deleteButton = event.target.closest('.delete-btn');
                if (deleteButton) {
                    const registrationId = deleteButton.dataset.id;
                    if (confirm('Are you sure you want to delete this registration?')) {
                        try {
                            const response = await fetch('delete_registration.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ id: registrationId })
                            });
                            
                            if (!response.ok) {
                                const errorResult = await response.json();
                                throw new Error(errorResult.message || 'Failed to delete.');
                            }
                            
                            // FIXED: This now correctly calls the function to reload the table
                            loadRegistrations(); 

                        } catch (error) {
                            alert('Error: ' + error.message);
                        }
                    }
                }
            });

            // Load the data when the page first opens
            loadRegistrations();
        });
    </script>
</body>
</html>