```html
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Citizen Details | QueueLess</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">

            <a class="navbar-brand fw-bold" href="index.html">
                QueueLess
            </a>

            <a href="services.html" class="btn btn-light">
                Back
            </a>

        </div>
    </nav>

    <!-- Heading -->
    <section class="container text-center py-4">

        <h1 class="fw-bold">Citizen Details</h1>

        <p class="text-muted">
            Please enter your details to request a digital token.
        </p>

    </section>

    <!-- Citizen Form -->
    <section class="container">

        <div class="form-card">

            <form id="citizenForm" action="save_citizen.php" method="POST">

                <!-- Hidden Office ID -->
                <input
                    type="hidden"
                    name="office_id"
                    id="office_id">

                <!-- Hidden Service ID -->
                <input
                    type="hidden"
                    name="service_id"
                    id="service_id">

                <!-- Full Name -->
                <div class="mb-3">

                    <label for="name" class="form-label fw-bold">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="full_name"
                        class="form-control"
                        placeholder="Enter your full name"
                        maxlength="100"
                        required>

                </div>

                <!-- Mobile Number -->
                <div class="mb-3">

                    <label for="mobile" class="form-label fw-bold">
                        Mobile Number
                    </label>

                    <input
                        type="tel"
                        id="mobile"
                        name="mobile_number"
                        class="form-control"
                        placeholder="Enter 10-digit mobile number"
                        pattern="[0-9]{10}"
                        maxlength="10"
                        inputmode="numeric"
                        required>

                </div>

                <!-- Age -->
                <div class="mb-4">

                    <label for="age" class="form-label fw-bold">
                        Age
                    </label>

                    <input
                        type="number"
                        id="age"
                        name="age"
                        class="form-control"
                        placeholder="Enter your age"
                        min="1"
                        max="120"
                        required>

                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="btn btn-success w-100">

                    Generate Token 🎫

                </button>

            </form>

        </div>

    </section>

    <!-- Footer -->
    <footer class="text-center py-4 mt-5">

        <p class="mb-0">
            QueueLess Government Office | Hackathon Project
        </p>

    </footer>

    <!-- JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            // Office name and ID mapping
            const officeMap = {
                municipal: 1,
                collector: 2,
                mamlatdar: 3
            };

            // Service name and ID mapping
            const serviceMap = {
                birth: 1,
                income: 2,
                caste: 3,
                residence: 4
            };

            // Get selected office and service
            const selectedOffice =
                localStorage.getItem("selectedOffice");

            const selectedService =
                localStorage.getItem("selectedService");

            // Get hidden form fields
            const officeField =
                document.getElementById("office_id");

            const serviceField =
                document.getElementById("service_id");

            // Set office ID
            officeField.value = officeMap[selectedOffice] || "";

            // Set service ID
            serviceField.value = serviceMap[selectedService] || "";

            // Debugging information
            console.log("Selected office:", selectedOffice);
            console.log("Selected service:", selectedService);
            console.log("Office ID:", officeField.value);
            console.log("Service ID:", serviceField.value);

        });
    </script>

    <script src="js/script.js"></script>

</body>

</html>
```
