<?php
header("Content-Type: application/json");

$host = 'localhost';
$db = 'edpAct8';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

$pdo = new PDO($dsn, $user, $pass, $options);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT * from hobbies");
    $users = $stmt->fetchAll();
    echo json_encode($users);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $user_id = $input['user_id'];
    $hobby = $input['hobby'];

    // Check if the user_id exists in the users table
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user) {
        // If user exists, insert record into hobbies table
        $sql = "INSERT INTO hobbies (user_id, hobby, comment) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $hobby, $input['comment']]);
        echo json_encode(['message' => 'User Hobby added successfully']);
    } else {
        // If user does not exist, output error message
        echo json_encode(["error" => "The user id you entered not found"]);
    }
}
?>
