<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "quizapp";
$port = "3307";

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if ($conn -> connect_error) {
    die("Connection failed: " . $conn -> connect_error)
}

$questions = [
    [
        "question" => "What does php stands for?",
        "options" => ["Personal Home Page", "Private Home Page", "Php: Hypertext Processor", "Public Hypertext Preprocessor"],
        "answer" => 2
    ],
    [
        "question" => "Which symbol is used to access a property of an object in PHP?",
        "options" => [".", "->", "::", "#"],
        "answer" => 1
    ],
    [
        "question" => "Which function is used to include a file in PHP?",
        "options" => ["include()", "require()", "import()", "load()"],
        "answer" => 0
    ]
];

$score = 0;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = htmlspecialchars($_POST["name"]);

    foreach ($questions as $index => $question) {
        if (isset($_POST["question$index"]) && $_POST["question$index"] == $question['answer']) {
            $score++;
        }
    }

    $totalQuestions = count($questions);
    $stmt = $conn->prepare("INSERT INTO results (name, score, total_questions) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $name, $score, $totalQuestions);

    if ($stmt->execute()) {
        echo "<script>alert('Thank you, $name! Your Score: $score/$totalQuestions');</script>";
    } else {
        echo "<script>alert('Error saving results: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}