<?php
session_start();

if(isset($_SESSION['email'])){
    $email = $_SESSION['email'];

    include 'connect.php';

    $stmt = $conn->prepare("SELECT Nume FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = htmlspecialchars($row['Nume']); // Properly escape output
    } else {
        $name = 'User';
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $score = $_POST['scor'];

        $insertStmt = $conn->prepare("INSERT INTO testGeneral1 (Email, scor) VALUES (?, ?)");
        $insertStmt->bind_param("si", $email, $score);
        $insertStmt->execute();
        header("Location: dashboard.php");
        exit();
    }
} else {
    header("Location: ind.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test preferinte pe baza de sunet</title>
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
            box-shadow: -10px -10px 15px inset rgb(23, 22, 33), 10px 10px 15px rgb(0, 0, 0);
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
            margin-left: 1rem;
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

        .options {
            display: flex;
            margin: auto;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
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

        .qpic {
            border-radius: 20px;
            width: 80%;
            max-height: 70vh;
            box-shadow: 10px 10px 20px #090b14, -0.5rem -0.5rem 1rem rgba(53, 53, 55, 0.99);
        }

        @keyframes nav {
            to {
                width: 100%
            }
        }
    </style>
</head>
<body>

<form id="testForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <input type="hidden" name="scor" id="scorInput">
</form>

<div class="container">
    <div class="question-box" id="questionBox">
        <!-- Întrebarea  -->
    </div>
</div>

<div class="result" id="result"></div>
<div class="question-list" id="questionList"></div>

<script>
    let questions = [
        { question: "<img src='testGeneral1/q1.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'B' },
        { question: "<img src='testGeneral1/q2.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'D' },
        { question: "<img src='testGeneral1/q3.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'A' },
        { question: "<img src='testGeneral1/q4.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'B' },
        { question: "<img src='testGeneral1/q5.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'B' },
        { question: "<img src='testGeneral1/q6.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'B' },
        { question: "<img src='testGeneral1/q7.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'C' },
        { question: "<img src='testGeneral1/q8.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'E' },
        { question: "<img src='testGeneral1/q9.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'E' },
        { question: "<img src='testGeneral1/q10.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'D' },
        { question: "<img src='testGeneral1/q11.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'B' },
        { question: "<img src='testGeneral1/q12.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'C' },
        { question: "<img src='testGeneral1/q13.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'D' },
        { question: "<img src='testGeneral1/q14.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'E' },
        { question: "<img src='testGeneral1/q15.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'B' },
        { question: "<img src='testGeneral1/q16.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'A' },
        { question: "<img src='testGeneral1/q17.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'B' },
        { question: "<img src='testGeneral1/q18.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'A' },
        { question: "<img src='testGeneral1/q19.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'D' },
        { question: "<img src='testGeneral1/q20.png' class='qpic'>", answers: ['A', 'B', 'C', 'D', 'E'], correctAnswer: 'C' }
    ];

    let questionIndex = 0;
    let score = 0;

    function displayQuestion() {
        const question = questions[questionIndex];
        const answersHtml = question.answers.map((answer, index) => `
            <label>
                <input type="radio" name="answer" value="${answer}">
                ${answer}
            </label><br>
        `).join('');

        document.getElementById('questionBox').innerHTML = `
            <h2>Întrebarea ${questionIndex + 1}:</h2>
            <p>${question.question}</p>
            <form class="answer-form">
                <div class="options">${answersHtml}</div>
                <button type="button" onclick="nextQuestion()">Următoarea întrebare</button>
            </form>
        `;
    }

    function nextQuestion() {
        const selectedAnswer = document.querySelector('input[name="answer"]:checked');
        if (!selectedAnswer) {
            alert("Select an answer!");
            return;
        }

        const userAnswer = selectedAnswer.value;
        const question = questions[questionIndex];
        if (userAnswer === question.correctAnswer) {
            score++;
        }

        questionIndex++;
        if (questionIndex < questions.length) {
            displayQuestion();
        } else {
            showResult();
        }
    }

    function showResult() {
        document.getElementById('questionBox').style.display = 'none';
        document.getElementById('result').innerHTML = `
            <h2>Ai terminat testul!</h2>
            <p>Scorul tau este ${score} / ${questions.length}.</p>
        `;
        document.getElementById('scorInput').value = score;
        document.getElementById('testForm').submit();
    }

    window.onload = function() {
        displayQuestion();
    };
</script>

</body>
</html>
