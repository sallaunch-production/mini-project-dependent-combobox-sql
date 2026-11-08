<?php

// Include the database connection file
include 'database.php';


// ======================================================
// SAVE CUSTOMER
// ======================================================

if (isset($_POST['save'])) {

    $name = trim($_POST['name'] ?? '');
    $province = (int) ($_POST['province_id'] ?? 0);
    $city = (int) ($_POST['city_id'] ?? 0);

    if ($name && $province && $city) {

        // Prepared statement prevents SQL injection
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO customer (name, province_id, city_id) VALUES (?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "sii", $name, $province, $city);
        mysqli_stmt_execute($stmt);
    }

    exit;
}


// ======================================================
// GET CITY BY PROVINCE
// ======================================================

if (isset($_GET['province_id'])) {

    $province_id = (int) $_GET['province_id'];

    $query = mysqli_query(
        $conn,
        "SELECT id, name
         FROM city
         WHERE province_id = $province_id
         ORDER BY name"
    );

    echo '<option value="">-- Select City --</option>';

    while ($row = mysqli_fetch_assoc($query)) {
        $cityName = htmlspecialchars($row['name']);
        echo "<option value='{$row['id']}'>{$cityName}</option>";
    }

    exit;
}


// ======================================================
// GET PROVINCE DATA
// ======================================================

$provinces = mysqli_query(
    $conn,
    "SELECT * FROM province ORDER BY name"
);


// ======================================================
// GET CUSTOMER DATA
// ======================================================

$customers = mysqli_query($conn, "
    SELECT
        c.name,
        p.name AS province,
        ct.name AS city
    FROM customer c
    JOIN province p ON c.province_id = p.id
    JOIN city ct ON c.city_id = ct.id
    ORDER BY c.id
");

$totalCustomers = mysqli_num_rows($customers);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domicili</title>

    <!-- Bootstrap 5 + Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* Soft colored page background */
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #d9f2e6 0%, #e3f0fb 100%);
            background-attachment: fixed;
        }

        /* Card with rounded corners and soft shadow */
        .card-colored {
            border: 0;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(20, 90, 70, .12);
        }

        /* Gradient headers */
        .header-form {
            background: linear-gradient(90deg, #198754, #20c997);
            color: #fff;
        }

        .header-table {
            background: linear-gradient(90deg, #0d6efd, #0dcaf0);
            color: #fff;
        }

        /* Icon box on the left of each input */
        .input-group-text {
            background: #e8f6ef;
            color: #198754;
            border-right: 0;
        }

        .input-group .form-control,
        .input-group .form-select {
            border-left: 0;
        }

        .input-group:focus-within .input-group-text {
            border-color: #86b7fe;
        }

        /* Table header color */
        .table thead th {
            background: #e7f1ff;
            color: #0a58ca;
            border-bottom: 2px solid #9ec5fe;
        }
    </style>
</head>

<body>

    <div class="container py-5" style="max-width: 820px;">

        <!-- Page title -->
        <div class="text-center mb-4">
            <h2 class="fw-bold text-success mb-1">
                <i class="bi bi-geo-alt-fill"></i> Domicili
            </h2>
            <p class="text-secondary mb-0">Record where your customers live.</p>
        </div>


        <!-- ====================================================== -->
        <!-- CUSTOMER FORM CARD -->
        <!-- ====================================================== -->

        <div class="card card-colored mb-4">

            <div class="card-header header-form py-3">
                <h5 class="mb-0">
                    <i class="bi bi-person-plus-fill me-2"></i>Add customer
                </h5>
            </div>

            <div class="card-body p-4">

                <form id="form">

                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="name">Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" id="name" class="form-control" placeholder="Enter your name" required>
                        </div>
                    </div>

                    <!-- Province -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="province">Province</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-map"></i></span>
                            <select id="province" class="form-select" required>
                                <option value="">-- Select Province --</option>

                                <?php while ($row = mysqli_fetch_assoc($provinces)): ?>
                                        <option value="<?= $row['id'] ?>">
                                            <?= htmlspecialchars($row['name']) ?>
                                        </option>
                                <?php endwhile; ?>

                            </select>
                        </div>
                    </div>

                    <!-- City -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="city">City</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-buildings"></i></span>
                            <select id="city" class="form-select" required>
                                <option value="">-- Select City --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2">
                        <button class="btn btn-success px-4" type="submit">
                            <i class="bi bi-check-lg me-1"></i>Save
                        </button>
                        <button class="btn btn-outline-secondary px-4" type="button" onclick="form.reset()">
                            <i class="bi bi-x-lg me-1"></i>Cancel
                        </button>
                    </div>

                </form>

            </div>
        </div>


        <!-- ====================================================== -->
        <!-- CUSTOMER TABLE CARD -->
        <!-- ====================================================== -->

        <div class="card card-colored">

            <div class="card-header header-table py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-people-fill me-2"></i>Customer list
                </h5>
                <span class="badge bg-light text-primary rounded-pill">
                    <?= $totalCustomers ?> total
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead>
                            <tr>
                                <th class="ps-4">No.</th>
                                <th>Name</th>
                                <th>Province</th>
                                <th>City</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php $no = 1; ?>

                            <?php while ($row = mysqli_fetch_assoc($customers)): ?>
                                    <tr>
                                        <td class="ps-4"><?= $no++ ?>.</td>
                                        <td class="fw-semibold"><?= htmlspecialchars($row['name']) ?></td>
                                        <td>
                                            <span class="badge rounded-pill text-bg-success">
                                                <?= htmlspecialchars($row['province']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill text-bg-info">
                                                <?= htmlspecialchars($row['city']) ?>
                                            </span>
                                        </td>
                                    </tr>
                            <?php endwhile; ?>

                            <?php if ($totalCustomers === 0): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-secondary py-4">
                                            No customers yet. Add the first one above.
                                        </td>
                                    </tr>
                            <?php endif; ?>

                        </tbody>

                    </table>
                </div>
            </div>
        </div>

    </div>


    <script>

        const province = document.getElementById('province');
        const city = document.getElementById('city');
        const form = document.getElementById('form');


        // Load cities when the province changes
        province.onchange = function () {

            fetch('basic_test.php?province_id=' + this.value)
                .then(res => res.text())
                .then(data => city.innerHTML = data);

        };


        // Save customer
        form.onsubmit = function (e) {

            e.preventDefault();

            const data = new FormData();
            data.append('save', true);
            data.append('name', document.getElementById('name').value);
            data.append('province_id', province.value);
            data.append('city_id', city.value);

            fetch('basic_test.php', {
                method: 'POST',
                body: data
            })
                .then(() => location.reload());

        };

    </script>

</body>

</html>