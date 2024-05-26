<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="refresh" content="5" >
    <title>Graph Data</title>
    <!-- Include Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 50px;
        }

        #sensorChart {
            width: 800px;
            height: 400px;
        }

        .header-container {
    display: flex;
    align-items: center;
    justify-content: center; /* Center horizontally */
    width: 800px; /* Adjust as needed */
    margin: 0 auto; /* Center horizontally */
    margin-bottom: 20px;
}



        h1 {
            margin: 0;
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
    top: 50px; /* Adjust top position */
    left: 10px; /* Adjust left position */
}

        /* Adjust button style on hover */
        #homeButton:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<a id="homeButton" href="home.html">Home</a>
    <div class="header-container">
    <h1>Real-Time Sensor Graph</h1>
        
    </div>
    <canvas id="sensorChart"></canvas>

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

    $sql = "SELECT temperature, humidity, timestamp FROM sensor_data ORDER BY id DESC LIMIT 10"; /*select items to display from the sensordata table in the database*/

    // Arrays to store temperature, humidity, and timestamp data
    $temperatures = array();
    $humidities = array();
    $timestamps = array();

    if ($result = $conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $temperatures[] = $row["temperature"];
            $humidities[] = $row["humidity"];
            $timestamps[] = $row["timestamp"];
        }
        $result->free();
    }

    $conn->close();
    ?>

    <script>
        // Get temperature, humidity, and timestamp data from PHP and convert to JavaScript arrays
        var temperatures = <?php echo json_encode($temperatures); ?>;
        var humidities = <?php echo json_encode($humidities); ?>;
        var timestamps = <?php echo json_encode($timestamps); ?>;

        // Chart.js configuration
        var ctx = document.getElementById('sensorChart').getContext('2d');
        var sensorChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($timestamps); ?>, // Use timestamps as labels
                datasets: [{
                    label: 'Temperature (°C)',
                    data: temperatures,
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }, {
                    label: 'Humidity (%)',
                    data: humidities,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>
</body>
</html>
