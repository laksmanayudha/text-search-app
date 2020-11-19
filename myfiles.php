<?php 

require('Connection.php');
require('FileHandler.php');

$conn = Connection::get_connection("db_textSearch");
$fileHandler = new FileHandler($conn);

$documents = $fileHandler->get_all("documents");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Files</title>
    <link rel="stylesheet" href="styles/quintuple.css">
    <link rel="stylesheet" href="styles/table.css">
</head>
<body>

    <div class="container">

        <h2>My Files : </h2>
        <table id="files" >
            <tr>
                <td>No</td>
                <td>File Name</td>
                <td>Extension</td>
            </tr>
            <?php foreach($documents as $no => $document) : ?>
                <tr>
                    <td><?php echo $no + 1; ?></td>
                    <td><?php echo $document['name']; ?></td>
                    <td><?php echo "." .  $document['extensions']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        

    </div>
  
    
</body>
</html>