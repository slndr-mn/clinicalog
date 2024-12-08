<?php
class SystemLogs {
    // Database connection and table name
    private $conn;
    private $table_name = "systemlog";

    // Properties representing the columns of the 'systemlog' table
    public $id;     
    public $userid; 
    public $date;   
    public $time;   
    public $action; 

    // Constructor to initialize database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Method to retrieve all system logs with user details
    public function getAllSystemLogs() {
        // SQL query to fetch system logs joined with user information
        $query = "
            SELECT 
                s.syslog_id AS id, 
                a.user_idnum AS idnum, 
                CONCAT(a.user_fname, ' ', a.user_lname) AS name, 
                s.syslog_date AS date, 
                s.syslog_time AS time, 
                s.syslog_action AS action
            FROM " . $this->table_name . " s
            LEFT JOIN adminusers a ON s.syslog_userid = a.user_idnum
            ORDER BY s.syslog_date DESC, s.syslog_time DESC";
        
         // Prepare and execute the query
         $stmt = $this->conn->prepare($query);
         $stmt->execute();
 
         // Return the result set
         return $stmt;
     }
 }
 
 ?>