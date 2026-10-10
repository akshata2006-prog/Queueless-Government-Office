<?php

$conn = new mysqli("localhost", "root", "", "queueless");

if ($conn->connect_error) {
    die("Database connection failed.");
}

$token = "";
$result = null;
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = trim($_POST["token_number"] ?? "");

    if ($token === "") {

        $error = "Please enter your token number.";

    } else {

        $sql = "SELECT
                    t.token_number,
                    t.token_status,
                    c.full_name
                FROM tokens t
                INNER JOIN citizens c
                    ON t.citizen_id = c.citizen_id
                WHERE t.token_number = ?";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Query error: " . $conn->error);
        }

        $stmt->bind_param("s", $token);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $error = "Token not found. Please check your token number.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Check Token Status | QueueLess</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">

</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-primary">

        <div class="container">

            <a class="navbar-brand fw-bold" href="index.html">
                QueueLess
            </a>

            <a href="index.html" class="btn btn-light">
                Back to Home
            </a>

        </div>

    </nav>

    <div class="container py-5">

        <div class="card shadow mx-auto"
             style="max-width: 600px;">

            <div class="card-body p-4">

                <h2 class="text-center fw-bold mb-3">
                    Check Token Status
                </h2>

                <p class="text-center text-muted">
                    Enter your token number to check your status.
                </p>

                <form method="POST" action="check_status.php">

                    <div class="mb-3">

                        <label for="token_number"
                               class="form-label fw-bold">
                            Token Number
                        </label>

                        <input
                            type="text"
                            id="token_number"
                            name="token_number"
                            class="form-control"
                            placeholder="Example: COL-0003"
                            value="<?php echo htmlspecialchars($token); ?>"
                            required>

                    </div>

                    <button type="submit"
                            class="btn btn-primary w-100">
                        Check Status
                    </button>

                </form>

                <?php if ($error !== ""): ?>

                    <div class="alert alert-danger mt-4">
                        <?php echo htmlspecialchars($error); ?>
                    </div>

                <?php endif; ?>

                <?php if ($result && $result->num_rows > 0): ?>

                    <?php $citizen = $result->fetch_assoc(); ?>

                    <div class="card mt-4 border-success">

                        <div class="card-body">

                            <h4 class="text-center text-success">
                                Token Details
                            </h4>

                            <hr>

                            <p>
                                <strong>Name:</strong>
                                <?php echo htmlspecialchars(
                                    $citizen["full_name"]
                                ); ?>
                            </p>

                            <p>
                                <strong>Token Number:</strong>
                                <?php echo htmlspecialchars(
                                    $citizen["token_number"]
                                ); ?>
                            </p>

                            <p class="mb-0">
                                <strong>Status:</strong>

                                <span class="badge bg-primary">
                                    <?php echo htmlspecialchars(
                                        $citizen["token_status"]
                                    ); ?>
                                </span>
                            </p>

                        </div>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</body>

</html>