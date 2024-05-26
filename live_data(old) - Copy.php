<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="refresh" content="5">
    <title>Sensor Data</title>
    <style>
        body {
            background: #ffffff;
            background-image: url('image.jpg');
            background-size: cover;
            box-sizing: border-box;
            color: #000;
            font-size: 1.8rem;
            letter-spacing: -0.015em;
            text-align: center;
        }

        table {
            margin-left: auto;
            margin-right: auto;
            width: 80%;
        }

        th {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 20px;
            background: linear-gradient(to right, #000d9c, #0066cc);
            color: #ffffff;
            padding: 2px 6px;
            border-collapse: separate;
            border: 1px solid #000;
        }

        td {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 15px;
            text-align: center;
            border: 1px solid #ffffff;
        }

        .alert {
            background-color: #ffcccc;
            border: 1px solid #ff0000;
            color: #ff0000;
            padding: 10px;
            margin: 10px auto;
            width: 50%;
            border-radius: 5px;
        }

        #homeButton {
            background: linear-gradient(to right, #000d9c, #0066cc);
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            position: absolute;
            top: 50px;
            left: 10px;
        }

        #homeButton:hover {
            background-color: #45a049;
        }

        #stopButton {
            background-color: #ff0000;
            border: none;
            color: white;
            padding: 10px 10px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            position: absolute;
            top: 100px;
            left: 10px;
        }

        #stopButton:hover {
            background-color: #cc0000;
        }

    </style>
</head>

<body>

    <h1>SENSOR DATA</h1>

    <audio id="alertSound">
        <source src="alert.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>

    <a id="homeButton" href="home.html">Home</a>

    <button id="stopButton" onclick="toggleAlert()">Stop Alert</button>

    <table cellspacing="5" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Temperature &deg;C</th>
            <th>Humidity &#37;</th>
            <th>mac address</th>
            <th>Date & Time</th>
        </tr>

        <?php
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

        $sql = "SELECT id, temperature, humidity, mac_address, timestamp FROM sensor_data ORDER BY id DESC";

        if ($result = $conn->query($sql)) {
            while ($row = $result->fetch_assoc()) {
                $row_id = $row["id"];
                $row_value1 = $row["temperature"];
                $row_value2 = $row["humidity"];
                $row_value3 = $row["mac_address"];
                $row_reading_time = $row["timestamp"];

                if ($row_value1 >= 40) {
                    echo '<tr class="alert">
                            <td colspan="5">Temperature is beyond threshold!</td>
                          </tr>';
                }

                echo '<tr>
                        <td>' . $row_id . '</td>
                        <td>' . $row_value1 . '</td>
                        <td>' . $row_value2 . '</td>
                        <td>' . $row_value3 . '</td>
                        <td>' . $row_reading_time . '</td>
                      </tr>';
            }
            $result->free();
        }

        $conn->close();
        ?>
    </table>

    <script>
        var isAlertPlaying = false; // Variable to track if alert is currently playing

        // Function to toggle alert sound
        function toggleAlert() {
            var alertSound = document.getElementById("alertSound");

            if (isAlertPlaying) {
                alertSound.pause();
                alertSound.currentTime = 0;
                isAlertPlaying = false;
            } else {
                alertSound.play();
                isAlertPlaying = true;
            }
        }

        // Check if temperature exceeds threshold and play alert sound initially
        var rows = document.querySelectorAll(".alert");
        if (rows.length > 0) {
            toggleAlert();
        }
    </script>

</body>

</html>
