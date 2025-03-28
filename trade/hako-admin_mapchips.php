<?php
// Path to the JSON file
$jsonFilePath = './config_jsons/mapchip.json';

// Check if the file exists
if (!file_exists($jsonFilePath)) {
    echo $jsonFilePath;
    die('JSON file not found.');
}

// Read and decode the JSON file
$jsonData = json_decode(file_get_contents($jsonFilePath), true);

// Check if the JSON data is valid
//if (json_last_error() !== JSON_ERROR_NONE) {
//    die('Invalid JSON data.');
//}

// Start HTML output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapchip JSON Table</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <h1>Mapchip JSON Table</h1>
    <table>
        <thead>
            <tr>
                <?php
                // Display table headers dynamically based on JSON keys
                if (!empty($jsonData)) {
                    foreach (array_keys(current($jsonData)) as $key) {
                        echo "<th>" . htmlspecialchars($key) . "</th>";
                    }
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            // Display table rows dynamically based on JSON data
            foreach ($jsonData as $row) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>" . htmlspecialchars($cell) . "</td>";
                }
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>