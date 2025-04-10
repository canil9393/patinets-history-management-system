<?php
session_start();
include 'db_connect.php';

// Check if the receptionist is logged in
if (!isset($_SESSION['receptionist_id'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in receptionist's name
$receptionist_name = isset($_SESSION['receptionist_name']) ? $_SESSION['receptionist_name'] : 'Admin';

// Handle export to CSV
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="patient_records.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Patient ID', 'Patient Name', 'Visited Date', 'Hospital Name', 'Doctor ID', 'Doctor Consulted', 'Reason for Visit', 'Diagnosis', 'Lab Tests', 'Test Results', 'Medications Prescribed', 'Using or Not']);

    $query = "SELECT * FROM patient_records";
    $conditions = [];
    $params = [];

    if (isset($_GET['patient_id']) && !empty(trim($_GET['patient_id']))) {
        $patient_id = trim($_GET['patient_id']);
        $conditions[] = "patient_id = :patient_id";
        $params[':patient_id'] = $patient_id;
    }
    if (isset($_GET['reason_for_visit']) && !empty($_GET['reason_for_visit'])) {
        $conditions[] = "reason_for_visit = :reason_for_visit";
        $params[':reason_for_visit'] = $_GET['reason_for_visit'];
    }
    if (isset($_GET['using_or_not']) && !empty($_GET['using_or_not'])) {
        $conditions[] = "using_or_not = :using_or_not";
        $params[':using_or_not'] = $_GET['using_or_not'];
    }
    if (isset($_GET['visited_date']) && !empty($_GET['visited_date'])) {
        $conditions[] = "checkup_date = :visited_date";
        $params[':visited_date'] = $_GET['visited_date'];
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        fputcsv($output, [
            $row['patient_id'],
            $row['name'],
            $row['checkup_date'],
            $row['hospital_name'],
            $row['doctor_id'],
            $row['doctor_consulted'],
            $row['reason_for_visit'],
            $row['diagnosis'],
            $row['lab_tests'],
            $row['test_results'],
            $row['medications_prescribed'],
            $row['using_or_not']
        ]);
    }
    fclose($output);
    exit();
}

// Handle adding new patient record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_patient'])) {
    $patient_id = $_POST['patient_id'];
    $name = $_POST['name'];
    $checkup_date = $_POST['checkup_date'];
    $hospital_name = $_POST['hospital_name'];
    $doctor_id = $_POST['doctor_id'];
    $doctor_consulted = $_POST['doctor_consulted'];
    $reason_for_visit = $_POST['reason_for_visit'];
    $diagnosis = $_POST['diagnosis'];
    $lab_tests = $_POST['lab_tests'];
    $test_results = $_POST['test_results'];
    $medications_prescribed = $_POST['medications_prescribed'];
    $using_or_not = $_POST['using_or_not'];

    $stmt = $conn->prepare("INSERT INTO patient_records (patient_id, name, checkup_date, hospital_name, doctor_id, doctor_consulted, reason_for_visit, diagnosis, lab_tests, test_results, medications_prescribed, using_or_not) VALUES (:patient_id, :name, :checkup_date, :hospital_name, :doctor_id, :doctor_consulted, :reason_for_visit, :diagnosis, :lab_tests, :test_results, :medications_prescribed, :using_or_not)");
    $stmt->bindParam(':patient_id', $patient_id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':checkup_date', $checkup_date);
    $stmt->bindParam(':hospital_name', $hospital_name);
    $stmt->bindParam(':doctor_id', $doctor_id);
    $stmt->bindParam(':doctor_consulted', $doctor_consulted);
    $stmt->bindParam(':reason_for_visit', $reason_for_visit);
    $stmt->bindParam(':diagnosis', $diagnosis);
    $stmt->bindParam(':lab_tests', $lab_tests);
    $stmt->bindParam(':test_results', $test_results);
    $stmt->bindParam(':medications_prescribed', $medications_prescribed);
    $stmt->bindParam(':using_or_not', $using_or_not);

    if ($stmt->execute()) {
        $message = "Patient record added successfully!";
    } else {
        $message = "Error adding patient record.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
    <title>Patient Medical History Dashboard</title>
    <style>
        @media print {
            .dashboard-container header, .search-section, .results-actions, .pagination {
                display: none;
            }
            .results-section {
                box-shadow: none;
                padding: 0;
            }
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                border: 1px solid #ddd;
            }
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto; /* Reduced margin to ensure more space for content */
            padding: 20px;
            width: 60%; /* Increased width for better visibility */
            max-height: 80vh; /* Set maximum height relative to viewport */
            overflow-y: auto; /* Enable vertical scrolling */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .close {
            float: right;
            font-size: 24px;
            cursor: pointer;
        }

        .modal-content form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .modal-content input, .modal-content select {
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%; /* Ensure inputs take full width */
            box-sizing: border-box; /* Include padding in width calculation */
        }

        .modal-content button {
            padding: 10px;
            background-color: #2c3e50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: auto; /* Allow button to size naturally */
        }

        .modal-content button:hover {
            background-color: #34495e;
        }

        .message {
            color: #27ae60;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Patient Medical History Dashboard</h1>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($receptionist_name); ?></span>
                <a href="logout.php" style="margin-left: 10px; color: #e74c3c; text-decoration: none;">Logout</a>
            </div>
        </header>

        <div class="search-section">
            <div class="search-box">
                <form method="GET" action="">
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" name="patient_id" placeholder="Search by Patient ID (e.g., #P001)" value="<?php echo isset($_GET['patient_id']) ? htmlspecialchars($_GET['patient_id']) : ''; ?>">
                    </div>
                    <select name="reason_for_visit">
                        <option value="">All Reasons for Visit</option>
                        <option value="general" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'general') ? 'selected' : ''; ?>>General Checkup</option>
                        <option value="cardiac" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'cardiac') ? 'selected' : ''; ?>>Cardiac Checkup</option>
                        <option value="dental" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'dental') ? 'selected' : ''; ?>>Dental Checkup</option>
                        <option value="vision" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'vision') ? 'selected' : ''; ?>>Vision Checkup</option>
                        <option value="thyroid symptoms" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'thyroid symptoms') ? 'selected' : ''; ?>>Thyroid Symptoms</option>
                        <option value="ear pain" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'ear pain') ? 'selected' : ''; ?>>Ear Pain</option>
                        <option value="stomach pain" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'stomach pain') ? 'selected' : ''; ?>>Stomach Pain</option>
                        <option value="weakness due to summer" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'weakness due to summer') ? 'selected' : ''; ?>>Weakness due to Summer</option>
                        <option value="migraine" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'migraine') ? 'selected' : ''; ?>>Migraine</option>
                        <option value="shin pain" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'shin pain') ? 'selected' : ''; ?>>Shin Pain</option>
                        <option value="knee pain" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'knee pain') ? 'selected' : ''; ?>>Knee Pain</option>
                        <option value="fever" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'fever') ? 'selected' : ''; ?>>Fever</option>
                        <option value="chicken pox" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'chicken pox') ? 'selected' : ''; ?>>Chicken Pox</option>
                        <option value="vomting" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'vomting') ? 'selected' : ''; ?>>Vomiting</option>
                        <option value="recurring stomach infection" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'recurring stomach infection') ? 'selected' : ''; ?>>Recurring Stomach Infection</option>
                        <option value="high bp" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'high bp') ? 'selected' : ''; ?>>High BP</option>
                        <option value="eye redness" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'eye redness') ? 'selected' : ''; ?>>Eye Redness</option>
                        <option value="joint pain" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'joint pain') ? 'selected' : ''; ?>>Joint Pain</option>
                        <option value="appendix pain" <?php echo (isset($_GET['reason_for_visit']) && $_GET['reason_for_visit'] == 'appendix pain') ? 'selected' : ''; ?>>Appendix Pain</option>
                    </select>
                    <select name="using_or_not">
                        <option value="">All Using Status</option>
                        <option value="yes" <?php echo (isset($_GET['using_or_not']) && $_GET['using_or_not'] == 'yes') ? 'selected' : ''; ?>>Yes</option>
                        <option value="no" <?php echo (isset($_GET['using_or_not']) && $_GET['using_or_not'] == 'no') ? 'selected' : ''; ?>>No</option>
                    </select>
                    <input type="date" name="visited_date" class="date-filter" placeholder="Visited Date" value="<?php echo isset($_GET['visited_date']) ? htmlspecialchars($_GET['visited_date']) : ''; ?>">
                    <button type="submit" class="search-btn">Search</button>
                </form>
            </div>
        </div>

        <div class="results-section">
            <div class="results-header">
                <h2>Medical Checkup History</h2>
                <div class="results-actions">
                    <button class="export-btn" onclick="window.location.href='?export=1&<?php echo http_build_query($_GET); ?>'"><i class="fas fa-download"></i> Export</button>
                    <button class="print-btn" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                    <button class="add-btn" onclick="document.getElementById('addModal').style.display='block'"><i class="fas fa-plus"></i> Add</button>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>Patient Name</th>
                            <th>Visited Date</th>
                            <th>Hospital Name</th>
                            <th>Doctor ID</th>
                            <th>Doctor Consulted</th>
                            <th>Reason for Visit</th>
                            <th>Diagnosis</th>
                            <th>Lab Tests</th>
                            <th>Test Results</th>
                            <th>Medications Prescribed</th>
                            <th>Using or Not</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $query = "SELECT * FROM patient_records";
                            $conditions = [];
                            $params = [];

                            if (isset($_GET['patient_id']) && !empty(trim($_GET['patient_id']))) {
                                $patient_id = trim($_GET['patient_id']);
                                $conditions[] = "patient_id = :patient_id";
                                $params[':patient_id'] = $patient_id;
                            }
                            if (isset($_GET['reason_for_visit']) && !empty($_GET['reason_for_visit'])) {
                                $conditions[] = "reason_for_visit = :reason_for_visit";
                                $params[':reason_for_visit'] = $_GET['reason_for_visit'];
                            }
                            if (isset($_GET['using_or_not']) && !empty($_GET['using_or_not'])) {
                                $conditions[] = "using_or_not = :using_or_not";
                                $params[':using_or_not'] = $_GET['using_or_not'];
                            }
                            if (isset($_GET['visited_date']) && !empty($_GET['visited_date'])) {
                                $conditions[] = "checkup_date = :visited_date";
                                $params[':visited_date'] = $_GET['visited_date'];
                            }

                            if (!empty($conditions)) {
                                $query .= " WHERE " . implode(" AND ", $conditions);
                            }

                            $stmt = $conn->prepare($query);
                            foreach ($params as $key => $value) {
                                $stmt->bindValue($key, $value);
                            }
                            $stmt->execute();
                            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (count($results) > 0) {
                                foreach ($results as $row) {
                                    $status_class = isset($row['using_or_not']) && strtolower($row['using_or_not']) == 'yes' ? 'normal' : 'abnormal';
                                    echo "<tr>";
                                    echo "<td>" . (isset($row['patient_id']) ? htmlspecialchars($row['patient_id']) : '') . "</td>";
                                    echo "<td>" . (isset($row['name']) ? htmlspecialchars($row['name']) : '') . "</td>";
                                    echo "<td>" . (isset($row['checkup_date']) ? htmlspecialchars($row['checkup_date']) : '') . "</td>";
                                    echo "<td>" . (isset($row['hospital_name']) ? htmlspecialchars($row['hospital_name']) : '') . "</td>";
                                    echo "<td>" . (isset($row['doctor_id']) ? htmlspecialchars($row['doctor_id']) : '') . "</td>";
                                    echo "<td>" . (isset($row['doctor_consulted']) ? htmlspecialchars($row['doctor_consulted']) : '') . "</td>";
                                    echo "<td>" . (isset($row['reason_for_visit']) ? htmlspecialchars($row['reason_for_visit']) : '') . "</td>";
                                    echo "<td>" . (isset($row['diagnosis']) ? htmlspecialchars($row['diagnosis']) : '') . "</td>";
                                    echo "<td>" . (isset($row['lab_tests']) ? htmlspecialchars($row['lab_tests']) : '') . "</td>";
                                    echo "<td>" . (isset($row['test_results']) ? htmlspecialchars($row['test_results']) : '') . "</td>";
                                    echo "<td>" . (isset($row['medications_prescribed']) ? htmlspecialchars($row['medications_prescribed']) : '') . "</td>";
                                    echo "<td><span class='status " . $status_class . "'>" . (isset($row['using_or_not']) ? htmlspecialchars(ucfirst(strtolower($row['using_or_not']))) : '') . "</span></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='12' class='no-records'>No records found for the selected criteria.</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='12' class='no-records'>Error: Table 'patient_records' does not exist or query failed. Please recreate the table or check the database.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
                <button class="page-btn active">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn">3</button>
                <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>

        <!-- Add Modal -->
        <div id="addModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="document.getElementById('addModal').style.display='none'">×</span>
                <?php if (isset($message)) echo "<div class='message'>$message</div>"; ?>
                <form method="POST" action="">
                    <input type="text" name="patient_id" placeholder="Patient ID (e.g., P001)" required>
                    <input type="text" name="name" placeholder="Patient Name" required>
                    <input type="date" name="checkup_date" placeholder="Visited Date" required>
                    <input type="text" name="hospital_name" placeholder="Hospital Name" required>
                    <input type="text" name="doctor_id" placeholder="Doctor ID (e.g., D101)" required>
                    <input type="text" name="doctor_consulted" placeholder="Doctor Consulted" required>
                    <input type="text" name="reason_for_visit" placeholder="Reason for Visit" required>
                    <input type="text" name="diagnosis" placeholder="Diagnosis">
                    <input type="text" name="lab_tests" placeholder="Lab Tests">
                    <input type="text" name="test_results" placeholder="Test Results">
                    <input type="text" name="medications_prescribed" placeholder="Medications Prescribed" required>
                    <select name="using_or_not" required>
                        <option value="">Select Using Status</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                    <button type="submit" name="add_patient">Add Patient</button>
                </form>
            </div>
        </div>

        <script>
            // Close modal when clicking outside
            window.onclick = function(event) {
                var modal = document.getElementById('addModal');
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        </script>
    </div>
</body>
</html>