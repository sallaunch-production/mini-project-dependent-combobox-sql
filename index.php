<?php

include 'database.php';

// return city options based on selected province
if (isset($_GET['id'])) {

    // get selected province id
    $id = (int) $_GET['id'];

    // retrieve city data based on selected province
    $query = mysqli_query(
        $conn,
        "SELECT id, name FROM city
            WHERE province_id = $id"
    );

    // generate city options list
    while ($row = mysqli_fetch_assoc($query)) {

        echo "<option value='{$row['id']}'>
                    {$row['name']}
                </option>";

        //stop rendering HTML and return AJAX response only    
        exit;
    }
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Double Combo Box</title>
</head>

<body>

    <br><br>
    <label>Province:</label>
    <select id="province">
        <option value="">-- select province --</option>

        <?php

        // Load Province
        $query = mysqli_query(
            $conn,
            "SELECT * FROM province ORDER BY name"
        );
        ?>

        <?php
        // Generate option province dari database 
        while ($row = mysqli_fetch_assoc($query)):
            ?>

            //show province as an option
            <option value="<?= $row['id'] ?>">
                <?= htmlspecialchars($row['name']) ?>
            </option>

            <?php
            //finish looping
        endwhile;
        ?>

    </select>

    <br><br>

    <!-- City -->

    <label>City:</label>

    <select id="city">

        <option value="">
            -- Select City --
        </option>

    </select>

    <script>
        // province dropdown change event    
        document.getElementById('province').addEventListener('change', function () {

            // get selected province id
            const id = this.value;

            // request city data by AJAX
            fetch('index.php?id=' + id)
                .then(response => response.text())

                // Populate city dropdown
                .then(html => {
                    document.getElementById('city').innerHTML = html;
                });

        // handle req error
        .catch (error => {
                console.error(error);
            })

    });
    </script>

</body>

</html>