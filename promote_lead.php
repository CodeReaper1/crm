<?php
session_start();
require_once 'db_connection.php';

$response = ['success' => false, 'message' => ''];

if (isset($_POST['id']) && isset($_POST['action'])) {
    $leadId = $_POST['id'];
    $userId = $_SESSION['user_id']; // Ensure this lead belongs to current user
    
    try {
        // Update the lead status in database
        $stmt = $pdo->prepare("UPDATE leads SET status = 'warm' WHERE id = ? AND user_id = ?");
        $result = $stmt->execute([$leadId, $userId]);
        
        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Lead successfully promoted to warm leads';
        }
    } catch (PDOException $e) {
        $response['message'] = 'Database error occurred';
    }
}

header('Content-Type: application/json');
echo json_encode($response); 