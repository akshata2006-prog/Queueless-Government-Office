<?php
require_once __DIR__ . "/config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Please submit the citizen form first.");
}

// Get form details
$full_name = trim($_POST["full_name"] ?? "");
$mobile_number = trim($_POST["mobile_number"] ?? "");
$age_input = trim($_POST["age"] ?? "");

$office_id = filter_var(
    $_POST["office_id"] ?? "",
    FILTER_VALIDATE_INT
);

$service_id = filter_var(
    $_POST["service_id"] ?? "",
    FILTER_VALIDATE_INT
);

// Validate citizen details
if ($full_name === "") {
    exit("Error: Please enter your full name.");
}

if (!preg_match('/^[0-9]{10}$/', $mobile_number)) {
    exit("Error: Mobile number must contain exactly 10 digits.");
}

if (
    $age_input === "" ||
    !ctype_digit($age_input) ||
    (int)$age_input < 1 ||
    (int)$age_input > 120
) {
    exit("Error: Please enter a valid age between 1 and 120.");
}

$age = (int)$age_input;

// Validate office and service IDs
if ($office_id === false || $service_id === false) {
    exit("Error: Please select a valid office and service.");
}

// Confirm that the selected service belongs to the selected office
$stmt = $conn->prepare(
    "SELECT service_name
     FROM services
     WHERE service_id = ? AND office_id = ?"
);

// DEBUG: Check submitted IDs
echo "Received Office ID: " . htmlspecialchars((string)$office_id) . "<br>";
echo "Received Service ID: " . htmlspecialchars((string)$service_id) . "<br>";

$stmt = $conn->prepare(
    "SELECT service_name FROM services WHERE service_id = ? AND office_id = ?"
);

$stmt->bind_param("ii", $service_id, $office_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Service and office IDs do not match in database.";
    $stmt->close();
    exit;
}

$row = $result->fetch_assoc();
$service_name = $row["service_name"];
$stmt->close();

// Start transaction so citizen and token are saved together
$conn->begin_transaction();

try {
    // Save citizen details
    $stmt = $conn->prepare(
        "INSERT INTO citizens (full_name, mobile_number, age)
         VALUES (?, ?, ?)"
    );

    $stmt->bind_param("ssi", $full_name, $mobile_number, $age);
    $stmt->execute();

    $citizen_id = $conn->insert_id;
    $stmt->close();

    // Generate token number
    $prefixes = [
        1 => "MUN",
        2 => "COL",
        3 => "MAM"
    ];

    $prefix = $prefixes[$office_id];

    // Create a unique token number
    $token_number = $prefix . "-" . str_pad(
        (string)$citizen_id,
        4,
        "0",
        STR_PAD_LEFT
    );

    // Save token
    $token_status = "Pending";

    $stmt = $conn->prepare(
        "INSERT INTO tokens
         (token_number, citizen_id, office_id, service_id, token_status)
         VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "siiis",
        $token_number,
        $citizen_id,
        $office_id,
        $service_id,
        $token_status
    );

    $stmt->execute();
    $stmt->close();

    $conn->commit();

    // Display confirmation
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Token Confirmation</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body class='bg-light'>
        <div class='container py-5'>
            <div class='card shadow mx-auto' style='max-width: 550px;'>
                <div class='card-body text-center p-4'>
                    <h2 class='text-success'>Registration Successful!</h2>
                    <p>Your citizen details have been saved.</p>
                    <hr>
                    <h4>Your Token Number</h4>
                    <p><strong>Citizen ID:</strong> " . (int)$citizen_id . "</p>
                    <h1 class='text-primary'>" .
                    htmlspecialchars($token_number, ENT_QUOTES, 'UTF-8') .
                    "</h1>
                    <p><strong>Citizen ID:</strong> " . (int)$citizen_id . "</p>
                    <p><strong>Name:</strong> " .
                    htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8') .
                    "</p>
                    <p><strong>Status:</strong> Pending</p>
                    <p>Please keep your token number for future reference.</p>
                    <a href='index.html' class='btn btn-primary'>Back to Home</a>
                </div>
            </div>
        </div>
    </body>
    </html>";

} catch (Throwable $e) {
    $conn->rollback();

    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }

    error_log($e->getMessage());

    http_response_code(500);
    echo "Unable to generate the token. Please check your database tables and try again.";
}

$conn->close();
?>