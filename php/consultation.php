<?php
// Consultation class
class Consultation {
    public $consult_id;
    public $consult_patientid;
    public $consultation_consult_diagnosis;
    public $consult_treatmentnotes;
    public $consultation_consult_remark;
    public $consult_date;

// Initialize consultation details
    public function __construct($id, $idnum, $consult_diagnosis, $notes, $consult_remark, $date) {
        $this->consult_id = $id;
        $this->consult_patientid = $idnum;
        $this->consultation_consult_diagnosis = $consult_diagnosis;
        $this->consult_treatmentnotes = $notes;
        $this->consultation_consult_remark = $consult_remark;
        $this->consult_date = $date;
    }
}

//Prescribemed class 
class Prescribemed {
    public $pm_id;
    public $pm_consultid;
    public $pm_medstockid;
    public $pm_medqty;  

    public function __construct($id, $consultid, $medstockid, $medqty) {
        $this->pm_id = $id;
        $this->pm_consultid = $consultid;
        $this->pm_medstockid = $medstockid;
        $this->pm_medqty = $medqty;  
    }
}


// ConsultListNode class
class ConsultListNode {
    public $item;
    public $next;

    public function __construct($item) {
        $this->item = $item;
        $this->next = null;
    }
}

// ConsultationLinkedList class
class ConsultationLinkedList {
    public $head;

    public function __construct() {
        $this->head = null;
    }

// Adds an item to the list
    public function add($item) {
        $newNode = new ConsultListNode($item);
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

// Retrieves all nodes as an array
    public function getAllNodes() {
        $nodes = [];
        $current = $this->head;
        while ($current !== null) {
            $nodes[] = $current->item;
            $current = $current->next;
        }
        return $nodes;
    }

// Finds a node by ID
    public function find($id) {
        $current = $this->head;
        while ($current !== null) {
            if ($current->item->consult_id == $id) {
                return $current->item;
            }
            $current = $current->next;
        }
        return null;
    }

// Removes a node by ID
    public function remove($id) {
        if ($this->head === null) return false;

        if ($this->head->item->consult_id == $id) {
            $this->head = $this->head->next;
            return true;
        }

        $current = $this->head;
        while ($current->next !== null) {
            if ($current->next->item->consult_id == $id) {
                $current->next = $current->next->next;
                return true;
            }
            $current = $current->next;
        }
        return false;
    }
}

// ConsultationManager class
class ConsultationManager { 
    private $db;
    public $consultations;
    public $prescribemeds;

    public function __construct($db) {
        $this->db = $db;
        $this->consultations = new ConsultationLinkedList();
        $this->prescribemeds = new ConsultationLinkedList();
        $this->loadConsultations();
        $this->loadPrescribemeds();
    }

// Load all consultations from the database
    private function loadConsultations() {

        $sql = "SELECT *
                FROM consultations";
        $stmt = $this->db->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Consultation object for each row and add it to the linked list
            $consultation = new Consultation(
                $row['consult_id'],
                $row['consult_patientid'],
                $row['consult_diagnosis'],
                $row['consult_treatmentnotes'],
                $row['consult_remark'],
                $row['consult_date'],
            );

            $this->consultations->add($consultation); // Add consultation to the list
        }
    }

    // Load all prescribed medications from the database
    private function loadPrescribemeds() {
        $sql = "SELECT * FROM prescribemed";
        $stmt = $this->db->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Prescribemed object for each row and add it to the linked list
            $prescribemed = new Prescribemed(
                $row['pm_id'],
                $row['pm_consultid'],
                $row['pm_medstockid'],
                $row['pm_medqty']
            );
            $this->prescribemeds->add($prescribemed); // Add prescribed medication to the list
        }
    }

    // Insert a new prescribed medicine into the database
    public function insertPrescribemed($pm_consultid, $pm_medstockid, $pm_medqty) {
        $sql = "INSERT INTO prescribemed (pm_consultid, pm_medstockid, pm_medqty) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            // Execute the query with provided values
            $stmt->execute([$pm_consultid, $pm_medstockid, $pm_medqty]);
            $pm_id = $this->db->lastInsertId(); 
            // Create a new Prescribemed object and add it to the list
            $prescribemed = new Prescribemed($pm_id, $pm_consultid, $pm_medstockid, $pm_medqty);
            $this->prescribemeds->add($prescribemed);
            echo "Prescribed Medicine inserted successfully.<br>";
            return true;
        } else {
            echo "Error inserting Prescribed medicine.<br>";
            return false;
        }
    }
 
    // Insert a new consultation into the database
    public function insertConsultation($admin_id, $consult_patientid, $consult_diagnosis, $consult_treatmentnotes, $consult_remark, $consult_date) {
        try {

            $setAdminIdQuery = "SET @admin_id = :admin_id";
            $setStmt = $this->db->prepare($setAdminIdQuery);
            $setStmt->bindValue(':admin_id', $admin_id);
            $setStmt->execute();

            // SQL statement to match database table column names
            $sql = "INSERT INTO consultations (consult_patientid, consult_diagnosis, consult_treatmentnotes, consult_remark, consult_date)
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
    
            // Parameters for the query
            $params = [
                $consult_patientid, 
                $consult_diagnosis, 
                $consult_treatmentnotes, 
                $consult_remark,  
                $consult_date
            ];
    
            // Execute the query
            $stmt->execute($params);
    
            // Retrieve the last inserted consultation ID
            $consult_id = $this->db->lastInsertId(); 
    
            $consultation = new Consultation($consult_id, $consult_patientid, $consult_diagnosis, $consult_treatmentnotes, $consult_remark, $consult_date);
    
            $this->consultations->add($consultation); 
    
            // Return success response
            return [
                'status' => 'success', 
                'message' => 'Consultation inserted successfully.', 
                'consult_id' => $consult_id // Return the consultation ID
            ];
    
        } catch (PDOException $e) {
            // Log and return a detailed database error
            error_log("Error inserting consultation: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'An error occurred while inserting consultation.',
                'details' => ['sqlState' => $e->getCode()]
            ];
        } catch (Exception $e) {
            // Return general error message
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    
    
// Retrieve all consultations with related prescription data
    public function getAllItems() {
        // Get consultations and prescriptions
        $consultations = $this->getConsultations(); // Fetch consultations with patient names
        $prescribemeds = $this->prescribemeds->getAllNodes();
        
        // Initialize an array to hold the combined items
        $combinedItems = [];
    
        // Create a map of consultations based on their ID
        $consultationMap = [];
        foreach ($consultations as $consultation) {
            $consultationMap[$consultation['consult_id']] = $consultation;
        }
    
        // Link prescriptions with consultations
        foreach ($prescribemeds as $prescribemed) {
            $consultation = $consultationMap[$prescribemed->pm_consultid] ?? null; // Get the corresponding consultation
            if ($consultation) {
                // Merge consultation and prescription data
                $combinedItems[] = array_merge($consultation, [
                    'pm_medstockid' => $prescribemed->pm_medstockid,
                    'pm_medqty' => $prescribemed->pm_medqty,
                ]);
            }
        }
    
        return $combinedItems; 
    }
    
// Fetch consultations along with patient details
    public function getConsultations() {
        // Correctly reference the patient ID column in the JOIN clause
        $sql = "SELECT consultations.*, patients.patient_fname, patients.patient_lname 
                FROM consultations 
                JOIN patients ON consultations.consult_patientid = patients.patient_id"; // Adjust this if necessary
        
        // Fetch all consultations
        $consultations = $this->db->query($sql)->fetchAll();
    
        // Prepare the consultation details for return
        $consultationDetails = [];
        foreach ($consultations as $consultation) {
            $consultationDetails[] = [
                'consult_id' => $consultation['consult_id'],
                'name' => $consultation['patient_lname'] . ' ' . $consultation['patient_fname'],
                'consult_patientid' => $consultation['consult_patientid'],
                'consult_diagnosis' => $consultation['consult_diagnosis'],
                'consult_remark' => $consultation['consult_remark'],
                'consult_treatmentnotes' => $consultation['consult_treatmentnotes'],
                'consult_date' => $consultation['consult_date'],
            ];
        }
    
        return $consultationDetails;
    }

     // Fetch all prescribed medications
    public function getPrescribedMeds() {
        // Query to fetch all data from the prescribemed table
        $sql = "SELECT * FROM prescribemed";
        
        // Fetch all records from prescribemed
        $prescribedMeds = $this->db->query($sql)->fetchAll();
        
        // Prepare the prescription details for return
        $prescribedMedDetails = [];
        foreach ($prescribedMeds as $prescribedMed) {
            $prescribedMedDetails[] = [
                'pm_id' => $prescribedMed['pm_id'],
                'pm_consultid' => $prescribedMed['pm_consultid'],
                'pm_medstockid' => $prescribedMed['pm_medstockid'],
                'pm_medqty' => $prescribedMed['pm_medqty']
            ];
        }
        
        return $prescribedMedDetails;
    }
    

// Update consultation details
    public function updateConsultation($admin_id, $consult_id, $diagnosis, $treatment_notes, $remark) {
        try {
            // Check if the consultation exists
            $checkSql = "SELECT consult_id FROM consultations WHERE consult_id = :consult_id";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->bindParam(':consult_id', $consult_id, PDO::PARAM_INT);
            $checkStmt->execute();
    
            if ($checkStmt->rowCount() === 0) {
                return ['status' => 'error', 'message' => 'Consultation not found.'];
            }

            // Set the admin ID for auditing
            $setAdminIdQuery = "SET @admin_id = :admin_id";
            $setStmt = $this->db->prepare($setAdminIdQuery);
            $setStmt->bindValue(':admin_id', $admin_id);
            $setStmt->execute();        
    
            // If consultation exists, proceed to update
            $sql = "UPDATE consultations SET 
                        consult_diagnosis = :consult_diagnosis,
                        consult_treatmentnotes = :consult_treatmentnotes,
                        consult_remark = :consult_remark
                    WHERE consult_id = :consult_id";
            $stmt = $this->db->prepare($sql);
    
            // Bind parameters
            $stmt->bindParam(':consult_diagnosis', $diagnosis, PDO::PARAM_STR);
            $stmt->bindParam(':consult_treatmentnotes', $treatment_notes, PDO::PARAM_STR);
            $stmt->bindParam(':consult_remark', $remark, PDO::PARAM_STR);
            $stmt->bindParam(':consult_id', $consult_id, PDO::PARAM_INT);
    
            // Execute the update
            $stmt->execute();
    
            // Check if any row was updated
            if ($stmt->rowCount() > 0) {
                return [
                    'status' => 'success',
                    'edit_consult_id' => $consult_id
                ];
            } else {
                return ['status' => 'warning', 'message' => 'No changes were made.'];
            }
        } catch (PDOException $e) {
            // Log detailed error information
            error_log("Error updating consultation: " . $e->getMessage() . 
                      "\nConsult ID: " . $consult_id . 
                      "\nDiagnosis: " . $diagnosis . 
                      "\nTreatment Notes: " . $treatment_notes . 
                      "\nRemark: " . $remark);
            return ['status' => 'error', 'message' => 'An error occurred while updating the consultation.'];
        }
    }
    
    // Fetch a single consultation by ID
    public function getConsultationById($consult_id) {
        $sql = "SELECT * FROM consultations WHERE consult_id = :consult_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':consult_id' => $consult_id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    // Fetch prescribed medicine details by its ID
    public function getMedDataByPmId($pm_id) {
        try {
            $stmt = $this->db->prepare("SELECT pm_medstockid, pm_medqty FROM prescribemed WHERE pm_id = :pm_id");
            $stmt->execute([':pm_id' => $pm_id]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $result; 
            } else {
                return false; 
            }
        } catch (PDOException $e) {
            error_log("Error fetching medicine data by pm_id: " . $e->getMessage());
            return false; 
        }
    }

    // Update prescribed medicine details
    public function updatePrescribemd($pm_id, $pm_consultid, $medstock_id, $pm_medqty) {
        try {
            error_log("Attempting to update prescribed medicine: PM ID = $pm_id, Consult ID = $pm_consultid, Medstock ID = $medstock_id, Quantity = $pm_medqty");
    
            // Check for empty inputs
            if (empty($pm_id) || empty($pm_consultid) || empty($medstock_id) || $pm_medqty < 0) {
                error_log("Invalid input data: PM ID = $pm_id, Consult ID = $pm_consultid, Medstock ID = $medstock_id, Quantity = $pm_medqty");
                return ['status' => 'error', 'message' => 'Invalid input data provided.'];
            }
    
            // Update query for prescribed medicine
            $sql = "UPDATE prescribemed SET 
                        pm_medstockid = :pm_medstockid,
                        pm_medqty = :pm_medqty 
                    WHERE pm_id = :pm_id AND pm_consultid = :pm_consultid";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':pm_consultid' => $pm_consultid,
                ':pm_medstockid' => $medstock_id,
                ':pm_medqty' => $pm_medqty,
                ':pm_id' => $pm_id
            ]);
    
            // Check if any row was updated
            if ($stmt->rowCount() === 0) {
                error_log("No rows updated for PM ID = $pm_id, Consult ID = $pm_consultid.");
                throw new PDOException("No prescribed medicine found with the given Consult ID or no changes made.");
            }
        
            return ['status' => 'success', 'message' => 'Prescribed medicine updated successfully.'];
        } catch (PDOException $e) {
            error_log("Error updating Prescribed Medicine: " . $e->getMessage() . " (Code: " . $e->getCode() . ")");
            return ['status' => 'error', 'message' => 'An error occurred while updating the prescribed medicine.'];
        }
    }
    
    
    

    public function getPmIdByConsultId($consult_id) {
        $sql = "SELECT pm_id FROM prescribemed WHERE pm_consultid = :consult_id LIMIT 1"; // Assuming pm_consultid links to consultations
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':consult_id' => $consult_id]);
        return $stmt->fetchColumn(); // Returns the pm_id or false if not found
    }

    public function getMedQtyByPmId($pm_id) {
        // Prepare the SQL query to retrieve the quantity of the prescribed medicine
        $sql = "SELECT pm_medqty FROM prescribemed WHERE pm_id = :pm_id";
        $stmt = $this->db->prepare($sql);
        
        // Execute the statement
        $stmt->execute([':pm_id' => $pm_id]);
    
        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if the result is valid and return the quantity, otherwise return null
        if ($result) {
            return (int)$result['pm_medqty'];
        } else {
            return null; // or throw an exception if preferred
        }
    }   
    
    public function getMedicineStock($medstock_id) {
        $sql = "SELECT medstock_qty FROM medstock WHERE medstock_id = :medstock_id";
        $stmt = $this->db->prepare($sql);
    
        try {
            $stmt->execute([':medstock_id' => $medstock_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return ['status' => 'success', 'quantity' => $result['medstock_qty']];
            } else {
                return ['status' => 'error', 'message' => 'Medicine stock not found.'];
            }
        } catch (PDOException $e) {
            error_log("Error fetching Medicine Stock: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'An error occurred while fetching the medicine stock.'];
        }    
    

    }

    public function updateEditedMedStock($medstock_id, $original_qty, $edited_qty) {
        try {
            // Fetch the current stock quantity
            $stmt = $this->db->prepare("SELECT medstock_qty FROM medstock WHERE medstock_id = :medstock_id");
            $stmt->execute([':medstock_id' => $medstock_id]);
            $medstock = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($medstock) {
                $current_qty = (int) $medstock['medstock_qty'];
    
                // Calculate the difference
                $quantityDifference = $edited_qty - $original_qty;
                $new_qty = $current_qty - $quantityDifference;
    
                // Ensure stock is not negative
                if ($new_qty < 0) {
                    return ['status' => 'error', 'message' => 'Insufficient stock.'];
                }
    
                // Update stock
                $update_stmt = $this->db->prepare("UPDATE medstock SET medstock_qty = :new_qty WHERE medstock_id = :medstock_id");
                $update_stmt->execute([':new_qty' => $new_qty, ':medstock_id' => $medstock_id]);
    
                error_log("Updated Stock Quantity: $new_qty for medstock_id: $medstock_id");
                return ['status' => 'success', 'message' => 'Stock updated successfully.'];
            } else {
                return ['status' => 'error', 'message' => 'Medicine stock not found.'];
            }
        } catch (PDOException $e) {
            error_log("Error updating stock: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error updating stock.'];
        }
    }
    

    // Delete a consultation along with its related prescriptions
    public function deleteConsultation($consult_id) {
        try {
            // Start a transaction
            $this->db->beginTransaction();
    
            // Step 1: Delete related prescriptions
            $sqlPrescriptions = "DELETE FROM prescribemed WHERE pm_consultid = ?";
            $stmtPrescriptions = $this->db->prepare($sqlPrescriptions);
            $stmtPrescriptions->execute([$consult_id]);
    
            // Step 2: Delete the consultation
            $sqlConsultation = "DELETE FROM consultations WHERE consult_id = ?";
            $stmtConsultation = $this->db->prepare($sqlConsultation);
            $stmtConsultation->execute([$consult_id]);
    
            // Commit transaction if both deletions succeed
            $this->db->commit();
    
            // Optionally, remove from the linked list if applicable
            if (isset($this->consultations) && $this->consultations->remove($consult_id)) {
                return ['status' => 'success', 'message' => 'Consultation and related prescriptions deleted successfully.'];
            } else {
                return ['status' => 'success', 'message' => 'Consultation deleted, but error removing from linked list.'];
            }
        } catch (Exception $e) {
            // Rollback transaction if any error occurs
            $this->db->rollBack();
            return ['status' => 'error', 'message' => 'Error deleting consultation: ' . $e->getMessage()];
        }
    }
    
    // Search for a patient by name or ID
    public function searchPatientByNameOrId($query) {
        $sql = "SELECT * FROM patients WHERE CONCAT(patient_fname, ' ', patient_lname) LIKE ? OR consult_patientid = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['%' . $query . '%', $query]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
     
    // Fetch available medicine quantity for a stock ID
    public function getAvailableQuantity($medstock_id) {
        $stmt = $this->db->prepare("SELECT m.medstock_qty - 
                                      (IFNULL(SUM(pm.pm_medqty), 0) + IFNULL(SUM(mi.mi_medqty), 0)) AS available_stock
                                      FROM medstock m
                                      LEFT JOIN prescribemed pm ON pm.pm_medstockid = m.medstock_id
                                      LEFT JOIN medissued mi ON mi.mi_medstockid = m.medstock_id
                                      WHERE m.medstock_id = ?
                                      GROUP BY m.medstock_id");
        $stmt->execute([$medstock_id]);
        return (int) $stmt->fetchColumn();
    }
    


}

?>