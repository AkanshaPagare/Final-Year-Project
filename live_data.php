<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="refresh" content="10">
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

        .alert-message {
            font-size: 12px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 5px;
            margin-bottom: 5px;
        }

        .alert-below {
            background-color: #FAFF93;
            color: #f00;
        }

        .alert-above {
            background-color: #ff9999;
            color: #f00;
        }

        .button {
            background: linear-gradient(to right, #000d9c, #0066cc);
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        .button:hover {
            background-color: #45a049;
        }

        #homeButton {
            position: absolute;
            top: 100px;
            left: 10px;
            width: 50px;
        }

        #stopButton {
            background-color: #ff0000;
            border: none;
            color: white;
            padding: 10px 10px;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            position: absolute;
            top: 200px;
            left: 10px;
            width: 90px;
        }

        #stopButton:hover {
            background-color: #cc0000;
        }

        #downloadCsv {
            background: linear-gradient(to right, #000d9c, #0066cc);
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            position: absolute;
            top: 150px;
            left: 10px;
            width: 90px;
            height: 40px;
        }

        #downloadCsv:hover {
            background-color: #45a049;
        }

        .marquee {
            overflow: hidden;
            white-space: nowrap;
            width: 100%;
            background-color: #e6e6e6;
            padding: 10px 0;
            border-bottom: 2px solid #ccc;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 40px;
            font-weight: bold;
            color: #333;
        }

        .marquee span {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 20s linear infinite;
            color: #0066cc;
        }

        @keyframes marquee {
            0% {
                transform: translate(-100%, 0);
            }

            100% {
                transform: translate(100%, 0);
            }
        }
    </style>
</head>

<body>
    <!-- Marquee and other elements -->
    <div class="marquee">
        <span>CEAT Tyres Sensor Data</span>
    </div>

    <!-- Temperature Limit Form -->
    <div>
        <form id="tempForm">
            <label for="minTemp">Minimum Temperature:</label>
            <input type="number" id="minTemp" name="minTemp" required>

            <label for="maxTemp">Maximum Temperature:</label>
            <input type="number" id="maxTemp" name="maxTemp" required>

            <button type="button" onclick="setTemperatureLimits()">Set Limits</button>
        </form>
    </div>

    <audio id="alertSound" src="alert.mp3" loop></audio>

    <a id="homeButton" class="button" href="home.html">Home</a>

    <button id="stopButton" onclick="toggleAlert()">Stop Alert</button>

    <button id="downloadCsv" onclick="downloadCSV()">Download CSV</button>

    <table cellspacing="5" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Temperature &deg;C</th>
            <th>Humidity &#37;</th>
            <th>Sensor Name</th>
            <th>mac address</th>
            <th>Date & Time</th>
        </tr>

        <?php
        if (!isset($_POST["minTemp"]) && !isset($_POST["maxTemp"])) {
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

            $sql = "SELECT id, temperature, humidity, mac_address, timestamp FROM sensor_data ORDER BY id DESC LIMIT 20";

            if ($result = $conn->query($sql)) {
                while ($row = $result->fetch_assoc()) {
                    $row_id = $row["id"];
                    $row_value1 = $row["temperature"];
                    $row_value2 = $row["humidity"];
                    $mac_address = $row["mac_address"];
                    $row_reading_time = $row["timestamp"];

                    // Determine sensor name based on MAC address
                    $sensor_name = "";
                    if ($mac_address === "48:55:19:C1:AC:D2") {
                        $sensor_name = "Duplex 01 Panel Room Division A";
                    } elseif ($mac_address === "48:55:19:C8:50:CD") {
                        $sensor_name = "Four Roll Panel Room Division A";
                    }

                    echo '<tr data-alert-played="false">
                            <td>' . $row_id . '</td>
                            <td>' . $row_value1 . '</td>
                            <td>' . $row_value2 . '</td>
                            <td>' . $sensor_name . '</td>
                            <td>' . $mac_address . '</td>
                            <td>' . $row_reading_time . '</td>
                          </tr>';
                }
                $result->free();
            }

            $conn->close();
        }
        ?>
    </table>

    <script>
        var isAlertPlaying = false;
        var alertSound = document.getElementById("alertSound");

        // Retrieve temperature limits from localStorage on page load
        var minTemp = localStorage.getItem("minTemp");
        var maxTemp = localStorage.getItem("maxTemp");

        if (minTemp) {
            document.getElementById("minTemp").value = minTemp;
        }

        if (maxTemp) {
            document.getElementById("maxTemp").value = maxTemp;
        }

        function setTemperatureLimits() {
            var minTempValue = parseFloat(document.getElementById("minTemp").value);
            var maxTempValue = parseFloat(document.getElementById("maxTemp").value);

            // Store temperature limits in localStorage
            localStorage.setItem("minTemp", minTempValue);
            localStorage.setItem("maxTemp", maxTempValue);

            // Display alert message above the table
            var alertAboveMessage = document.getElementById("alertAboveMessage");
            alertAboveMessage.textContent = "Temperature thresholds set: Min " + minTempValue + "°C, Max " + maxTempValue + "°C";
            alertAboveMessage.className = "alert-red";

            // Start alert sound if not already playing
            if (!isAlertPlaying) {
                alertSound.play();
                isAlertPlaying = true;
            }

            // Clear alert message after 5 seconds
            setTimeout(function () {
                alertAboveMessage.textContent = "";
            }, 5000);
        }

        function toggleAlert() {
            var stopButton = document.getElementById("stopButton");
            if (isAlertPlaying) {
                alertSound.pause();
                isAlertPlaying = false;
                // Remove event listener to prevent further clicks
                stopButton.removeEventListener("click", toggleAlert);
                stopButton.textContent = "Start Alert";
                console.log("Alert sound stopped");
            } else {
                alertSound.play();
                isAlertPlaying = true;
                // Add event listener back after starting the alert sound
                stopButton.addEventListener("click", toggleAlert);
                stopButton.textContent = "Stop Alert";
                console.log("Alert sound started");
            }
        }

        // Function to play alert sound only for new inserted values
        function playAlert(row) {
            var alertPlayed = row.getAttribute("data-alert-played");
            if (alertPlayed === "false") {
                alertSound.play();
                row.setAttribute("data-alert-played", "true");
            }
        }

        // Check temperature limits and display alert messages below the table
        if (minTemp && maxTemp) {
            var currentMinTemp = parseFloat(minTemp);
            var currentMaxTemp = parseFloat(maxTemp);

            var rows = document.querySelectorAll("table tr:not(:first-child)");

            rows.forEach(function (row, index) {
                if (index < 10) {
                    var cells = row.querySelectorAll("td");
                    var temperature = parseFloat(cells[1].textContent);

                    if (temperature < currentMinTemp) {
                        var alertBelowMessage = document.createElement("div");
                        alertBelowMessage.textContent = "Temperature is below the threshold value " + currentMinTemp + "°C";
                        alertBelowMessage.className = "alert-message alert-below";
                        cells[1].appendChild(alertBelowMessage);
                        playAlert(row);
                    } else if (temperature > currentMaxTemp) {
                        var alertBelowMessage = document.createElement("div");
                        alertBelowMessage.textContent = "Temperature is above the threshold value " + currentMaxTemp + "°C";
                        alertBelowMessage.className = "alert-message alert-above";
                        cells[1].appendChild(alertBelowMessage);
                        playAlert(row);
                    }
                }
            });

            // Disable "Stop Alert" button if temperature is within the appropriate range
            var stopButton = document.getElementById("stopButton");
            if (temperature >= currentMinTemp && temperature <= currentMaxTemp) {
                stopButton.disabled = true;
            } else {
                stopButton.disabled = false;
            }
        }

        function downloadCSV() {
            var csv = [];
            var rows = document.querySelectorAll("table tr");

            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");

                for (var j = 0; j < cols.length; j++)
                    row.push(cols[j].innerText);

                csv.push(row.join(","));
            }

            var csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "sensor_data.csv");
            document.body.appendChild(link); // Required for FF

            link.click();
        }
    </script>

</body>

</html>
