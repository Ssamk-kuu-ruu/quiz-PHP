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

$leaderboardQuery = "SELECT name, score, total_questions, timestamp FROM results ORDER BY score DESC, timestamp ASC LIMIT 10";
$leaderboardResult = $conn -> query($leaderboardQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
    <title>Dashboard | Quiz Application</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
      @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700");
      /* Add your styles here */
      body {
        background: #dfe9f5;
        font-family: "Poppins", sans-serif;
      }
      .container {
        display: flex;
      }
      nav {
        width: 280px;
        background: #fff;
        box-shadow: 0 20px 35px rgba(0, 0, 0, 0.1);
      }
      .main {
        padding: 20px;
        width: 100%;
      }
      .main-top {
        background: #0427ee;
        padding: 20px;
        border-radius: 10px;
        color: white;
        margin-bottom: 20px;
      }
      .quiz-section, .leaderboard-section {
        margin-bottom: 20px;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      }
      table {
        width: 100%;
        border-collapse: collapse;
      }
      table th, table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
      }
      table th {
        background-color: #f4f4f4;
      }
      button {
        background: #0427ee;
        color: #fff;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
      }
    </style>
</head>
<body>
<main class="main">
        <div class="main-top">
          <h1>Welcome to the Quiz Dashboard</h1>
          <p>Participate in the quiz and see your scores in the leaderboard!</p>
        </div>

        <section class="quiz-section">
          <h2>Quiz</h2>
          <form method="post">
            <label for="name">Enter Your Name:</label>
            <input type="text" id="name" name="name" required><br><br>
            <?php foreach ($questions as $index => $question): ?>
              <fieldset>
                <legend><?php echo $question['question']; ?></legend>
                <?php foreach ($question['options'] as $optionIndex => $option): ?>
                  <label>
                    <input type="radio" name="question<?php echo $index; ?>" value="<?php echo $optionIndex; ?>">
                    <?php echo $option; ?>
                  </label><br>
                <?php endforeach; ?>
              </fieldset>
            <?php endforeach; ?>
            <button type="submit">Submit Quiz</button>
          </form>
        </section>

        <section class="leaderboard-section">
          <h2>Leaderboard</h2>
          <table>
            <thead>
              <tr>
                <th>Rank</th>
                <th>Name</th>
                <th>Score</th>
                <th>Total Questions</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($leaderboardResult && $leaderboardResult->num_rows > 0): ?>
                <?php $rank = 1; ?>
                <?php while ($row = $leaderboardResult->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo $rank++; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo $row['score']; ?></td>
                    <td><?php echo $row['total_questions']; ?></td>
                    <td><?php echo date('Y-m-d H:i:s', strtotime($row['timestamp'])); ?></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="5">No scores available yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </section>
      </main>
    </div>
</body>
</html>