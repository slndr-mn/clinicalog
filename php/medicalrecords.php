<?php
//Medical records class
class MedicalRecords {
    public $medicalrec_id;
    public $medicalrec_patientid;
    public $medicalrec_filename;
    public $medicalrec_file;
    public $medicalrec_comment;
    public $medicalrec_dateadded;
    public $medicalrec_timeadded;

    // Constructor to initialize a medical record with all necessary properties.
    public function __construct($id, $patientid, $filename, $file, $comment, $dateadded, $timeadded) {
        $this->medicalrec_id = $id;
        $this->medicalrec_patientid = $patientid;
        $this->medicalrec_filename = $filename;
        $this->medicalrec_file = $file;
        $this->medicalrec_comment = $comment;
        $this->medicalrec_dateadded = $dateadded;
        $this->medicalrec_timeadded = $timeadded;
    }
}

class MedRecNode {
    public $item;
    public $next;

    // Constructor to initialize the node with a medical record.
    public function __construct($item) {
        $this->item = $item;
        $this->next = null;
    }
} 

class MedRecordsList {
    public $head;

    // Initializes an empty list.
    public function __construct() {
        $this->head = null;
    }

    // Adds a new record to the list.
    public function add($item) {
        $newNode = new MedRecNode($item);
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

    // Returns all records from the list.
    public function getAllNodes() {
        $nodes = [];
        $current = $this->head;
        while ($current !== null) {
            $nodes[] = $current->item;
            $current = $current->next;
        }
        return $nodes;
    }

    // Checks if a record already exists for the patient and filename.
    public function MedRecExists($patientid, $filename) {
        $current = $this->head;
        while ($current !== null) {
            if ($current->item->medicalrec_patientid === $patientid && 
                strcasecmp($current->item->medicalrec_filename, $filename) === 0) {
                return true;
            }
            $current = $current->next;
        }
        return false;
    }

    // Checks for duplicate filenames for a patient.
    public function getDuplicateFilenames($patientid, $filenames) {
        $current = $this->head;
        $duplicateFilenames = [];
    
        while ($current !== null) {
            foreach ($filenames as $filename) {
                if ($current->item->medicalrec_patientid === $patientid && 
                    strcasecmp($current->item->medicalrec_filename, $filename) === 0) {
                    $duplicateFilenames[] = $filename;
                }
            }
            $current = $current->next;
        }
            return $duplicateFilenames;
    }

    // Checks if a specific filename already exists for a patient.
    public function isDuplicateFilename($patientid, $filename) {
        $current = $this->head;
        while ($current !== null) {
            if ($current->item->medicalrec_patientid === $patientid && 
                    strcasecmp($current->item->medicalrec_filename, $filename) === 0) { {
                return true;  
            }
            $current = $current->next;
        }
        return false; 
        }
    }   
    
    
    // Finds a record by its ID.
    public function findMedicalRecordById($medicalrec_id) {
        $current = $this->head;
        while ($current !== null) {
            if ($current->item->medicalrec_id === $medicalrec_id) {
                return $current->item; 
            }
            $current = $current->next; 
        }
        return null; 
    }
    
    
}

class MedRecManager {
    private $db;
    public $medicalrecs;

    // Initializes the manager and loads records from DB.
    public function __construct($db) {
        $this->db = $db; 
        $this->medicalrecs = new MedRecordsList();
        $this->loadMedicalRecords();
    }
    // Loads medical records from the database into the list.
    private function loadMedicalRecords() {
        $sql = "SELECT * FROM medicalrec"; 
        $stmt = $this->db->query($sql); 
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $medicalrec = new MedicalRecords(
                $row['medicalrec_id'], $row['medicalrec_patientid'], $row['medicalrec_filename'], 
                $row['medicalrec_file'], $row['medicalrec_comment'], $row['medicalrec_dateadded'], 
                $row['medicalrec_timeadded']
            );        
            $this->medicalrecs->add($medicalrec); 
        }
    }

    // Checks for duplicate filenames for a given patient.
    public function getDuplicateFilenames($patientid, $filenames) {
        $duplicateFilenames = [];
    
        if (!is_array($filenames)) {
            $filenames = [$filenames]; 
        }
    
        foreach ($filenames as $filename) {
            if ($this->medicalrecs->MedRecExists($patientid, $filename)) {
                $duplicateFilenames[] = $filename;
            }
        }
    
        return $duplicateFilenames;
    }
    

    
    // Inserts medical records into DB and updates the list.
    public function insertMedicalRecord($admin_id, $patientid, $filenames, $files, $comment, $dateadded, $timeadded) {
        try {    
            $setAdminIdQuery = "SET @admin_id = :admin_id";
            $setStmt = $this->db->prepare($setAdminIdQuery);
            $setStmt->bindValue(':admin_id', $admin_id);
            $setStmt->execute();
            
            $sql = "INSERT INTO medicalrec (medicalrec_patientid, medicalrec_filename, medicalrec_file, medicalrec_comment, medicalrec_dateadded, medicalrec_timeadded) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);

            foreach ($filenames as $index => $filename) {
                $file = $files[$index]; 
                
                if ($stmt->execute([$patientid, $filename, $file, $comment, $dateadded, $timeadded])) {
                    $medicalrec_id = $this->db->lastInsertId();
                    $newRecord = new MedicalRecords($medicalrec_id, $patientid, $filename, $file, $comment, $dateadded, $timeadded);
                    $this->medicalrecs->add($newRecord);
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'Failed to insert one or more medical records.'
                    ];
                }
            }

            return [
                'status' => 'success',
                'message' => 'All medical records inserted successfully.'
            ];
    
        } catch (PDOException $e) {
            return [ 
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    // Inserts medical records for a patient without admin ID.
    public function insertMedicalRecordbyPatient($patientid, $filenames, $files, $comment, $dateadded, $timeadded) {
        try {    

            
            $sql = "INSERT INTO medicalrec (medicalrec_patientid, medicalrec_filename, medicalrec_file, medicalrec_comment, medicalrec_dateadded, medicalrec_timeadded) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);

            foreach ($filenames as $index => $filename) {
                $file = $files[$index]; 
                
                if ($stmt->execute([$patientid, $filename, $file, $comment, $dateadded, $timeadded])) {
                    $medicalrec_id = $this->db->lastInsertId();
                    $newRecord = new MedicalRecords($medicalrec_id, $patientid, $filename, $file, $comment, $dateadded, $timeadded);
                    $this->medicalrecs->add($newRecord);
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'Failed to insert one or more medical records.'
                    ];
                }
            }

            return [
                'status' => 'success',
                'message' => 'All medical records inserted successfully.'
            ];
    
        } catch (PDOException $e) {
            return [ 
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

     // Updates a medical record for a specific patient.
    public function updateMedicalRecordbyPatient($medicalrec_id, $patientid, $filename, $comment) {
        try {

            // Check if a record with the same patient and filename already exists.
            if ($this->medicalrecs->MedRecExists($patientid, $filename)) {
                $existingRecord = $this->medicalrecs->findMedicalRecordById($medicalrec_id);
                // Ensure the update doesn't overwrite an existing record with the same patient ID and filename.
                if ($existingRecord && 
                    ($existingRecord->medicalrec_patientid !== $patientid || 
                     strcasecmp($existingRecord->medicalrec_filename, $filename) !== 0)) {
                    return [
                        'status' => 'error',
                        'message' => 'A medical record with this patient ID and filename already exists.'
                    ];
                }
            }

            // Prepare SQL query to update the record.
            $sql = "UPDATE medicalrec 
                    SET medicalrec_filename = ?,  medicalrec_comment = ?
                    WHERE medicalrec_id = ? AND medicalrec_patientid = ?";
            $stmt = $this->db->prepare($sql);
    
            // Execute the update query and return success or failure message.
            if ($stmt->execute([$filename, $comment, $medicalrec_id, $patientid ])) {
                return [
                    'status' => 'success',
                    'message' => 'Medical record updated successfully.',
                    'medicalrec_id' => $medicalrec_id
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to update medical record.'
                ];
            }
    
        } catch (PDOException $e) {
            return [
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    
    // Updates a medical record with admin ID for logging actions.
    public function updateMedicalRecord($admin_id, $medicalrec_id, $patientid, $filename, $comment) {
        try {

            // Check if a record with the same patient and filename already exists.
            if ($this->medicalrecs->MedRecExists($patientid, $filename)) {
                $existingRecord = $this->medicalrecs->findMedicalRecordById($medicalrec_id);
                // Ensure the update doesn't overwrite an existing record with the same patient ID and filename.
                if ($existingRecord && 
                    ($existingRecord->medicalrec_patientid !== $patientid || 
                     strcasecmp($existingRecord->medicalrec_filename, $filename) !== 0)) {
                    return [
                        'status' => 'error',
                        'message' => 'A medical record with this patient ID and filename already exists.'
                    ];
                }
            }
    
            $setAdminIdQuery = "SET @admin_id = :admin_id";
            $setStmt = $this->db->prepare($setAdminIdQuery);
            $setStmt->bindValue(':admin_id', $admin_id);
            $setStmt->execute();

            $sql = "UPDATE medicalrec 
                    SET medicalrec_filename = ?,  medicalrec_comment = ?
                    WHERE medicalrec_id = ? AND medicalrec_patientid = ?";
            $stmt = $this->db->prepare($sql);
    
            if ($stmt->execute([$filename, $comment, $medicalrec_id, $patientid ])) {
                return [
                    'status' => 'success',
                    'message' => 'Medical record updated successfully.',
                    'medicalrec_id' => $medicalrec_id
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to update medical record.'
                ];
            }
    
        } catch (PDOException $e) {
            return [
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    // Deletes a medical record from the database and logs the action.
    public function deleteMedicalRecord($admin_id, $medicalrec_id) {
        try {
            // Start a database transaction.
            $this->db->beginTransaction(); 
    
            // Prepare SQL query to delete the record.
            $sql = "DELETE FROM medicalrec WHERE medicalrec_id = ?";
            $stmt = $this->db->prepare($sql);
    
            // If the deletion is successful, log the action and commit the transaction.
            if ($stmt->execute([$medicalrec_id])) {
                // Fetch the full name of the patient.
                $patientQuery = "SELECT CONCAT(patient_fname, ' ', patient_lname, ' ', patient_mname) AS full_name 
                                 FROM patients 
                                 WHERE patient_id = (
                                     SELECT medicalrec_patientid FROM medicalrec WHERE medicalrec_id = ?
                                 )";
                $patientStmt = $this->db->prepare($patientQuery);
                $patientStmt->execute([$medicalrec_id]);
                $fullName = $patientStmt->fetchColumn();
    
                // Log the deletion action in the system log.
                $logQuery = "INSERT INTO systemlog (syslog_userid, syslog_date, syslog_time, syslog_action) 
                             VALUES (?, CURDATE(), CURTIME(), ?)";
                $logStmt = $this->db->prepare($logQuery);
                $logAction = "Deleted Medical Record for Patient: " . $fullName;
                $logStmt->execute([$admin_id, $logAction]);
    
                // Commit the transaction after success.
                $this->db->commit();
                return ['success' => true, 'message' => 'Medical record deleted and log entry created successfully.'];
            } else {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Failed to delete the medical record.'];
            }
        } catch (PDOException $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    
    // Retrieves all medical records for a specific patient.
    public function getMedicalRecords($patientid) {
        $records = [];
        $current = $this->medicalrecs->head;

        // Loop through all records and select those for the given patient.
        while ($current !== null) {
            if (strcasecmp($current->item->medicalrec_patientid, $patientid) === 0) {
                $records[] = $current->item; 
            }
            $current = $current->next; 
        }

        return $records; 
    }

    // Retrieves the file path of a medical record by its ID.
    public function getFilePathByMedicalRecId($medicalrecId) {
        $filePath = null; 
        $current = $this->medicalrecs->head; 
    
        // Loop through the records to find the matching ID and return the file path.
        while ($current !== null) {
            if (strcasecmp($current->item->medrec_id, $medicalrecId) === 0) {
                $filePath = $current->item->medicalrec_file; 
                break; 
            }
            $current = $current->next; 
        }
    
        return $filePath;
    }
    
    // Checks if a medical record already has the given filename for the patient.
    public function isDuplicateFilename($patientid, $filename) {
        return $this->medicalrecs->isDuplicateFilename($patientid, $filename);
    }

}
?>
