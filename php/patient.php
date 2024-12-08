<?php
// Patient class representing patient details
class Patient {
    public $patient_id;
    public $patient_lname;
    public $patient_fname;
    public $patient_mname;
    public $patient_dob;
    public $patient_email;
    public $patient_connum;
    public $patient_sex;
    public $patient_profile;
    public $patient_patienttype;
    public $patient_dateadded;
    public $patient_password;
    public $patient_status;
    public $patient_code; 
    public $next; 

    // Constructor to initialize patient object with necessary data
    public function __construct($id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, $password, $status, $code) {
        $this->patient_id = $id;
        $this->patient_lname = $lname;
        $this->patient_fname = $fname;
        $this->patient_mname = $mname; 
        $this->patient_dob = $dob;
        $this->patient_email = $email;
        $this->patient_connum = $connum;
        $this->patient_sex = $sex;
        $this->patient_profile = $profile;
        $this->patient_patienttype = $type;
        $this->patient_dateadded = $dateadded;
        $this->patient_password = $password;
        $this->patient_status = $status;
        $this->patient_code = $code;
        $this->next = null; // Initialize next pointer to null
    }
}
// Student class representing student details
class Student {
    public $student_id;
    public $student_idnum;
    public $patient_id;
    public $student_program;
    public $student_major;
    public $student_year;
    public $student_section;
    public $next; // Pointer to the next node

    // Constructor to initialize student object with necessary data
    public function __construct($id, $idnum, $patientid, $program, $major, $year, $section) {
        $this->student_id = $id;
        $this->student_idnum = $idnum;
        $this->patient_id = $patientid;
        $this->student_program = $program;
        $this->student_major = $major;
        $this->student_year = $year;
        $this->student_section = $section;
        $this->next = null; // Initialize next pointer to null
    }
}
// Faculty class representing faculty details
class Faculty {
    public $faculty_id;
    public $patient_id;
    public $faculty_idnum;
    public $faculty_college;
    public $faculty_depart;
    public $faculty_role;
    public $next; // Pointer to the next node

    // Constructor to initialize faculty object with necessary data
    public function __construct($id, $patientid, $idnum, $college, $depart, $role) {
        $this->faculty_id = $id;
        $this->patient_id = $patientid;
        $this->faculty_idnum = $idnum;
        $this->faculty_college = $college;
        $this->faculty_depart = $depart;
        $this->faculty_role = $role;
        $this->next = null; // Initialize next pointer to null
    }
}
// Staff class representing staff details
class Staff {
    public $staff_id;
    public $patient_id;
    public $staff_idnum;
    public $staff_office;
    public $staff_role;
    public $next; // Pointer to the next node

    // Constructor to initialize staff object with necessary data
    public function __construct($id, $patientid, $idnum, $office, $role) {
        $this->staff_id = $id;
        $this->patient_id = $patientid;
        $this->staff_idnum = $idnum;
        $this->staff_office = $office;
        $this->staff_role = $role;
        $this->next = null; // Initialize next pointer to null
    }
}
// Extension class representing extension details
class Extension {
    public $exten_id;
    public $patient_id;
    public $exten_idnum;
    public $exten_role;
    public $next; // Pointer to the next node

    // Constructor to initialize extension object with necessary data
    public function __construct($id, $patientid, $idnum, $role) {
        $this->exten_id = $id;
        $this->patient_id = $patientid;
        $this->exten_idnum = $idnum;
        $this->exten_role = $role;
        $this->next = null; // Initialize next pointer to null
    }
}
// Address class representing address details
class Address {
    public $address_id;
    public $patient_id;
    public $address_region;
    public $address_province;
    public $address_municipality;
    public $address_barangay;
    public $address_prkstrtadd;
    public $next; // Pointer to the next node

    // Constructor to initialize address object with necessary data
    public function __construct($id, $patientid, $region, $province, $municipality, $barangay, $prkstrtadd) {
        $this->address_id = $id;
        $this->patient_id = $patientid;
        $this->address_region = $region;
        $this->address_province = $province;
        $this->address_municipality = $municipality;
        $this->address_barangay = $barangay;
        $this->address_prkstrtadd = $prkstrtadd;
        $this->next = null; // Initialize next pointer to null
    }
}
// EmergencyContact class representing emergency contact details
class EmergencyContact {
    public $emcon_contactid;
    public $patient_id;
    public $emcon_conname;
    public $emcon_relationship;
    public $emcon_connum;
    public $next; // Pointer to the next node

    // Constructor to initialize emergency contact object with necessary data
    public function __construct($id, $patientid, $conname, $relationship, $connum) {
        $this->emcon_contactid = $id;
        $this->patient_id = $patientid;
        $this->emcon_conname = $conname;
        $this->emcon_relationship = $relationship;
        $this->emcon_connum = $connum;
        $this->next = null; // Initialize next pointer to null
    }
}

// PatientNode class represents a node in a linked list for storing patient data
class PatientNode{
    public $item;
    public $next;

    // Constructor to initialize the node with a patient item
    public function __construct($item) {
        $this->item = $item;
        $this->next = null;
    }
}
// PatientLinkedList class for managing a linked list of patients
class PatientLinkedList {
    public $head;

    // Constructor to initialize an empty linked list
    public function __construct() {
        $this->head = null;
    }

    // Adds a patient node to the linked list
    public function add($item) {
        $newNode = new PatientNode($item);
        if ($this->head === null) {
            $this->head = $newNode;
        } else {
            $current = $this->head;
            while ($current->next !== null) {
                $current = $current->next;
            }
            $current->next = $newNode;
        }
    }

    // Retrieves all patient nodes in the linked list
    public function getAllNodes() {
        $nodes = [];
        $current = $this->head;
        while ($current !== null) {
            $nodes[] = $current->item;
            $current = $current->next;
        }
        return $nodes;
    }

    // Checks if a patient exists by their email address
    public function PatientExists($email) {
        $current = $this->head;
        while ($current !== null) {
            if ($current->patient_email  === $email) { 
                return true; 
            }
            $current = $current->next;
        }
        return false; 
    }

    // Finds a patient by their email address
    public function PatientEmailExists($email) {
        $current = $this->head;
        while ($current !== null) {
            if ($current->patient_email === $email) { 
                return $current; 
            }
            $current = $current->next;
        }
        return false; 
    }
    
    // Checks if a student exists by their ID number
    public function StudentExists($id) {
        $current = $this->head;
        while ($current !== null) {
            if (strcasecmp($current->item->student_idnum, $id) === 0) { 
                return true; 
            }
            $current = $current->next;
        }
        return false; 
    }

    // Checks if a faculty exists by their ID number
    public function FacultyExists($id) {
        $current = $this->head;
        while ($current !== null) {
            if (strcasecmp($current->item->faculty_idnum, $id) === 0) { 
                return true; 
            } 
            $current = $current->next;
        }
        return false; 
    }

    // Checks if a staff exists by their ID number
    public function StaffExists($id) {
        $current = $this->head;
        while ($current !== null) {
            if (strcasecmp($current->item->staff_idnum, $id) === 0) { 
                return true; 
            }
            $current = $current->next;
        }
        return false; 
    }

    // Checks if an extension exists by their ID number
    public function ExtensionExists($id) {
        $current = $this->head;
        while ($current !== null) {
            if (strcasecmp($current->item->exten_idnum, $id) === 0) { 
                return true; 
            }
            $current = $current->next;
        }
        return false; 
    }

    

    
}
// PatientManager class to manage patients and related entities like students, faculties, staffs,extensions, address, and emergency contact.
class PatientManager{
    private $db;
    private $patients;
    private $students;
    private $faculties;
    private $staffs;
    private $extensions;
    private $addresses;
    private $emergencycon;

    // Constructor to initialize database connection and load all patient-related data into memory
    public function __construct($db) {
        $this->db = $db; 
        $this->patients = new PatientLinkedList();
        $this->students = new PatientLinkedList();
        $this->faculties = new PatientLinkedList();
        $this->staffs = new PatientLinkedList();
        $this->extensions = new PatientLinkedList();
        $this->addresses = new PatientLinkedList();
        $this->emergencycon = new PatientLinkedList();

        // Load all patient-related data from the database
        $this->loadPatients();
        $this->loadFaculties();
        $this->loadStaff();
        $this->loadStudents();
        $this->loadExtensions();
        $this->loadEmergencyContacts();
        $this->loadAddresses();
    }
 
    // Loads patient data from the database and adds it to the linked list of patients
    private function loadPatients() {
        $sql = "SELECT * FROM patients"; // Adjust the SQL as needed
        $stmt = $this->db->query($sql); // Prepare the SQL query
        
        // Iterate through the result set and create Patient objects
       while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $patient = new Patient(
                $row['patient_id'], $row['patient_lname'], $row['patient_fname'], 
                $row['patient_mname'], $row['patient_dob'], $row['patient_email'], 
                $row['patient_connum'], $row['patient_sex'], $row['patient_profile'], 
                $row['patient_patienttype'], $row['patient_dateadded'], $row['patient_password'], 
                $row['patient_status'], $row['patient_code']
            );        
            $this->patients->add($patient); 
        }
    }
    
    // Loads student data from the database and adds it to the linked list of students
    private function loadStudents() {
        $sql = "SELECT * FROM patstudents";
        $stmt = $this->db->query($sql);

        // Iterate through the result set and create Student objects
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $student = new Student(
                $row['student_id'], $row['student_idnum'], $row['student_patientid'], 
                $row['student_program'], $row['student_major'], $row['student_year'], 
                $row['student_section']
            );
            $this->students->add($student);
        }
    }

    // Loads faculty data from the database and adds it to the linked list of faculties
    private function loadFaculties() {
        $sql = "SELECT * FROM patfaculties";
        $stmt = $this->db->query($sql);
        // Iterate through the result set and create Faculty objects
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $faculty = new Faculty(
                $row['faculty_id'], $row['faculty_patientid'], $row['faculty_idnum'], 
                $row['faculty_college'], $row['faculty_depart'], $row['faculty_role']
            );
            $this->faculties->add($faculty);
        }
    }

    // Loads staff data from the database and adds it to the linked list of staff members
    private function loadStaff() {
        $sql = "SELECT * FROM patstaffs";
        $stmt = $this->db->query($sql);
        // Iterate through the result set and create Staff objects
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $staff = new Staff(
                $row['staff_id'], $row['staff_patientid'], $row['staff_idnum'], 
                $row['staff_office'], $row['staff_role']
            );
            $this->staffs->add($staff);
        }
    }

    // Loads extension data from the database and adds it to the linked list of extensions
    private function loadExtensions() {
        $sql = "SELECT * FROM patextensions";
        $stmt = $this->db->query($sql);
        // Iterate through the result set and create Extension objects
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $extension = new Extension(
                $row['exten_id'], $row['exten_patientid'], $row['exten_idnum'], 
                $row['exten_role']
            );
            $this->extensions->add($extension);
        }
    }
    
    // Loads address data from the database and adds it to the linked list of addresses
    private function loadAddresses() {
        $sql = "SELECT * FROM pataddresses";
        $stmt = $this->db->query($sql);
        // Iterate through the result set and create Address objects
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $address = new Address(
                $row['address_id'], $row['address_patientid'], $row['address_region'], 
                $row['address_province'], $row['address_municipality'], $row['address_barangay'], 
                $row['address_prkstrtadd']
            );
            $this->addresses->add($address);
        }
    }
    
    // Loads emergency contact data from the database and adds it to the linked list of emergency contacts
    private function loadEmergencyContacts() {
        $sql = "SELECT * FROM patemergencycontacts";
        $stmt = $this->db->query($sql);
        // Iterate through the result set and create EmergencyContact objects
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emergencyContact = new EmergencyContact(
                $row['emcon_contactid'], $row['emcon_patientid'], $row['emcon_conname'], 
                $row['emcon_relationship'], $row['emcon_connum']
            );
            $this->emergencycon->add($emergencyContact);
        }
    }

    // Checks if a user exists with the given email and password, and returns the corresponding Patient object if valid
    public function userpatientExists($email, $password) {
        $sql = "SELECT * FROM patients WHERE patient_email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Verify the password and return the patient data if valid
        if ($row && password_verify($password, $row['patient_password'])) {
            return new Patient(
                $row['patient_id'], $row['patient_lname'], $row['patient_fname'], 
                $row['patient_mname'], $row['patient_dob'], $row['patient_email'], 
                $row['patient_connum'], $row['patient_sex'], $row['patient_profile'], 
                $row['patient_patienttype'], $row['patient_dateadded'], $row['patient_password'], 
                $row['patient_status'], $row['patient_code']
            );
        }
    
        return false; // Return false if no valid user found
    }

    // Retrieves a patient's data by their patient ID and returns a Patient object
    public function getPatientData($patient_id) {
        $sql = "SELECT * FROM patients WHERE patient_id = :patientid";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':patientid', $patient_id);
        $stmt->execute();
    
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Return a Patient object with the retrieved data
        if ($row) {
            return new Patient(
                $row['patient_id'], $row['patient_lname'], $row['patient_fname'], 
                $row['patient_mname'], $row['patient_dob'], $row['patient_email'], 
                $row['patient_connum'], $row['patient_sex'], $row['patient_profile'], 
                $row['patient_patienttype'], $row['patient_dateadded'], $row['patient_password'], 
                $row['patient_status'], $row['patient_code']
            );
        }
    
        return false;  // Return false if no patient found with the given ID
    }
    

    
    // Insert a new patient into the database and handle various patient-related operations.
    public function insertPatient($admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, $password, $status, $code) {
        try {
            // Set admin ID for logging
            $setAdminIdQuery = "SET @admin_id = :admin_id";
            $setStmt = $this->db->prepare($setAdminIdQuery);
            $setStmt->bindValue(':admin_id', $admin_id);
            $setStmt->execute();
    
            // Check if the patient already exists
            if ($this->patients->patientExists($email)) {
                return ['status' => 'error', 'message' => 'Patient already exists.'];
            }
    
            // Insert the patient
            $sql = "INSERT INTO patients 
                    (patient_lname, patient_fname, patient_mname, patient_dob, patient_email, patient_connum, patient_sex, patient_profile, patient_patienttype, patient_dateadded, patient_password, patient_status, patient_code)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
    
            // Define the parameters to insert the patient
            $params = [
                $lname, 
                $fname, 
                $mname === '' ? null : $mname,  
                $dob, 
                $email, 
                $connum, 
                $sex, 
                $profile, 
                $type, 
                $dateadded, 
                $password, 
                $status, 
                $code
            ];
    
            // Execute the SQL query
            $stmt->execute($params);
    
            // Get the inserted patient ID
            $patient_id = $this->db->lastInsertId();
    
            // Add the patient to the linked list
            $patient = new Patient($patient_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, $password, $status, $code);
            $this->patients->add($patient);
    
            // Return success status with patient ID
            return ['status' => 'success', 'message' => 'Patient inserted successfully.', 'patient_id' => $patient_id];
    
        } catch (PDOException $e) {
            // Log the error and return failure status with error details
            error_log("Error inserting patient: " . $e->getMessage());
    
            return [
                'status' => 'error',
                'message' => 'Error inserting patient: ' . $e->getMessage(),  
                'details' => [
                    'sqlState' => $e->getCode(),  
                    'params' => json_encode($params)  
                ]
            ];
        }
    }
    
    
    // Insert a new student associated with a patient into the database.
    public function insertStudent($idnum, $patientid, $program, $major, $year, $section) {
        // Check if the student already exists by their ID number
        if ($this->students->studentExists($idnum)) {
            return ['status' => 'error', 'message' => 'Student already exists.'];
        }
    
        // SQL query to insert student data
        $sql = "INSERT INTO patstudents (student_idnum, student_patientid, student_program, student_major, student_year, student_section)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
    
        try {
            // Execute the SQL query with student data
            $stmt->execute([$idnum, $patientid, $program === '' ? null : $program, $major === '' ? null : $major, $year === '' ? null : $year, $section === '' ? null : $section]);
            
            // Get the inserted student ID
            $student_id = $this->db->lastInsertId();
            return ['status' => 'success', 'message' => 'Student inserted successfully.', 'student_id' => $student_id];
    
        } catch (PDOException $e) {
            // Log the error and return failure status with error message
            error_log("Error inserting student: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error inserting student. Please try again later.'];
        }
    }
    
    // Insert a new faculty associated with a patient into the database.
    public function insertFaculty($patientid, $idnum, $college, $depart, $role) {    
        // Check if the faculty already exists by their ID number
        if ($this->faculties->facultyExists($idnum)) {
            return ['status' => 'error', 'message' => 'Faculty already exists.'];
        }
    
        // SQL query to insert faculty data
        $sql = "INSERT INTO patfaculties (faculty_patientid, faculty_idnum, faculty_college, faculty_depart, faculty_role)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
    
        try {
            // Execute the SQL query with faculty data
            $stmt->execute([$patientid, $idnum, $college, $depart, $role]);
            $faculty_id = $this->db->lastInsertId();
        
            // Get the inserted faculty ID
            return [
                'status' => 'success', 
                'message' => 'Faculty inserted successfully. Faculty ID: ' . $faculty_id 
            ];
        } catch (PDOException $e) {
            // Log the error and return failure status with error details
            $errorMessage = "Error inserting faculty: " . $e->getMessage();
            echo $errorMessage . "<br>";
            return [
                'status' => 'error', 
                'message' => 'Error inserting faculty. Please try again later. SQL Error: ' . $errorMessage 
            ];
        }
    }
    
    // Insert a new staff associated with a patient into the database.
    public function insertStaff($patientid, $idnum, $office, $role) {
        // Check if the staff already exists by their ID number
        if ($this->staffs->staffExists($idnum)) {
            return ['status' => 'error', 'message' => 'Staff already exists.'];
        }
    
        // SQL query to insert staff data
        $sql = "INSERT INTO patstaffs (staff_patientid, staff_idnum, staff_office, staff_role)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        try {
            // Execute the SQL query with staff data
            $stmt->execute([$patientid, $idnum, $office, $role]);

            // Get the inserted staff ID
            $staff_id = $this->db->lastInsertId();

            // Add the new staff member to the linked list
            $staff = new Staff($staff_id, $patientid, $idnum, $office, $role);
            $this->staffs->add($staff);
            return ['status' => 'success', 'message' => 'Staff inserted successfully.', 'staff_id' => $staff_id];

        } catch (PDOException $e) {
            // Log the error and return failure status with error message
            error_log("Error inserting staff: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error inserting staff. Please try again later.'];

        }
    }
    
    // Insert a new extension associated with a patient into the database.
    public function insertExtension($idnum, $patientid, $role) {
        // Check if the extension already exists by their ID number
        if ($this->extensions->ExtensionExists($idnum)) {
            return ['status' => 'error', 'message' => 'Extension already exists.'];
        }
    
        // SQL query to insert extension data
        $insertSql = "INSERT INTO patextensions (exten_patientid, exten_idnum, exten_role)
                      VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($insertSql);
        
        try {
            // Execute the SQL query with extension data
            $stmt->execute([$patientid, $idnum,  $role]);

            // Get the inserted extension ID
            $extension_id = $this->db->lastInsertId();

            // Add the new extension to the linked list
            $extension = new Extension($extension_id, $patientid, $idnum, $role);
            $this->extensions->add($extension); 
            return ['status' => 'success', 'message' => 'Extension inserted successfully.', 'staff_id' => $extension_id];
        } catch (PDOException $e) {
            // Log the error and return failure status with error message
            error_log("Error inserting staff: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error inserting extension. Please try again later.'];
        }
    }

    // Insert a new address associated with a patient into the database.
    public function insertAddress($patientid, $region, $province, $municipality, $barangay, $prkstrtadd) {
        // SQL query to insert address data
        $sql = "INSERT INTO pataddresses (address_patientid, address_region, address_province, address_municipality, address_barangay, address_prkstrtadd)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
    
        try {
            // Execute the query and retrieve the last inserted address ID
            $stmt->execute([$patientid, $region, $province, $municipality, $barangay, $prkstrtadd]);
            $address_id = $this->db->lastInsertId();
            return ['status' => 'success', 'message' => 'Address inserted successfully.', 'address_id' => $address_id];
    
        } catch (PDOException $e) {
            error_log("Error inserting address: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error inserting address. Please try again later.'];
        }
    }
    
    // Insert a new emergency contact associated with a patient into the database.
    public function insertEmergencyContact($patientid, $conname, $relationship, $emergency_connum) {
        $sql = "INSERT INTO patemergencycontacts (emcon_patientid, emcon_conname, emcon_relationship, emcon_connum)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
    
        try {
            // Execute the query and retrieve the last inserted emergency contact ID
            $stmt->execute([$patientid, $conname, $relationship, $emergency_connum]);
            $contact_id = $this->db->lastInsertId();
            return ['status' => 'success', 'message' => 'Emergency contact inserted successfully.', 'contact_id' => $contact_id];
    
        } catch (PDOException $e) {
            error_log("Error inserting emergency contact: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error inserting emergency contact. Please try again later.'];
        }
    }
    
    // Add a new student patient and associate them with additional information like program, major, and address.
    public function addStudentPatient($admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, $password, $status, $code, $idnum, $program, $major, $year, $section, $region, $province, $municipality, $barangay, $prkstrtadd, $conname, $relationship, $emergency_connum) {
        // Insert patient information
        $insertPatientResponse = $this->insertPatient($admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, $password, $status, $code);
        
        if ($insertPatientResponse['status'] !== 'success') {
            return $insertPatientResponse; 
        }

        // Get patient ID from the inserted patient record
        $patientid = $insertPatientResponse['patient_id']; 
    
        // Insert student-specific information
        $insertStudentResponse = $this->insertStudent($idnum, $patientid, $program, $major, $year, $section);
        if ($insertStudentResponse['status'] !== 'success') {
            return $insertStudentResponse; 
        }
    
        // Insert address information for the patient
        $insertAddressResponse = $this->insertAddress($patientid, $region, $province, $municipality, $barangay, $prkstrtadd);
        if ($insertAddressResponse['status'] !== 'success') {
            return $insertAddressResponse; 
        }
    
        // Insert emergency contact information for the patient
        $insertEmergencyContactResponse = $this->insertEmergencyContact($patientid, $conname, $relationship, $emergency_connum);
        if ($insertEmergencyContactResponse['status'] !== 'success') {
            return $insertEmergencyContactResponse; 
        }
    
       // Return a success message with IDs of associated records
        return [
            'status' => 'success',
            'message' => 'Student patient added successfully.',
            'patient_id' => $patientid,
            'student_id' => $insertStudentResponse['student_id'],
            'address_id' => $insertAddressResponse['address_id'],
            'contact_id' => $insertEmergencyContactResponse['contact_id']
        ];
    }
    
    // Add a new faculty patient and associate them with faculty-specific information, address, and emergency contact.
    public function addFacultyPatient(
        $admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, 
        $password, $status, $code, $idnum, $college, $depart, $role,
        $region, $province, $municipality, $barangay, $prkstrtadd, $conname, 
        $relationship, $emergency_connum
    ) {
        // Insert patient information
        $insertPatientResponse = $this->insertPatient($admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, $password, $status, $code);
        
       
        if ($insertPatientResponse['status'] !== 'success') {
            return $insertPatientResponse; 
        }
        // Get patient ID from the inserted patient record
        $patientid = $insertPatientResponse['patient_id']; 

        // Insert faculty-specific information
        $insertFacultyResponse = $this->insertFaculty($patientid, $idnum, $college, $depart, $role);
        if ($insertFacultyResponse['status'] !== 'success') {
            return $insertFacultyResponse; 
        }

        // Insert address information for the patient
        $insertAddressResponse = $this->insertAddress($patientid, $region, $province, $municipality, $barangay, $prkstrtadd);
        if ($insertAddressResponse['status'] !== 'success') {
            return $insertAddressResponse; 
        }

        // Insert emergency contact information for the patient
        $insertEmergencyContactResponse = $this->insertEmergencyContact($patientid, $conname, $relationship, $emergency_connum);
        if ($insertEmergencyContactResponse['status'] !== 'success') {
            return $insertEmergencyContactResponse; 
        }

        // Return a success message with IDs of associated records
        return [
            'status' => 'success',
            'message' => 'Faculty patient added successfully.',
            'patient_id' => $patientid,
            'faculty_id' => $insertFacultyResponse['faculty_id'],
            'address_id' => $insertAddressResponse['address_id'],
            'contact_id' => $insertEmergencyContactResponse['contact_id']
        ];
    }

    // Add a new staff patient and associate them with office details, role, address, and emergency contact.
    public function addStaffPatient(
        $admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, 
        $password, $status, $code, $idnum, $office, $role,
        $region, $province, $municipality, $barangay, $prkstrtadd, $conname, 
        $relationship, $emergency_connum
    ) {
        // Insert patient information
        $insertPatientResponse = $this->insertPatient($admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, $password, $status, $code);
        
        if ($insertPatientResponse['status'] !== 'success') {
            return $insertPatientResponse; 
        }
        
        // Get patient ID from the inserted patient record
        $patientid = $insertPatientResponse['patient_id']; 

        // Insert staff-specific information
        $insertStaffResponse = $this->insertStaff($patientid, $idnum, $office, $role);
        if ($insertStaffResponse['status'] !== 'success') {
            return $insertStaffResponse; 
        }

        // Insert address information for the patient
        $insertAddressResponse = $this->insertAddress($patientid, $region, $province, $municipality, $barangay, $prkstrtadd);
        if ($insertAddressResponse['status'] !== 'success') {
            return $insertAddressResponse; 
        }
 
        // Insert emergency contact information for the patient
        $insertEmergencyContactResponse = $this->insertEmergencyContact($patientid, $conname, $relationship, $emergency_connum);
        if ($insertEmergencyContactResponse['status'] !== 'success') {
            return $insertEmergencyContactResponse;
        }

        // Return a success message with IDs of associated records
        return [
            'status' => 'success',
            'message' => 'Staff patient added successfully.',
            'patient_id' => $patientid,
            'staff_id' => $insertStaffResponse['staff_id'],
            'address_id' => $insertAddressResponse['address_id'],
            'contact_id' => $insertEmergencyContactResponse['contact_id']
        ];
    }

    // Method to add a new extension patient
    public function addExtenPatient(
        $admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, 
        $password, $status, $code, $idnum, $role,
        $region, $province, $municipality, $barangay, $prkstrtadd, $conname, 
        $relationship, $emergency_connum
    ) {
        // Insert patient details
        $insertPatientResponse = $this->insertPatient($admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, $password, $status, $code);
        
        // If patient insertion fails, return the error response
        if ($insertPatientResponse['status'] !== 'success') {
            return $insertPatientResponse; 
        }
        // Get the inserted patient ID
        $patientid = $insertPatientResponse['patient_id']; 

        // Insert extension-specific data
        $insertExtenResponse = $this->insertExtension($idnum, $patientid, $role);
        if ($insertExtenResponse['status'] !== 'success') {
            return $insertExtenResponse; 
        }

        // Insert address information
        $insertAddressResponse = $this->insertAddress($patientid, $region, $province, $municipality, $barangay, $prkstrtadd);
        if ($insertAddressResponse['status'] !== 'success') {
            return $insertAddressResponse; 
        }

        // Insert emergency contact information
        $insertEmergencyContactResponse = $this->insertEmergencyContact($patientid, $conname, $relationship, $emergency_connum);
        if ($insertEmergencyContactResponse['status'] !== 'success') {
            return $insertEmergencyContactResponse; 
        }
        // If all insertions succeed, return a success response with the relevant IDs
        return [
            'status' => 'success',
            'message' => 'Extension patient added successfully.',
            'patient_id' => $patientid,
            'extension_id' => $insertExtenResponse['exten_id'],
            'address_id' => $insertAddressResponse['address_id'],
            'contact_id' => $insertEmergencyContactResponse['contact_id']
        ];
    
    }

    // Method to update an existing patient's basic information
    public function updatePatient($admin_id, $patient_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $newPassword, $status) {
        try {
            // Set admin ID for logging
            $setAdminIdQuery = "SET @admin_id = :admin_id";
            $setStmt = $this->db->prepare($setAdminIdQuery);
            $setStmt->bindValue(':admin_id', $admin_id);
            $setStmt->execute();
    
            // Update patient details
            $sql = "UPDATE patients 
                    SET patient_lname = ?, patient_fname = ?, patient_mname = ?, patient_dob = ?, 
                        patient_email = ?, patient_connum = ?, patient_sex = ?, 
                        patient_password = ?, patient_status = ?   
                    WHERE patient_id = ?";
            $stmt = $this->db->prepare($sql);
    
            $params = [
                $lname,
                $fname,
                $mname === '' ? null : $mname,
                $dob,
                $email,
                $connum,
                $sex,
                $newPassword,
                $status,
                $patient_id
            ];
    
            $stmt->execute($params);
    
            // Return success if the update is successful
            return ['status' => 'success', 'message' => 'Patient updated successfully.'];
    
        } catch (PDOException $e) {
            // Catch any errors and log them
            error_log("Error updating patient: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Error updating patient. Please try again later.'
            ];
        }
    }
    
    // Method to update student-specific data
    public function updateStudent($patientid, $idnum, $program, $major, $year, $section) {
        $sql = "UPDATE patstudents 
                SET student_idnum = ?, student_program = ?, student_major = ?, student_year = ?, student_section = ?
                WHERE student_patientid = ?";
        $stmt = $this->db->prepare($sql);
    
        try {
            // Update student-specific data
            $stmt->execute([
                            $idnum,
                            $program, 
                            $major,  
                            $year, 
                            $section, 
                            $patientid]);
    
            return ['status' => 'success', 'message' => 'Student updated successfully.'];
    
        } catch (PDOException $e) {
            // Log any errors and return failure message
            error_log("Error updating student: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error updating student. Please try again later.'];
        }
    }

    // Method to update faculty-specific data
    public function updateFaculty($patientid, $idnum, $college, $depart, $role) {    
        $sql = "UPDATE patfaculties 
                SET faculty_idnum = ?, faculty_college = ?, faculty_depart = ?, faculty_role = ?
                WHERE faculty_patientid = ?";
        $stmt = $this->db->prepare($sql);
    
        try {
            // Update faculty-specific data
            $stmt->execute([$idnum, $college, $depart, $role, $patientid]);
    
            return [
                'status' => 'success', 
                'message' => 'Faculty updated successfully.'
            ];
        } catch (PDOException $e) {
            // Log any errors and return failure message
            error_log("Error updating faculty: " . $e->getMessage());
            return [
                'status' => 'error', 
                'message' => 'Error updating faculty. Please try again later.'
            ];
        }
    }

    // Method to update staff-specific data
    public function updateStaff($patientid, $idnum, $office, $role) {
        $sql = "UPDATE patstaffs 
                SET staff_idnum = ?, staff_office = ?, staff_role = ?
                WHERE staff_patientid = ?";
        $stmt = $this->db->prepare($sql);
    
        try {
            // Update staff-specific data
            $stmt->execute([$idnum, $office, $role, $patientid]);
            return ['status' => 'success', 'message' => 'Staff updated successfully.'];
    
        } catch (PDOException $e) {
            // Log any errors and return failure message
            error_log("Error updating staff: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error updating staff. Please try again later.'];
        }
    }

    // Method to update extension-specific data
    public function updateExtension($patientid, $idnum, $role) {
        $sql = "UPDATE patextensions 
                SET exten_idnum = ?, exten_role = ?
                WHERE exten_patientid = ?";
        $stmt = $this->db->prepare($sql);
    
        try {
            // Update extension-specific data
            $stmt->execute([$idnum, $role, $patientid]);
            return ['status' => 'success', 'message' => 'Extension updated successfully.'];
        } catch (PDOException $e) {
            // Log any errors and return failure message
            error_log("Error updating extension: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error updating extension. Please try again later.'];
        }
    }

    // Method to update address information
    public function updateAddress($patientid, $region, $province, $municipality, $barangay, $prkstrtadd) {
        $sql = "UPDATE pataddresses 
                SET address_region = ?, address_province = ?, address_municipality = ?, address_barangay = ?, address_prkstrtadd = ?
                WHERE address_patientid = ?";
        $stmt = $this->db->prepare($sql);
    
        try {
            // Update address information
            $stmt->execute([$region, $province, $municipality, $barangay, $prkstrtadd, $patientid]);
            return ['status' => 'success', 'message' => 'Address updated successfully.'];
    
        } catch (PDOException $e) {
            // Log any errors and return failure message
            error_log("Error updating address: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error updating address. Please try again later.'];
        }
    }

    // Method to update emergency contact information
    public function updateEmergencyContact($patientid, $conname, $relationship, $emergency_connum) {
        $sql = "UPDATE patemergencycontacts 
                SET emcon_conname = ?, emcon_relationship = ?, emcon_connum = ?
                WHERE emcon_patientid = ?";
        $stmt = $this->db->prepare($sql);
    
        try {
            // Update emergency contact information
            $stmt->execute([$conname, $relationship, $emergency_connum, $patientid]);
            return ['status' => 'success', 'message' => 'Emergency contact updated successfully.'];
    
        } catch (PDOException $e) {
            // Log any errors and return failure message
            error_log("Error updating emergency contact: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error updating emergency contact. Please try again later.'];
        }
    }

    public function updatePatientProfileImage($patient_id, $profile) {
        try {
            $sql = "UPDATE patients 
                    SET patient_profile = ?
                    WHERE patient_id = ?";
            
            $stmt = $this->db->prepare($sql);
            
            // Prepare the values
            $params = [
                $profile,
                $patient_id
            ];
            
            $stmt->execute($params);
            
            return ['status' => 'success', 'message' => 'Profile image updated successfully.'];
            
        } catch (PDOException $e) {
            error_log("Error updating profile image: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Error updating profile image. Please try again later.'
            ];
        }
    }
    
    //Update the details of a student patient.
    public function updateStudentPatient(
        $admin_id, $patientId, $lname, $fname, $mname, $dob, $email, $connum, $sex,   
        $password, $status, $idnum, $program, $major, $year, $section, 
        $region, $province, $municipality, $barangay, $prkstrtadd, $conname, 
        $relationship, $emergency_connum
    ) {
        $updatePatientResponse = $this->updatePatient($admin_id, $patientId, $lname, $fname, $mname, $dob, $email, $connum, $sex, $password, $status);
        
        if ($updatePatientResponse['status'] !== 'success') {
            return $updatePatientResponse; 
        }
    
        $updateStudentResponse = $this->updateStudent($patientId, $idnum, $program, $major, $year, $section);
        if ($updateStudentResponse['status'] !== 'success') {
            return $updateStudentResponse; 
        }
    
        $updateAddressResponse = $this->updateAddress($patientId, $region, $province, $municipality, $barangay, $prkstrtadd);
        if ($updateAddressResponse['status'] !== 'success') {
            return $updateAddressResponse; 
        }
    
        $updateEmergencyContactResponse = $this->updateEmergencyContact($patientId, $conname, $relationship, $emergency_connum);
        if ($updateEmergencyContactResponse['status'] !== 'success') {
            return $updateEmergencyContactResponse; 
        }
    
        return [
            'status' => 'success',
            'message' => 'Student patient updated successfully.',
            'patient_id' => $patientId,
            'student_id' => $updateStudentResponse['student_id'],
            'address_id' => $updateAddressResponse['address_id'],
            'contact_id' => $updateEmergencyContactResponse['contact_id']
        ];
    }
    
    //Update the details of a faculty patient.
    public function updateFacultyPatient(
        $admin_id, $patientId, $lname, $fname, $mname, $dob, $email, $connum, $sex, 
        $password, $status, $idnum, $college, $depart, $role,
        $region, $province, $municipality, $barangay, $prkstrtadd, $conname, 
        $relationship, $emergency_connum
    ) {
        $updatePatientResponse = $this->updatePatient($admin_id, $patientId, $lname, $fname, $mname, $dob, $email, $connum, $sex, $password, $status);
        
        if ($updatePatientResponse['status'] !== 'success') {
            return $updatePatientResponse; 
        }
    
        $updateFacultyResponse = $this->updateFaculty($patientId, $idnum, $college, $depart, $role);
        if ($updateFacultyResponse['status'] !== 'success') {
            return $updateFacultyResponse; 
        }
    
        $updateAddressResponse = $this->updateAddress($patientId, $region, $province, $municipality, $barangay, $prkstrtadd);
        if ($updateAddressResponse['status'] !== 'success') {
            return $updateAddressResponse; 
        }
    
        $updateEmergencyContactResponse = $this->updateEmergencyContact($patientId, $conname, $relationship, $emergency_connum);
        if ($updateEmergencyContactResponse['status'] !== 'success') {
            return $updateEmergencyContactResponse; 
        }
    
        return [
            'status' => 'success',
            'message' => 'Faculty patient updated successfully.',
            'patient_id' => $patientId,
            'faculty_id' => $updateFacultyResponse['faculty_id'],
            'address_id' => $updateAddressResponse['address_id'],
            'contact_id' => $updateEmergencyContactResponse['contact_id']
        ];
    }
    
    //Update the details of a staff patient.
    public function updateStaffPatient(
        $admin_id,  $patientId, $lname, $fname, $mname, $dob, $email, $connum, $sex,
        $password, $status, $idnum, $office, $role,
        $region, $province, $municipality, $barangay, $prkstrtadd, $conname, 
        $relationship, $emergency_connum
    ) {
        $updatePatientResponse = $this->updatePatient($admin_id, $patientId, $lname, $fname, $mname, $dob, $email, $connum, $sex, $password, $status);
        
        if ($updatePatientResponse['status'] !== 'success') {
            return $updatePatientResponse; 
        }
    
        $updateStaffResponse = $this->updateStaff($patientId, $idnum, $office, $role);
        if ($updateStaffResponse['status'] !== 'success') {
            return $updateStaffResponse; 
        }
    
        $updateAddressResponse = $this->updateAddress($patientId, $region, $province, $municipality, $barangay, $prkstrtadd);
        if ($updateAddressResponse['status'] !== 'success') {
            return $updateAddressResponse; 
        }
    
        $updateEmergencyContactResponse = $this->updateEmergencyContact($patientId, $conname, $relationship, $emergency_connum);
        if ($updateEmergencyContactResponse['status'] !== 'success') {
            return $updateEmergencyContactResponse; 
        }
    
        return [
            'status' => 'success',
            'message' => 'Staff patient updated successfully.',
            'patient_id' => $patientId,
            'staff_id' => $updateStaffResponse['staff_id'],
            'address_id' => $updateAddressResponse['address_id'],
            'contact_id' => $updateEmergencyContactResponse['contact_id']
        ];
    }
    
    //Update the details of an extension patient.
    public function updateExtenPatient(
        $admin_id,  $patientId, $lname, $fname, $mname, $dob, $email, $connum, $sex,
        $password, $status, $idnum, $role,
        $region, $province, $municipality, $barangay, $prkstrtadd, $conname, 
        $relationship, $emergency_connum
    ) {
        $updatePatientResponse = $this->updatePatient($admin_id,  $patientId, $lname, $fname, $mname, $dob, $email, $connum, $sex, $password, $status);
        
        if ($updatePatientResponse['status'] !== 'success') {
            return $updatePatientResponse; 
        }
    
        $updateExtenResponse = $this->updateExtension($patientId, $idnum, $role);
        if ($updateExtenResponse['status'] !== 'success') {
            return $updateExtenResponse; 
        }
    
        $updateAddressResponse = $this->updateAddress($patientId, $region, $province, $municipality, $barangay, $prkstrtadd);
        if ($updateAddressResponse['status'] !== 'success') {
            return $updateAddressResponse; 
        }
    
        $updateEmergencyContactResponse = $this->updateEmergencyContact($patientId, $conname, $relationship, $emergency_connum);
        if ($updateEmergencyContactResponse['status'] !== 'success') {
            return $updateEmergencyContactResponse; 
        }
    
        return [
            'status' => 'success',
            'message' => 'Extension patient updated successfully.',
            'patient_id' => $patientId,
            'extension_id' => $updateExtenResponse['exten_id'],
            'address_id' => $updateAddressResponse['address_id'],
            'contact_id' => $updateEmergencyContactResponse['contact_id']
        ];
    }
    

    //Retrieve all patients.
    public function getAllPatients() {
        return $this->patients->getAllNodes();
    }

    //Retrieve all students.
    public function getAllStudents() {
        return $this->students->getAllNodes();
    }
    
    //Retrieve all faculty members.
    public function getAllFaculties() {
        return $this->faculties->getAllNodes();
    }

    //Retrieve all staff members.
    public function getAllStaffs() {
        return $this->staffs->getAllNodes();
    }

    //Retrieve all extensions.
    public function getAllExtensions() {
        return $this->extensions->getAllNodes();
    }

    //Retrieve all addresses.
    public function getAllAddresses() {
        return $this->addresses->getAllNodes();
    }

    //Retrieve all emergency contacts.
    public function getAllEmergencyCon() {
        return $this->emergencycon->getAllNodes();
    }

    //Retrieve all patients and their information in a tabular format.
    public function getAllPatientsTable() {
        $patients = $this->patients->getAllNodes();
        $students = $this->students->getAllNodes();
        $faculties = $this->faculties->getAllNodes();
        $staffs = $this->staffs->getAllNodes();
        $extensions = $this->extensions->getAllNodes();
    
        $combinedData = [];
    
        // Helper function to combine data for a specific patient type
        $combineRows = function($personType, $dataArray) use (&$combinedData) {
            foreach ($dataArray as $entry) {
                if (isset($entry->patient_id, $entry->patient_lname, $entry->patient_fname, $entry->patient_email, $entry->patient_sex, $entry->patient_status)) {
                    
                    // Construct dynamic idnum field name based on the personType
                    $idnumField = strtolower($personType) . '_idnum';
                    $idnum = property_exists($entry, $idnumField) ? $entry->$idnumField : null;
    
                    // Create a unified entry format
                    $combinedEntry = (object) [
                        'id' => $entry->patient_id,
                        'name' => $entry->patient_lname . ' ' . $entry->patient_fname,
                        'email' => $entry->patient_email,
                        'sex' => $entry->patient_sex,
                        'type' => $personType,
                        'status' => $entry->patient_status,
                        'idnum' => $idnum // Dynamically added idnum
                    ];
    
                    $combinedData[] = $combinedEntry;
                } else {
                    error_log("Missing required fields in entry: " . json_encode($entry));
                }
            }
        };
    
        // Combine data for each type of patient
        $combineRows('Patient', $patients);
        $combineRows('Student', $students);
        $combineRows('Faculty', $faculties);
        $combineRows('Staff', $staffs);
        $combineRows('Extension Worker', $extensions);
    
        return $combinedData;
    }
    
    
//Retrieve detailed information for a student patient.
public function getStudentData($patient_id) {
    $query = "
        SELECT 
            p.*, 
            s.student_idnum, 
            s.student_program, 
            s.student_major, 
            s.student_year, 
            s.student_section, 
            a.address_region, 
            a.address_province, 
            a.address_municipality, 
            a.address_barangay, 
            a.address_prkstrtadd, 
            ec.emcon_conname, 
            ec.emcon_relationship, 
            ec.emcon_connum
        FROM 
            patients p
        LEFT JOIN 
            patstudents s ON p.patient_id = s.student_patientid
        LEFT JOIN 
            pataddresses a ON p.patient_id = a.address_patientid
        LEFT JOIN 
            patemergencycontacts ec ON p.patient_id = ec.emcon_patientid
        WHERE 
            p.patient_id = :patient_id AND 
            p.patient_patienttype = 'Student'
    ";
    
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':patient_id', $patient_id);
    $stmt->execute();

    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return [
        'patient' => $data, 
        'student' => [
            'student_idnum' => $data['student_idnum'],
            'student_program' => $data['student_program'],
            'student_major' => $data['student_major'],
            'student_year' => $data['student_year'],
            'student_section' => $data['student_section']
        ],
        'address' => [
            'address_region' => $data['address_region'],
            'address_province' => $data['address_province'],
            'address_municipality' => $data['address_municipality'],
            'address_barangay' => $data['address_barangay'],
            'address_prkstrtadd' => $data['address_prkstrtadd']
        ],
        'emergencyContact' => [
            'emcon_conname' => $data['emcon_conname'],
            'emcon_relationship' => $data['emcon_relationship'],
            'emcon_connum' => $data['emcon_connum']
        ]
    ];
}

//Retrieve detailed information for a faculty patient.
public function getFacultyData($patient_id) {
    $query = "
        SELECT 
            p.*, 
            f.faculty_idnum, 
            f.faculty_college, 
            f.faculty_depart, 
            f.faculty_role, 
            a.address_region, 
            a.address_province, 
            a.address_municipality, 
            a.address_barangay, 
            a.address_prkstrtadd, 
            ec.emcon_conname, 
            ec.emcon_relationship, 
            ec.emcon_connum
        FROM 
            patients p
        LEFT JOIN 
            patfaculties f ON p.patient_id = f.faculty_patientid
        LEFT JOIN 
            pataddresses a ON p.patient_id = a.address_patientid
        LEFT JOIN 
            patemergencycontacts ec ON p.patient_id = ec.emcon_patientid
        WHERE 
            p.patient_id = :patient_id AND 
            p.patient_patienttype = 'Faculty'
    ";
    
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':patient_id', $patient_id);
    $stmt->execute();

    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return [
        'patient' => $data, // Patient data
        'faculty' => [ 
            'faculty_idnum' => $data['faculty_idnum'],
            'faculty_college' => $data['faculty_college'],
            'faculty_department' => $data['faculty_depart'],
            'faculty_role' => $data['faculty_role']
        ],
        'address' => [
            'address_region' => $data['address_region'],
            'address_province' => $data['address_province'],
            'address_municipality' => $data['address_municipality'],
            'address_barangay' => $data['address_barangay'],
            'address_prkstrtadd' => $data['address_prkstrtadd']
        ],
        'emergencyContact' => [
            'emcon_conname' => $data['emcon_conname'],
            'emcon_relationship' => $data['emcon_relationship'],
            'emcon_connum' => $data['emcon_connum']
        ]
    ];
}

//Retrieve detailed information for a staff patient.
public function getStaffData($patient_id) {
    $query = "
        SELECT 
            p.*, 
            s.staff_idnum, 
            s.staff_office, 
            s.staff_role,  
            a.address_region, 
            a.address_province, 
            a.address_municipality,  
            a.address_barangay, 
            a.address_prkstrtadd, 
            ec.emcon_conname, 
            ec.emcon_relationship, 
            ec.emcon_connum
        FROM 
            patients p
        LEFT JOIN 
            patstaffs s ON p.patient_id = s.staff_patientid
        LEFT JOIN 
            pataddresses a ON p.patient_id = a.address_patientid
        LEFT JOIN 
            patemergencycontacts ec ON p.patient_id = ec.emcon_patientid
        WHERE 
            p.patient_id = :patient_id AND 
            p.patient_patienttype = 'Staff'
    ";
    
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':patient_id', $patient_id);
    $stmt->execute();

    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return [
        'patient' => $data, 
        'staff' => [
            'staff_idnum' => $data['staff_idnum'],
            'staff_office' => $data['staff_office'],
            'staff_role' => $data['staff_role']
        ],
        'address' => [
            'address_region' => $data['address_region'],
            'address_province' => $data['address_province'],
            'address_municipality' => $data['address_municipality'],
            'address_barangay' => $data['address_barangay'],
            'address_prkstrtadd' => $data['address_prkstrtadd']
        ],
        'emergencyContact' => [
            'emcon_conname' => $data['emcon_conname'],
            'emcon_relationship' => $data['emcon_relationship'],
            'emcon_connum' => $data['emcon_connum']
        ]
    ];
}

//Retrieve detailed information for an extension worker patient.
public function getExtensionData($patient_id) {
    $query = "
        SELECT 
            p.*, 
            e.exten_idnum, 
            e.exten_role, 
            a.address_region, 
            a.address_province, 
            a.address_municipality, 
            a.address_barangay, 
            a.address_prkstrtadd, 
            ec.emcon_conname, 
            ec.emcon_relationship, 
            ec.emcon_connum
        FROM 
            patients p
        LEFT JOIN 
            patextensions e ON p.patient_id = e.exten_patientid
        LEFT JOIN 
            pataddresses a ON p.patient_id = a.address_patientid
        LEFT JOIN 
            patemergencycontacts ec ON p.patient_id = ec.emcon_patientid
        WHERE 
            p.patient_id = :patient_id AND 
            p.patient_patienttype = 'Extension'
    ";

    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':patient_id', $patient_id);
    $stmt->execute();

    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return [
        'patient' => $data, 
        'extension' => [ 
            'exten_idnum' => $data['exten_idnum'], 
            'exten_role' => $data['exten_role'],   
        ],
        'address' => [
            'address_region' => $data['address_region'],
            'address_province' => $data['address_province'],
            'address_municipality' => $data['address_municipality'],
            'address_barangay' => $data['address_barangay'],
            'address_prkstrtadd' => $data['address_prkstrtadd']
        ],
        'emergencyContact' => [
            'emcon_conname' => $data['emcon_conname'],
            'emcon_relationship' => $data['emcon_relationship'],
            'emcon_connum' => $data['emcon_connum']
        ]
    ];
}



    
    
     

    
    
}







?>