<?php
// Include functions file for database configuration
require_once 'functions.php';

// Database configuration
$host = DB_HOST;
$user = DB_USER;
$password = DB_PASS;
$database = DB_NAME;

// Create connection
$conn = new mysqli($host, $user, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db($database);

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql) === TRUE) {
    echo "Users table created successfully<br>";
} else {
    echo "Error creating users table: " . $conn->error . "<br>";
}

// Create leads table
$sql = "CREATE TABLE IF NOT EXISTS `leads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_name` varchar(255) NOT NULL,
  `niche` varchar(255) DEFAULT NULL,
  `base_url` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_numbers` text DEFAULT NULL,
  `business_description` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT 'callStack',
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `category` (`category`),
  CONSTRAINT `leads_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql) === TRUE) {
    echo "Leads table created successfully<br>";
} else {
    echo "Error creating leads table: " . $conn->error . "<br>";
}

// Insert a default user
$password_hash = password_hash('password', PASSWORD_DEFAULT);
$sql = "INSERT INTO `users` (`name`, `email`, `password`)
        SELECT 'Admin User', 'admin@example.com', '$password_hash'
        WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE `email` = 'admin@example.com')";

if ($conn->query($sql) === TRUE) {
    echo "Default user created successfully<br>";
} else {
    echo "Error creating default user: " . $conn->error . "<br>";
}

// Insert sample leads
$leads = [
    [
        'business_name' => 'Digital Marketing Experts',
        'niche' => 'Digital Marketing',
        'email' => 'contact@digitalmarketingexperts.com',
        'phone_numbers' => json_encode(['555-123-4567', '555-765-4321']),
        'website' => 'digitalmarketingexperts.com',
        'notes' => 'Specializes in SEO and PPC campaigns',
        'category' => 'callStack'
    ],
    [
        'business_name' => 'Web Solutions Inc.',
        'niche' => 'Web Development',
        'email' => 'info@websolutions.com',
        'phone_numbers' => json_encode(['555-234-5678']),
        'website' => 'websolutions.com',
        'notes' => 'Looking for e-commerce development',
        'category' => 'coldLeads'
    ],
    [
        'business_name' => 'Creative Design Studio',
        'niche' => 'Graphic Design',
        'email' => 'hello@creativedesign.com',
        'phone_numbers' => json_encode(['555-345-6789']),
        'website' => 'creativedesign.com',
        'notes' => 'Interested in brand refresh',
        'category' => 'warmLeads'
    ],
    [
        'business_name' => 'Content Writers Pro',
        'niche' => 'Content Marketing',
        'email' => 'writers@contentpro.com',
        'phone_numbers' => json_encode(['555-456-7890']),
        'website' => 'contentwriterspro.com',
        'notes' => 'Current client for blog management',
        'category' => 'currentlyWorkingWith'
    ]
];

// Check if leads table is empty
$result = $conn->query("SELECT COUNT(*) as count FROM leads");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    // Insert sample leads
    foreach ($leads as $lead) {
        $sql = "INSERT INTO `leads` (`business_name`, `niche`, `email`, `phone_numbers`, `website`, `notes`, `category`)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss",
            $lead['business_name'],
            $lead['niche'],
            $lead['email'],
            $lead['phone_numbers'],
            $lead['website'],
            $lead['notes'],
            $lead['category']
        );
        if ($stmt->execute()) {
            echo "Sample lead '{$lead['business_name']}' created successfully<br>";
        } else {
            echo "Error creating sample lead: " . $stmt->error . "<br>";
        }
        $stmt->close();
    }
} else {
    echo "Leads table already has data, skipping sample data insertion<br>";
}

// Close connection
$conn->close();
echo "Database setup completed!";
?>
