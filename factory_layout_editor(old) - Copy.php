<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connection Editor</title>
    <style>
        /* CSS styles */
        h1 {
            text-align: center;
        }

        body {
    font-family: 'Times New Roman', Times, serif;
    background-image: url('image.jpg');
    background-size: cover;
    margin: 0;
    padding: 0;
    position: relative;
}


        header, footer {
            text-align: center;
            padding: 20px 0;
            background: linear-gradient(to right, #000d9c, #0066cc);
            color: #fff;
        }

        #canvas-container {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            margin-top: 20px;
        }

        #canvas {
            width: 1000px;
            height: 600px;
            border: 1px solid #ccc;
            margin-left: 50px;
            margin-right: 100px;
        }

        #left-buttons-container {
            display: flex;
            flex-direction: row;
            margin-left: 50px;
        }

        #bottom-buttons-container {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }

        #right-buttons-container {
            margin-top: 20px;
            margin-left: 10px;
            margin-right: 50px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .toolButton {
            height: 40px;
            margin-bottom: 10px;
            margin-right: 15px;
            padding: 4px 16px;
            font-size: 16px;
            background: linear-gradient(to right, #000d9c, #0066cc);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .toolButton:hover {
            background-color: #45a049;
        }

        .dropdown {
            position: relative;
            margin-bottom: 10px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #000000ba;
            min-width: 160px;
            box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: rgb(255, 255, 255);
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #ddd;
            color: rgba(0, 0, 0, 0.958);
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .stoke-select {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .stoke-select label {
            margin-right: 10px;
            margin-top: 10px;
        }

        .stoke-select input[type="number"] {
            width: 50px;
        }

        .toolButton.selected {
            background-color: #f44336;
        }

        .highlighted {
            border: 10px solid yellow;
        }

        .node-info {
            position: absolute;
            background-color: white;
            border: 1px solid black;
            padding: 5px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Connection Editor</h1>
    </header>
    <div id="canvas-container">
        <div id="left-buttons-container">
            <button class="toolButton" id="homeButton" onclick="window.location.href='home.html'">Home</button>
            <div class="dropdown">
                <button class="toolButton">File Menu</button>
                <div class="dropdown-content">
                    <a href="factory_layout_editor.php" id="newFile">New File</a>
                    <a href="#" id="saveFile">Save File</a>
					
                    <a href="factory_layout_editor.php" id="downloadFile">Download File</a>
                    <a href="factory_layout_editor.php" id="deleteFile">Delete File</a>
                </div>
            </div>
        </div>
        <canvas id="canvas"></canvas>
        <div id="right-buttons-container">
            <button class="toolButton" id="sensorNodeButton">Sensor Node</button>
            <button class="toolButton" id="coordinatorNodeButton">Coordinator Node</button>
            <button class="toolButton" id="repeaterNodeButton">Repeater Node</button>
            <div class="stoke-select">
                <label for="strokeColor">Stroke Color:</label>
                <input type="color" id="strokeColor" value="#000000">
                <label for="strokeWidth">Stroke Width:</label>
                <input type="number" id="strokeWidth" value="2" min="1">
            </div>
        </div>
    </div>
    <div id="bottom-buttons-container">
        <input type="file" id="imageInput">
        <button class="toolButton" id="insertImage">Insert Image</button>
        <button class="toolButton" id="connection">Connections</button>
        <button class="toolButton" id="erase">Erase</button>
        <button class="toolButton" id="deleteAll">Delete All</button>
    </div>

    <script>
        // JavaScript code
        let isDrawingConnection = false;
        let isErasing = false;
        let isErasingNodes = false;
        let canvas;
        let context;
        let nodes = [];
        let connections = [];
        let imageUploaded = false;
        let img;
        let strokeColor = '#000000';
        let strokeWidth = 2;
        let currentColor = strokeColor;
        let selectedNode = null;
        let isDragging = false;
        let dragOffsetX = 0;
        let dragOffsetY = 0;
        let firstSelectedNode = null;
        let secondSelectedNode = null;

        window.onload = function () {
            canvas = document.getElementById('canvas');
            context = canvas.getContext('2d');
            canvas.width = 1000;
            canvas.height = 600;

            // Event listeners
            document.getElementById('insertImage').addEventListener('click', function () {
                document.getElementById('imageInput').click();
            });

            document.getElementById('imageInput').addEventListener('change', function (event) {
                const file = event.target.files[0];
                const reader = new FileReader();
                reader.onload = function (e) {
                    img = new Image();
                    img.src = e.target.result;
                    img.onload = function () {
                        context.drawImage(img, 0, 0, canvas.width, canvas.height);
                        imageUploaded = true;
                    }
                }
                reader.readAsDataURL(file);
            });

            document.getElementById('erase').addEventListener('click', function () {
                isErasing = !isErasing;
                isErasingNodes = isErasing;
                document.getElementById('erase').classList.toggle('selected');
            });

            document.getElementById('deleteAll').addEventListener('click', function () {
                if (confirm("Are you sure you want to delete all drawings?")) {
                    clearCanvas();
                }
            });

            document.getElementById('newFile').addEventListener('click', function () {
                if (confirm("Are you sure you want to create a new file?")) {
                    clearCanvas();
                    clearImageInput();
                }
            });

            document.getElementById('deleteFile').addEventListener('click', function () {
                if (confirm("Are you sure you want to delete the file?")) {
                    clearCanvas();
                    clearImageInput();
                    clearImage();
                }
            });

            document.getElementById('downloadFile').addEventListener('click', function () {
                downloadCanvas();
            });

            document.getElementById('strokeColor').addEventListener('change', function (event) {
                strokeColor = event.target.value;
                currentColor = strokeColor;
            });

            document.getElementById('strokeWidth').addEventListener('change', function (event) {
                strokeWidth = parseInt(event.target.value);
            });

            document.getElementById('sensorNodeButton').addEventListener('click', function () {
                selectedNode = 'sensor';
            });

            document.getElementById('coordinatorNodeButton').addEventListener('click', function () {
                selectedNode = 'coordinator';
            });

            document.getElementById('repeaterNodeButton').addEventListener('click', function () {
                selectedNode = 'repeater';
            });

            document.getElementById('connection').addEventListener('click', function () {
                isDrawingConnection = true;
                if (firstSelectedNode && secondSelectedNode) {
                    alert('You have already selected two nodes for connection.');
                } else {
                    alert('Please select the two nodes by clicking on them.');
                }
            });
// Event listener for the save button
/*
document.getElementById('saveFile').addEventListener('click', function () {
    saveImageToDatabase();
}); */
document.getElementById('saveFile').addEventListener('click', function () {
    const fileName = prompt("Enter file name:");
    if (fileName !== null && fileName.trim() !== "") {
        saveImageToDatabase(fileName); // Pass the filename to the saveImageToDatabase function
    } else {
    
        alert("File name cannot be empty.");
    }
    saveToFile();
});

function saveImageToDatabase(fileName) {
    // Get the canvas image data
    const imageData = canvas.toDataURL();

    // Send the image data and filename to the PHP script
    fetch('save_image.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'imageData=' + encodeURIComponent(imageData) + '&filename=' + encodeURIComponent(fileName),
    })
    .then(response => response.text())
    .then(data => {
        alert('File saved successfully!');
    });
}



function saveToFile() {
    const data = {
        nodes: nodes,
        connections: connections
    };

    fetch('factory_layout_editor.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to save data');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            console.log('Data saved successfully:', data);
            alert('Data saved successfully!');
        } else {
            throw new Error('Failed to save data');
        }
    



    })
    //window.location.href = 'factory_layout_editor.php';
}


            canvas.addEventListener('dblclick', function (event) {
                const rect = canvas.getBoundingClientRect();
                const mouseX = event.clientX - rect.left;
                const mouseY = event.clientY - rect.top;
                const clickedNode = getNodeAtPosition(mouseX, mouseY);
                if (clickedNode) {
                    const nodeName = prompt("Enter the name of the node:");
                    if (nodeName !== null && nodeName !== "") {
                        const nodeType = prompt("Enter the type of the node (Temperature/Humidity):");
                        if (nodeType !== null && (nodeType === "Temperature" || nodeType === "Humidity")) {
                            clickedNode.name = nodeName;
                            clickedNode.type = nodeType;
                        } else {
                            alert("Invalid node type. Please enter 'Temperature' or 'Humidity'.");
                        }
                    }
                }
            });

            canvas.addEventListener('mousedown', function (event) {
                if (!imageUploaded) return;
                const rect = canvas.getBoundingClientRect();
                const mouseX = event.clientX - rect.left;
                const mouseY = event.clientY - rect.top;

                if (isDrawingConnection) {
                    const clickedNode = getNodeAtPosition(mouseX, mouseY);
                    if (clickedNode) {
                        if (!firstSelectedNode) {
                            firstSelectedNode = clickedNode;
                        } else {
                            secondSelectedNode = clickedNode;
                            drawConnection(firstSelectedNode, secondSelectedNode);
                            connections.push([firstSelectedNode, secondSelectedNode]);
                            firstSelectedNode = null;
                            secondSelectedNode = null;
                            isDrawingConnection = false;
                        }
                    }
                } else if (selectedNode && !isErasing) {
                    insertNode(mouseX, mouseY, selectedNode);
                    redrawCanvas();
                } else if (isErasing) {
                    const nodeIndex = getNodeIndexAtPosition(mouseX, mouseY);
                    const connIndex = getConnectionIndexAtPosition(mouseX, mouseY);
                    if (nodeIndex !== -1) {
                        nodes.splice(nodeIndex, 1);
                    }
                    if (connIndex !== -1) {
                        connections.splice(connIndex, 1);
                    }
                    redrawCanvas();
                } else {
                    const clickedNode = getNodeAtPosition(mouseX, mouseY);
                    if (clickedNode) {
                        isDragging = true;
                        selectedNode = clickedNode;
                        dragOffsetX = mouseX - selectedNode.x;
                        dragOffsetY = mouseY - selectedNode.y;
                    } else {
                        selectedNode = null;
                        redrawCanvas();
                    }
                }
            });

            canvas.addEventListener('mousemove', function (event) {
                if (!imageUploaded) return;
                const rect = canvas.getBoundingClientRect();
                const mouseX = event.clientX - rect.left;
                const mouseY = event.clientY - rect.top;

                if (isDragging && selectedNode) {
                    selectedNode.x = mouseX - dragOffsetX;
                    selectedNode.y = mouseY - dragOffsetY;
                    moveConnections(selectedNode);
                    redrawCanvas();
                } else {
                    const hoveredNode = getNodeAtPosition(mouseX, mouseY);
                    if (hoveredNode) {
                        showNodeInfo(hoveredNode, mouseX, mouseY); // Pass mouse coordinates to showNodeInfo function
                    } else {
                        hideNodeInfo();
                    }
                }
            });

            canvas.addEventListener('mouseup', function () {
                isDragging = false;
                selectedNode = null;
                hideNodeInfo(); // Hide node info on mouseup
            });
        };

        let nodeIdCounter = 1;

function insertNode(x, y, type) {
    let color;
    switch (type) {
        case 'sensor':
            color = 'red';
            break;
        case 'coordinator':
            color = 'green';
            break;
        case 'repeater':
            color = 'blue';
            break;
        default:
            color = 'black';
    }

    const id = generateSequentialId();
    const node = { id: id, x: x, y: y, type: type, color: color };
    nodes.push(node);
}

function generateSequentialId() {
    return nodeIdCounter++;
}



        function drawNodes() {
            for (const node of nodes) {
                drawNode(node.x, node.y, node.color, node === selectedNode);
            }
        }

        function drawNode(x, y, color, highlighted = false) {
            context.beginPath();
            context.arc(x, y, 10, 0, Math.PI * 2);
            if (highlighted) {
                context.strokeStyle = 'yellow';
                context.lineWidth = 2;
                context.stroke();
            }
            context.fillStyle = color;
            context.fill();
            context.closePath();
        }

        function clearCanvas() {
            context.clearRect(0, 0, canvas.width, canvas.height);
            nodes = [];
            connections = [];
            redrawCanvas();
        }

        function clearImageInput() {
            imageUploaded = false;
            document.getElementById('imageInput').value = '';
        }

        function clearImage() {
            context.clearRect(0, 0, canvas.width, canvas.height);
            imageUploaded = false;
        }

        function redrawCanvas() {
            context.clearRect(0, 0, canvas.width, canvas.height);
            if (imageUploaded) {
                context.drawImage(img, 0, 0, canvas.width, canvas.height);
            }
            drawNodes();
            redrawConnections();
        }

        function getNodeAtPosition(x, y) {
            for (const node of nodes) {
                const distance = Math.sqrt(Math.pow(x - node.x, 2) + Math.pow(y - node.y, 2));
                if (distance <= 10) {
                    return node;
                }
            }
            return null;
        }

        function getNodeIndexAtPosition(x, y) {
            for (let i = 0; i < nodes.length; i++) {
                const node = nodes[i];
                const distance = Math.sqrt(Math.pow(x - node.x, 2) + Math.pow(y - node.y, 2));
                if (distance <= 10) {
                    return i;
                }
            }
            return -1;
        }

        function getConnectionIndexAtPosition(x, y) {
            for (let i = 0; i < connections.length; i++) {
                const conn = connections[i];
                const [node1, node2] = conn;
                const x1 = node1.x;
                const y1 = node1.y;
                const x2 = node2.x;
                const y2 = node2.y;
                const dist = Math.abs((y2 - y1) * x - (x2 - x1) * y + x2 * y1 - y2 * x1) / Math.sqrt(Math.pow(y2 - y1, 2) + Math.pow(x2 - x1, 2));
                if (dist <= 5) {
                    return i;
                }
            }
            return -1;
        }

        function drawConnection(node1, node2) {
            context.beginPath();
            context.moveTo(node1.x, node1.y);
            context.lineTo(node2.x, node2.y);
            context.strokeStyle = currentColor;
            context.lineWidth = strokeWidth;
            context.stroke();
            context.closePath();
        }

        function redrawConnections() {
            for (const conn of connections) {
                drawConnection(conn[0], conn[1]);
            }
        }

        function moveConnections(node) {
            for (const conn of connections) {
                if (conn[0] === node || conn[1] === node) {
                    redrawCanvas();
                }
            }
        }

        function downloadCanvas() {
            const link = document.createElement('a');
            link.download = 'canvas_image.png';
            link.href = canvas.toDataURL();
            link.click();
        }

        function showNodeInfo(node, mouseX, mouseY) {
            let infoBox = document.getElementById('nodeInfo');
            if (!infoBox) {
                infoBox = document.createElement('div');
                infoBox.id = 'nodeInfo';
                infoBox.classList.add('node-info'); // Add 'node-info' class for styling
                document.body.appendChild(infoBox);
            }
            // Position the info box near the node
            infoBox.style.top = `${mouseY + 150}px`; // Adjust top position near the node
            infoBox.style.left = `${mouseX + 250}px`; // Adjust left position near the node
            infoBox.innerHTML = `Node ID: ${node.id}<br>Name: ${node.name || 'Unnamed'}<br>Type: ${node.type || 'Unknown'}`;
        }

        function hideNodeInfo() {
            const infoBox = document.getElementById('nodeInfo');
            if (infoBox) {
                infoBox.parentNode.removeChild(infoBox);
            }
        }
    </script>
  


<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

// Function to generate a random MAC address
function getMacAddress() {
    $output = '';
    $macAddress = '';

    // Execute a command to get the MAC address based on the operating system
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // For Windows
        exec("ipconfig /all", $output);
        // Search for the line containing 'Physical Address'
        foreach ($output as $line) {
            if (preg_match('/Physical Address/', $line)) {
                $macAddress = substr($line, strpos($line, ":") + 2);
                break;
            }
        }
    } else {
        // For Unix-like systems (Linux, macOS)
        exec("/sbin/ifconfig", $output);
        $regex = '/([0-9a-f]{2}(?::[0-9a-f]{2}){5})/i';
        // Search for the line containing 'ether' (for Linux/macOS)
        foreach ($output as $line) {
            if (preg_match('/ether/', $line)) {
                preg_match($regex, $line, $matches);
                $macAddress = $matches[1];
                break;
            }
        }
    }

    // Return the MAC address
    return $macAddress;
}

// Decode the JSON data sent from JavaScript
$data = json_decode(file_get_contents('php://input'), true);

// Check if data is received properly
if (!$data || !isset($data['nodes']) || !isset($data['connections'])) 
{
    die(" ");
}

// Insert nodes data into network_devices table
foreach ($data['nodes'] as $node) {
    $id = $node['id'];
    $device_type = $conn->real_escape_string($node['type']);
    $device_name = $conn->real_escape_string($node['name']);
    $mac_address = getMacAddress(); // Generate MAC address
    $sql = "INSERT INTO network_devices (id, device_type, device_name, mac_address) VALUES ('$id', '$device_type', '$device_name', '$mac_address')";
    if ($conn->query($sql) !== TRUE) {
        die("Error inserting node data: " . $conn->error);
    }
}

// Insert connections data into network_devices table
foreach ($data['connections'] as $connection) {
    $node_id = $connection[0]['id'];
    $connected_to_id = $connection[1]['id'];
    $sql = "UPDATE network_devices SET connection_to = '$connected_to_id' WHERE id = '$node_id'";
    if ($conn->query($sql) !== TRUE) {
        die("Error updating connection data: " . $conn->error);
    }
}

// Close database connection
$conn->close();

// Send response back to JavaScript
$response = array('success' => true);
echo json_encode($response);
?>

</body>
</html>