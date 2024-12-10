<?php

function parseEnvFile($filePath) {
    if (!file_exists($filePath)) {
        throw new Exception("File not found: $filePath");
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $envArray = [];

    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse key=value pairs
        $parts = explode('=', $line, 2);
        if (count($parts) == 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);

            // Remove surrounding quotes (if any)
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }

            $envArray[$key] = $value;
        }
    }

    return $envArray;
}


// Function to measure query execution time
function measureQuery($conn, $query, $label) {
    $startTime = microtime(true);
    $result = $conn->query($query);
    $executionTime = round(100*(microtime(true) - $startTime)) / 100;

    if ($result) {
        echo "{$executionTime}\t{$label}\n";
        if (is_object($result)) $result->free(); // Free result set for SELECT queries
    } else {
        echo "\t{$label}\tFailed: " . $conn->error . "\n";
    }
}

$BASE = dirname(dirname(__FILE__));
$ENV = parseEnvFile("$BASE/.env");

// Database configuration
$host = @$argv[1]?:$ENV['database.default.hostname']; // Replace with your DB host
$username = @$argv[2]?:$ENV['database.default.username']; // Replace with your DB username
$password = @$argv[3]?:$ENV['database.default.password']; // Replace with your DB password
$database = @$argv[4]?:$ENV['database.default.database']; // Replace with your DB name

// Initialize MySQLi connection
$conn = new mysqli();
$startTime = microtime(true);
$conn->real_connect($host, $username, $password, $database);
$connectionTime = microtime(true) - $startTime;

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connection established to {$host} in {$connectionTime} seconds.\n";




// drop if exists
$dropQuery = "DROP TABLE IF EXISTS sample_table1";
measureQuery($conn, $dropQuery, "Drop Table Test 1");
$dropQuery = "DROP TABLE IF EXISTS sample_table2";
measureQuery($conn, $dropQuery, "Drop Table Test 2");


// Create table
$createQuery = "CREATE TABLE sample_table1 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
)";
measureQuery($conn, $createQuery, "Create Test 1");
$createQuery = "CREATE TABLE IF NOT EXISTS sample_table2 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ext_id INT NOT NULL,
    value INT NOT NULL,
    INDEX idx_ext_id (ext_id)
)";
measureQuery($conn, $createQuery, "Create Test 2");


// Test 2: Write performance
$base = 5000;
$writeQuery = "INSERT INTO sample_table1 (id,name) VALUES (?,?)";
$startTime = microtime(true);
for ($i = 0; $i < $base; $i++) {
    $stmt = $conn->prepare($writeQuery);
    $id = 1000 + $i;
    $value = "t".rand(1000000,9999999);
    $stmt->bind_param('is',$id,$value);
    $stmt->execute();
}
$writeTime = round(100*(microtime(true) - $startTime))/100;
echo "{$writeTime}\tWrite Test: $base rows.\n";

$writeQuery = "INSERT INTO sample_table2 (ext_id,value) VALUES (?,?)";
$startTime = microtime(true);
$created = 0;
for ($i = 0; $i < $base; $i++) {
  $max = rand(5,10);
  for ($n = 0; $n < $max; $n++) {
    $stmt = $conn->prepare($writeQuery);
    $id = 1000 + $i;
    $value = rand(1000,9999);
    $stmt->bind_param('ii',$id,$value);
    $stmt->execute();
  }
  $created += $max;
}
$writeTime = round(100*(microtime(true) - $startTime))/100;
echo "{$writeTime}\tWrite Test: $created rows.\n";


// Test 1: Read performance
$readQuery = "SELECT * FROM sample_table2 order by value";
measureQuery($conn, $readQuery, "Read Test");


// Test 3: Complex query performance
$complexQuery = "SELECT t1.name, AVG(t2.value) AS avg_value 
                 FROM sample_table1 t1 
                 JOIN sample_table2 t2 ON t1.id = t2.id 
                 GROUP BY t1.name";
measureQuery($conn, $complexQuery, "Complex Query Test");

// Drop table
$dropQuery = "DROP TABLE sample_table1";
measureQuery($conn, $dropQuery, "Drop Table Test 1");
$dropQuery = "DROP TABLE sample_table2";
measureQuery($conn, $dropQuery, "Drop Table Test 2");


// Close connection
$conn->close();
echo "Connection closed.\n";
?>