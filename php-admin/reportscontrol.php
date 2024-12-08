<?php
// Set the content type to JSON for the response
header('Content-Type: application/json');

// Database connection parameters
$host = 'localhost';
$dbname = 'clinicalog';
$username = 'root';
$password = '';

try {
    // Create a new PDO instance to connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Set error mode to exception

    // Get the year from the URL parameter, or default to 2024 if not provided
    $year = isset($_GET['year']) ? (int)$_GET['year'] : 2024;

    // Query 1: Get monthly transaction counts by patient type for all purposes in the specified year
    $stmt1 = $pdo->prepare("SELECT 
            MONTH(t.transac_date) AS month,
            COUNT(CASE WHEN p.patient_patienttype = 'Student' THEN 1 END) AS students,
            COUNT(CASE WHEN p.patient_patienttype = 'Faculty' THEN 1 END) AS faculty,
            COUNT(CASE WHEN p.patient_patienttype = 'Staff' THEN 1 END) AS staff,
            COUNT(CASE WHEN p.patient_patienttype = 'Extension' THEN 1 END) AS extension
        FROM 
            transactions t
        INNER JOIN  
            patients p ON t.transac_patientid = p.patient_id
        WHERE 
            YEAR(t.transac_date) = :year AND t.transac_status = 'Done'
        GROUP BY 
            MONTH(t.transac_date)
        ORDER BY  
            month
    ");
    $stmt1->execute(['year' => $year]);  // Execute the query with the year parameter
    $results1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);  // Fetch all results as an associative array

    // Query 2: Get monthly transaction counts for 'Medical Certificate Issuance' purpose by patient type
    $stmt2 = $pdo->prepare("SELECT 
            MONTH(t.transac_date) AS month,
            p.patient_patienttype,
            COUNT(*) AS total_count
        FROM 
            transactions t
        INNER JOIN 
            patients p ON t.transac_patientid = p.patient_id
        WHERE 
            t.transac_purpose = 'Medical Certificate Issuance' 
            AND YEAR(t.transac_date) = :year AND t.transac_status = 'Done'
        GROUP BY 
            MONTH(t.transac_date), p.patient_patienttype
        ORDER BY 
            month
    ");
    $stmt2->execute(['year' => $year]);  // Execute the query for medical certificate issuance
    $results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);  // Fetch all results for medical certificate issuance

    // Query 3: Get monthly transaction counts for 'Medical Consultation and Treatment' by patient type
    $stmt3 = $pdo->prepare("SELECT 
            MONTH(t.transac_date) AS month,
            p.patient_patienttype,
            COUNT(*) AS total_count
        FROM 
            transactions t
        INNER JOIN 
            patients p ON t.transac_patientid = p.patient_id
        WHERE 
            t.transac_purpose = 'Medical Consultation and Treatment' 
            AND YEAR(t.transac_date) = :year AND t.transac_status = 'Done'
        GROUP BY 
            MONTH(t.transac_date), p.patient_patienttype
        ORDER BY 
            month
    ");
    $stmt3->execute(['year' => $year]);  // Execute the query for consultation and treatment purposes
    $results3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);  // Fetch all results for consultations

    // Query 4: Get monthly transaction counts for 'Dental Check Up & Treatment' by patient type
    $stmt4 = $pdo->prepare("SELECT 
            MONTH(t.transac_date) AS month,
            p.patient_patienttype,
            COUNT(*) AS total_count
        FROM 
            transactions t
        INNER JOIN 
            patients p ON t.transac_patientid = p.patient_id
        WHERE 
            t.transac_purpose = 'Dental Check Up & Treatment' 
            AND YEAR(t.transac_date) = :year AND t.transac_status = 'Done'
        GROUP BY 
            MONTH(t.transac_date), p.patient_patienttype
        ORDER BY 
            month
    ");
    $stmt4->execute(['year' => $year]);  // Execute the query for dental checkups
    $results4 = $stmt4->fetchAll(PDO::FETCH_ASSOC);  // Fetch all results for dental checkups

    // New Query: Get transaction counts by category for all patient types (students, faculty, staff, extension)
    $stmt5 = $pdo->prepare("SELECT 
            SUBSTRING_INDEX(SUBSTRING_INDEX(s.student_program, '(', -1), ')', 1) AS category, 
            COUNT(t.transac_id) AS total_count
        FROM 
            patstudents s
        JOIN 
            transactions t ON s.student_patientid = t.transac_patientid
        WHERE 
            t.transac_status = 'Done' 
            AND YEAR(t.transac_date) = :year
        GROUP BY 
            category
        UNION ALL
        SELECT 
            'Faculty' AS category, 
            COUNT(t.transac_id) AS total_count
        FROM 
            patfaculties f
        JOIN 
            transactions t ON f.faculty_patientid = t.transac_patientid
        WHERE 
            t.transac_status = 'Done' 
            AND YEAR(t.transac_date) = :year
        UNION ALL
        SELECT 
            'Staff' AS category, 
            COUNT(t.transac_id) AS total_count
        FROM 
            patstaffs st
        JOIN 
            transactions t ON st.staff_patientid = t.transac_patientid
        WHERE 
            t.transac_status = 'Done' 
            AND YEAR(t.transac_date) = :year
        UNION ALL
        SELECT 
            'Extension' AS category, 
            COUNT(t.transac_id) AS total_count
        FROM 
            patextensions e
        JOIN 
            transactions t ON e.exten_patientid = t.transac_patientid
        WHERE 
            t.transac_status = 'Done' 
            AND YEAR(t.transac_date) = :year");
    $stmt5->execute(['year' => $year]);  // Execute the query for transaction counts by category
    $results5 = $stmt5->fetchAll(PDO::FETCH_ASSOC);  // Fetch all results for transaction counts by category

    // Initialize an array to hold data for transaction counts by category (students, faculty, staff, extension)
    $categoryData = [
        'students' => [],
        'faculty' => 0,
        'staff' => 0,
        'extension' => 0,
    ];

    // Process the results from the transaction counts by category query
    foreach ($results5 as $row) {
        if ($row['category'] == 'Faculty') {
            $categoryData['faculty'] = (int)$row['total_count'];
        } elseif ($row['category'] == 'Staff') {
            $categoryData['staff'] = (int)$row['total_count'];
        } elseif ($row['category'] == 'Extension') {
            $categoryData['extension'] = (int)$row['total_count'];
        } else {
            // Process students by program
            $categoryData['students'][$row['category']] = (int)$row['total_count'];
        }
    }

    // Initialize arrays for month names and transaction data by patient type (students, faculty, staff, extension)
    $months = range(1, 12);  // Array of month numbers (1-12)
    $data = [
        'months' => array_map(function($month) { return date('F', mktime(0, 0, 0, $month, 10)); }, $months),
        'students' => array_fill(0, 12, 0),
        'faculty' => array_fill(0, 12, 0),
        'staff' => array_fill(0, 12, 0),
        'extension' => array_fill(0, 12, 0),
    ];

    // Populate the data array with results from the first query (general transactions)
    foreach ($results1 as $row) {
        $monthIndex = $row['month'] - 1;
        $data['students'][$monthIndex] = (int)$row['students'];
        $data['faculty'][$monthIndex] = (int)$row['faculty'];
        $data['staff'][$monthIndex] = (int)$row['staff'];
        $data['extension'][$monthIndex] = (int)$row['extension'];
    }

    // Initialize arrays for medical certificate data
    $medicalData = [
        'medical_students' => array_fill(0, 12, 0),
        'medical_faculty' => array_fill(0, 12, 0),
        'medical_staff' => array_fill(0, 12, 0),
        'medical_extension' => array_fill(0, 12, 0),
    ];

    // Populate medical data arrays with results from the second query (Medical Certificate Issuance)
    foreach ($results2 as $row) {
        $monthIndex = $row['month'] - 1;
        if ($row['patient_patienttype'] === 'Student') {
            $medicalData['medical_students'][$monthIndex] = (int)$row['total_count'];
        } elseif ($row['patient_patienttype'] === 'Faculty') {
            $medicalData['medical_faculty'][$monthIndex] = (int)$row['total_count'];
        } elseif ($row['patient_patienttype'] === 'Staff') {
            $medicalData['medical_staff'][$monthIndex] = (int)$row['total_count'];
        } elseif ($row['patient_patienttype'] === 'Extension') {
            $medicalData['medical_extension'][$monthIndex] = (int)$row['total_count'];
        }
    }

    // Initialize arrays for medical consultation and dental checkup data
    $consultationData = [
        'consultation_students' => array_fill(0, 12, 0),
        'consultation_faculty' => array_fill(0, 12, 0),
        'consultation_staff' => array_fill(0, 12, 0),
        'consultation_extension' => array_fill(0, 12, 0),
    ];

    // Populate consultation data arrays with results from the third query
    foreach ($results3 as $row) {
        $monthIndex = $row['month'] - 1;
        if ($row['patient_patienttype'] === 'Student') {
            $consultationData['consultation_students'][$monthIndex] = (int)$row['total_count'];
        } elseif ($row['patient_patienttype'] === 'Faculty') {
            $consultationData['consultation_faculty'][$monthIndex] = (int)$row['total_count'];
        } elseif ($row['patient_patienttype'] === 'Staff') {
            $consultationData['consultation_staff'][$monthIndex] = (int)$row['total_count'];
        } elseif ($row['patient_patienttype'] === 'Extension') {
            $consultationData['consultation_extension'][$monthIndex] = (int)$row['total_count'];
        }
    }

    // Initialize arrays for dental checkup data
    $dentalData = [
        'dental_students' => array_fill(0, 12, 0),
        'dental_faculty' => array_fill(0, 12, 0),
        'dental_staff' => array_fill(0, 12, 0),
        'dental_extension' => array_fill(0, 12, 0),
    ];

    // Populate dental data arrays with results from the fourth query
    foreach ($results4 as $row) {
        $monthIndex = $row['month'] - 1;
        if ($row['patient_patienttype'] === 'Student') {
            $dentalData['dental_students'][$monthIndex] = (int)$row['total_count'];
        } elseif ($row['patient_patienttype'] === 'Faculty') {
            $dentalData['dental_faculty'][$monthIndex] = (int)$row['total_count'];
        } elseif ($row['patient_patienttype'] === 'Staff') {
            $dentalData['dental_staff'][$monthIndex] = (int)$row['total_count'];
        } elseif ($row['patient_patienttype'] === 'Extension') {
            $dentalData['dental_extension'][$monthIndex] = (int)$row['total_count'];
        }
    }

    // Return the combined response as a JSON object
    echo json_encode([
        'status' => 'success',
        'year' => $year,
        'categoryData' => $categoryData,
        'data' => $data,
        'medicalData' => $medicalData,
        'consultationData' => $consultationData,
        'dentalData' => $dentalData,
    ]);
} catch (PDOException $e) {
    // Handle database connection errors and respond with an error message
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>