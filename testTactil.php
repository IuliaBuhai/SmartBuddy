<?php
// Start the session
session_start();

// Check if the user is logged in
if (isset($_SESSION['email'])) {
    // Get the email from the session
    $email = $_SESSION['email'];

    // Include database connection
    include 'connect.php';

    // Prepare and execute query to get user's name based on email
    $stmt = $conn->prepare("SELECT Nume FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the user's name
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['Nume'];
    } else {
        // If user data not found, handle it accordingly
        $name = 'User';
    }

    // Check if the form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the score from the form
        $score = $_POST['scor'];

        // Check if a record for this user and test already exists
        $checkStmt = $conn->prepare("SELECT scor FROM testtactil WHERE Email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // If exists, update the score
            $updateStmt = $conn->prepare("UPDATE testtactil SET scor = ? WHERE Email = ?");
            $updateStmt->bind_param("is", $score, $email);
            $updateStmt->execute();
        } else {
            // If not exists, insert a new record
            $insertStmt = $conn->prepare("INSERT INTO testtactil (Email, scor) VALUES (?, ?)");
            $insertStmt->bind_param("si", $email, $score);
            $insertStmt->execute();
        }

        // Redirect to dashboard after submitting the form
        header("Location: dashboard.php");
    }
} else {
    // If the user is not logged in, redirect them to the login page
    header("Location: ind.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test preferinte vizuale</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            background-image: url("back1.jpg");
            
            background-size: cover;
            transition: background-image 0.5s ease;
            background-attachment: fixed;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .question-box {
            background-color: #1b1e2d;
            color: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
            box-shadow: -10px -10px 15px inset rgb(23, 22, 33),
            10px 10px 15px rgb(0, 0, 0);
            ;
            width: 60vw;
        }

        .question-box h2 {
            margin-top: 0;
        }

        .answer-form {
            margin-top: 20px;
        }

        .answer-form input[type="radio"] {
            margin-bottom: 10px;
        }

        .answer-form button {
            background-color: #090b14;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .answer-form button:hover {
            background-color: #635f83;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .result {
            margin-top: 30px;
            text-align: center;
            color: #fff;
        }

        .question-list {
            margin-top: 20px;
            text-align: left;
            padding: 20px;
        }

        .correct {
            color: rgb(168, 210, 168);
            border: 1px solid green;
            margin-left: auto;
            margin-right: auto;
            margin-top: 20px;
            text-align: center;
            width: 60%;
            background-color: rgba(33, 47, 33, 0.4);
            border-radius: 10px;
            padding: 5px;
        }

        .wrong {
            color: rgb(205, 147, 147);
            border: 1px solid red;
            margin-left: auto;
            margin-right: auto;
            margin-top: 20px;
            text-align: center;
            width: 60%;
            background-color: rgba(30, 16, 16, 0.459);
            border-radius: 10px;
            padding: 5px;
        }

        .box {
            margin: 20px;
        }

        .nav-bar {
            position: fixed;
            right: auto;
            left: auto;
            flex-direction: row;
            height: 10px;
            width: 0px;
            background-color: rgb(234, 235, 237);
            animation: nav linear;
            text-align: center;
            justify-content: center;
            align-items: center;
            animation-timeline: scroll();
            top: 0px;
            mask-origin: center;
        }

        @keyframes nav {
            to {
                width: 100%
            }
        }
    </style>
</head>
<body>

<form id="testForm" method="POST" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
    <input type="hidden" name="test_tactil" id="testTactilInput">
    <input type="hidden" name="scor" id="scorInput">
</form>

<div class="container">
    <div class="question-box" id="questionBox">
        <!-- Întrebarea va fi completată din JavaScript -->
    </div>
</div>

<div class="result" id="result"></div>
<div class="question-list" id="questionList"></div>

<script>
    let questions = [
        { question: "Prefer să folosesc postere, modele sau practică efectivă și alte activități în clasă", answers: [{ text: "Des", score: 5 }, { text: "Cateodata", score: 3 }, { text: "Niciodată", score: 1 }]},
        { question: "Îmi place să lucrez cu mâinile sau să fac lucruri", answers: [{ text: "Des", score: 5 }, { text: "Cateodata", score: 3 }, { text: "Niciodată", score: 1 }]},
        { question: "Îmi amintesc cel mai bine scriind lucrurile.", answers: [{ text: "Des", score: 5 }, { text: "Cateodata", score: 3 }, { text: "Niciodată", score: 1 }]},
        { question: "Mă joc cu monede sau chei în buzunar.", answers: [{ text: "Des", score: 5 }, { text: "Cateodata", score: 3 }, { text: "Niciodată", score: 1 }]},
        { question: "Mestec gumă, fumez sau iau gustări în timp ce studiez.", answers: [{ text: "Des", score: 5 }, { text: "Cateodata", score: 3 }, { text: "Niciodată", score: 1 }]},
        { question: "Învăț ortografia cuvintelor „scriindu-le cu degetele” în aer", answers: [{ text: "Des", score: 5 }, { text: "Cateodata", score: 3 }, { text: "Niciodată", score: 1 }]},
        { question: "Țin obiecte în mâini în timpul perioadelor de învățare.", answers: [{ text: "Des", score: 5 }, { text: "Cateodata", score: 3 }, { text: "Niciodată", score: 1 }]},
        { question: "Nu am o problemă când vine vorba de contactul fizic cu alte persoane", answers: [{ text: "Des", score: 5 }, { text: "Cateodata", score: 3 }, { text: "Niciodată", score: 1 }]}
    ];

    let questionIndex = 0;
    let score = 0;

    // Functie pentru a afisa intrebarea curenta
    function displayQuestion() {
        const question = questions[questionIndex];
        const answersHtml = question.answers.map((answer, index) => `<input type="radio" name="answer" value="${index}"> ${answer.text}<br>`).join('');
        document.getElementById('questionBox').innerHTML = `
                <h2>Întrebarea ${questionIndex + 1}:</h2>
                <p>${question.question}</p>
                <form class="answer-form">
                    ${answersHtml}
                    <button type="button" onclick="nextQuestion()">Următoarea întrebare</button>
                </form>
            `;
    }

    // Functie pentru a incepe testul
    function startTest() {
        displayQuestion();
    }

    // Functie apelata la urmatoarea intrebare
    function nextQuestion() {
        const selectedAnswer = document.querySelector('input[name="answer"]:checked');
        if (!selectedAnswer) {
            alert("Selectează un răspuns!");
            return;
        }
        const userAnswer = parseInt(selectedAnswer.value);
        const question = questions[questionIndex];
        score += question.answers[userAnswer].score;
        questionIndex++;
        if (questionIndex < questions.length) {
            displayQuestion();
        } else {
            showResult();
        }
    }

    // Functie pentru a afisa rezultatul
    function showResult() {
        document.getElementById('result').innerHTML = `
                <h2>Testul s-a încheiat!</h2>
                <p>Ai obținut un scor total de ${score} puncte.</p>
            `;
        const classStyle = document.getElementsByClassName('container')[0];
        classStyle.style.display = 'none';

        // Actualizează valorile câmpurilor ascunse
        document.getElementById('testTactilInput').value = 'valoare_test';
        document.getElementById('scorInput').value = score;

        // Trimite formularul
        document.getElementById('testForm').submit();
    }

    // Porneste testul
    startTest();
</script>

</body>
</html>
