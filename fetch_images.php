<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Factory Layouts</title>
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            background-size: cover;
            background-color: #f4f4f4;
            padding: 20px;
            background-image: url('image.jpg');
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        /* CSS for navigation buttons */
        .nav-buttons {
            position: fixed;
            top: 8%;
            left: 20px;
            transform: translateY(-50%);
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            gap: 10px;
        }

        .nav-button {
            background: linear-gradient(to right, #000d9c, #0066cc);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            margin-bottom: 10px;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .nav-button:hover {
            background-color: #45a049;
        }

        /* CSS for the image blocks */
    .image-block {
        display: inline-block;
        margin-top: 20px;
        margin-right: 20px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        padding: 25px;
        border-radius: 10px;
        position: relative;
        /*background-color: #F8D7DA;  Light red */
        background-color: #fff;
        transition: box-shadow 0.3s ease;
        width: calc(33.33% - 20px); /* Adjusted width to fit 3 blocks in a row */
        vertical-align: top;
    }


        .image-block:last-child {
            margin-right: 0;
        }

        .image-block h2 {
            margin-top: 0;
            font-size: 18px;
            color: #333;
            text-align: center;
            margin-bottom: 10px;
        }

        /* CSS for full screen image display */
        .fullscreen {
            position: fixed;
            background-color: rgba(0, 0, 0, 0.9);
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .fullscreen img {
            max-width: 90%;
            max-height: 90%;
        }

        .close-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            cursor: pointer;
            color: #fff;
            font-size: 24px;
        }

        /* Additional CSS for image blocks */
        .click-to-enlarge {
            cursor: pointer;
            transition: transform 0.2s ease-in-out;
        }

        .click-to-enlarge:hover {
            transform: scale(1.1);
        }

        /* CSS for buttons */
        .image-buttons {
            position: absolute;
            bottom: 5px;
            left: 5px;
        }

        /* CSS for delete button */
        .image-buttons a.delete-button {
            background-color: red;
            color: white;
            padding: 5px;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 5px;
        }

        /* CSS for download button */
        .image-buttons a.download-button {
            background-color: green;
            color: white;
            padding: 5px;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 7px;
        }

        /* CSS for success message */
        .success-message {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4CAF50;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            z-index: 9999;
            display: none;
        }
    </style>
</head>
<body>
<div class="nav-buttons">
    <a href="home.html" class="nav-button">Home</a>
    <a href="factory_layout_editor.php" class="nav-button">Layout Editor</a>
</div>
<div class="container">
    <h1>Factory Layouts</h1>
    <?php
    // Establish database connection (replace with your own credentials)
    $servername = "localhost:3309";
    $username = "root";
    $password = "";
    $dbname = "wsn_database";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle delete image action
    if(isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $id = $_GET['id'];
        // Delete the image with the given ID from the database
        $sql = "DELETE FROM factorylayout WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo '<div class="success-message">Record deleted successfully</div>';
            echo "<script>setTimeout(function(){document.querySelector('.success-message').style.display = 'none';}, 5000);</script>";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    }

    // Fetch image data from the database
    $sql = "SELECT id, factoryname, layout_image FROM factorylayout"; // Replace 'factorylayout' with your actual table name
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output the image blocks
        $count = 0;
        while($row = $result->fetch_assoc()) {
            // Display a block for each image
            if ($count % 3 == 0) {
                echo '<div style="clear:both;"></div>'; // Clear the float every 3 blocks
            }
            echo '<div class="image-block">';
            echo '<h2>' . $row["factoryname"] . '</h2>';
            echo '<img class="click-to-enlarge" src="' . $row["layout_image"] . '" alt="' . $row["factoryname"] . '" style="max-width: 100%;">';
            echo '<div class="image-buttons">';
            echo '<a href="' . $row["layout_image"] . '" download="FactoryLayoutImage.png" class="download-button">Download</a>'; // Download link
            echo '<a href="?action=delete&id=' . $row["id"] . '" onclick="return confirm(\'Are you sure you want to delete this image?\')" class="delete-button">Delete</a>'; // Delete link
            echo '</div>';
            echo '</div>';
            $count++;
        }
    } else {
        echo "0 results";
    }

    $conn->close();
    ?>
    <!-- Full screen image display container -->
    <div class="fullscreen" id="fullscreen-container">
        <span class="close-icon">&times;</span>
        <img src="" id="fullscreen-image" alt="Fullscreen Image">
    </div>

    <!-- Success message -->
    <div class="success-message">Record deleted successfully</div>
</div>

<script>
    // JavaScript to handle full screen image display
    document.addEventListener('DOMContentLoaded', function () {
        const clickToEnlarge = document.querySelectorAll('.click-to-enlarge');

        clickToEnlarge.forEach(img => {
            img.addEventListener('click', function () {
                const fullscreenContainer = document.getElementById('fullscreen-container');
                const fullscreenImage = document.getElementById('fullscreen-image');
                fullscreenImage.src = img.src;
                fullscreenContainer.style.display = 'flex';
            });
        });

        // Close full screen image when clicked on the close icon
        const closeIcon = document.querySelector('.close-icon');
        closeIcon.addEventListener('click', function () {
            const fullscreenContainer = document.getElementById('fullscreen-container');
            fullscreenContainer.style.display = 'none';
        });

        // Close full screen image when clicked outside the image or close icon
        const fullscreenContainer = document.getElementById('fullscreen-container');
        fullscreenContainer.addEventListener('click', function (event) {
            if (event.target === fullscreenContainer) {
                fullscreenContainer.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>