<?php
header('Content-Type: application/json');

$file = 'books.json';

// Read all books
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['id'])) {
    echo file_get_contents($file);
    exit;
}

// Read a specific book
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $books = json_decode(file_get_contents($file), true);
    $id = (int)$_GET['id'];
    
    foreach ($books as $book) {
        if ($book['id'] === $id) {
            echo json_encode($book);
            exit;
        }
    }
    
    http_response_code(404);
    echo json_encode(['error' => 'Book not found']);
    exit;
}

// Add a new book
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if ($input) {
        $books = json_decode(file_get_contents($file), true);
        $newId = count($books) > 0 ? max(array_column($books, 'id')) + 1 : 1;
        $input['id'] = $newId;
        $books[] = $input;
        
        file_put_contents($file, json_encode($books, JSON_PRETTY_PRINT));
        http_response_code(201);
        echo json_encode($input);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
?>