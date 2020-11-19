<?php 

require('FileHandler.php');
require('Connection.php');
require('NFA.php');
require('Document.php');
require('Snippet.php');

// koneksi database
$conn = Connection::get_connection("db_textSearch");

// buat file hanlder
$fileHandler = new FileHandler($conn);

// tombol submit untuk upload file
if( isset($_POST['btnFile']) ){

    // cek tipe upload file
    if( $_POST['inputType'] == "gradually" ){
        $fileHandler->store("documents");
    } else{
        $fileHandler->delete_all("documents");
        $fileHandler->store("documents");
    }
}

// tombol cari
$accepted_documents = [];
if( isset($_POST['btnSearch']) ){

    if ( !empty($_POST['iSearch']) ){

        $start_time = microtime(true); // waktu mulai

        // membuat mesin nfa
        $nfa = NFA::create_textSearch_machine($_POST['iSearch']);

        // ambil semua file
        $files = $fileHandler->get_all("documents");

        // ambil satu dokumen
        foreach( $files as $file ){
            $path = "doc/" . $file["name"] . "." . $file["extensions"] ;
            $document = new Document($path);

            //cari hasil dokumen diterima/tidak
            $result = $nfa->result($document);

            //simpan dokumen jika diterima
            if( $result['accepted'] ){
                $snippet = new Snippet();
                $new_snippet = $snippet->get_snippet($document, $result['snippet_key']);
                $accepted_documents[] = [
                    "document" => $document,
                    "snippet" => $new_snippet
                ];
            }
        }
        
        $end_time = microtime(true); // waktu selesai
        $execution_time = ($end_time - $start_time); // hitung waktu

        // session
        session_start();
        $_SESSION['nfa'] = $nfa;
    }
}

?>




<!-- tampilan web -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text Search</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>

    <!-- searching dan upload file -->
    <div class="header">

        <h1>Text Search App</h1>

        <form  action="index.php" enctype='multipart/form-data' method='POST'>
            <!-- search engine -->
            <div class="searchEngine">
                <label for="search">Pencarian : </label>
                <input type="text" id="search" name="iSearch" size="50" value="<?php if( isset($_POST['iSearch']) ) echo $_POST['iSearch'];  ?>"> 
                <button  name="btnSearch">cari</button><br><br>

                <ul>
                    <li>Maximum file per upload : 20 file</li>
                    <li>Maximum file size : 20 MB</li>
                    <li>File extensions : .txt</li>
                </ul>

            </div>
            

            <!-- file upload -->
            <div class="uploader">
                <label for="files">Select files:</label>
                <input type="file" id="files" name="files[]" multiple><br><br>

                <label>Tipe file upload : </label><br>
                <input type="radio" id="changed" name="inputType" value="changed" checked>
                <label for="changed">Changed</label><br>
                <input type="radio" id="gradually" name="inputType" value="gradually">
                <label for="gradually">Gradually</label><br>
                
                <br>
                <button  name="btnFile">submit</button><br>
            </div>
            
        </form>
        <div class="menu">
            <span class="menu-item"><a href="myfiles.php" target="_blank" >My Files</a></span>
        </div>
    </div>

    

    <!-- list dokumen -->
    <div class="content">

        <?php if( isset($_POST['btnSearch']) && !empty($_POST['iSearch']) ) :?>

            <div> 
                <h3>Time Consumed : </h3>
                <p><pre><?php echo $execution_time . " sec"?></pre></p>
            </div>
            <div class="menu">
                <span class="menu-item"><a href="quintuple.php" target="_blank" >Quintuple</a></span>
            </div>
            <div class="documentContainer">
                <ul type="none">

                    <!-- cetak semua list dokument -->
                    <?php foreach ($accepted_documents as $document) : ?>
                        <li> 
                            <a href="<?php echo $document['document']->path; ?>" target="_blank">
                                <h2><?php echo $document['document']->name; ?></h2>
                            </a>
                            <p>
                                <?php echo "..." . $document['snippet']->str_before; ?>
                                <strong>
                                    <?php echo " " . $document['snippet']->highlights . " "; ?>
                                </strong>
                                <?php echo $document['snippet']->str_after . "..."; ?>
                            </p>
                            <hr>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="footer">
        <?php endif;  ?>

    </div>
    
    <script src="js/script.js"></script>
</body>
</html>