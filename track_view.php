<?php
/**
 * Portfolio View Tracking
 * Increment view count for portfolio items
 */

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// Only allow AJAX requests
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    // Allow regular fetch requests as well
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (strpos($contentType, 'application/json') === false) {
        http_response_code(400);
        exit('Invalid request');
    }
}

try {
    require_once 'config/database.php';
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['item_id']) || !is_numeric($input['item_id'])) {
        http_response_code(400);
        exit(json_encode(['error' => 'Invalid item ID']));
    }
    
    $itemId = (int)$input['item_id'];
    
    // Rate limiting - max 1 view per item per IP per hour
    $cacheKey = 'view_' . $itemId . '_' . md5($_SERVER['REMOTE_ADDR']);
    $cacheFile = sys_get_temp_dir() . '/' . $cacheKey;
    
    if (file_exists($cacheFile)) {
        $lastView = (int)file_get_contents($cacheFile);
        if (time() - $lastView < 3600) { // 1 hour
            // Don't increment, but return success
            http_response_code(200);
            exit(json_encode(['success' => true, 'message' => 'Already counted']));
        }
    }
    
    // Increment view count
    $portfolioManager = new PortfolioManager();
    $portfolioManager->incrementViews($itemId);
    
    // Cache this view
    file_put_contents($cacheFile, time());
    
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'View counted']);
    
} catch (Exception $e) {
    error_log('Portfolio tracking error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>