<?php 

class FileHandler{

    private $conn;
    public $failed_files;
    public $failed_queries;

    public function __construct($connection){
        $this->conn = $connection;
    }

    public function get_all($table){

        // buat query untuk mengambil semua data dokumen
        $query = "SELECT * FROM $table";
        $result = mysqli_query($this->conn, $query);

        // ambil data semua dokumen, simpan ke array
        $data = [];
        while( $d = mysqli_fetch_assoc($result) ){
            $data[] = $d;
        }

        return $data;
    }

    public function store($table){

        // bersihkan file dari file yang tidak diinginkan
        $files = $this->clean_files($_FILES['files']);
        $failed = [];

        foreach( $files as $position => $file ){

            $query = "INSERT INTO $table VALUES ('', '{$file['name']}', '{$file['ext']}')";

            mysqli_query($this->conn, $query);
            // cek apakah file berhasil ditambahkan
            if( mysqli_affected_rows($this->conn) < 0 ){
                $failed[$position] = "[{$file['name']}]: " . mysqli_error($this->conn);
            }

        }

        $this->failed_queries = $failed;
    }

    public function delete_all($table){

        // ambil semua data dokumen
        $result = $this->get_all("documents");

        // buat query untuk menghapus semua data dokumen pada database
        $query = "DELETE FROM $table";
        mysqli_query($this->conn, $query);

        // hapus semua dokumen dari local storage
        $this->delete_all_files($result);

    }

    public function delete_all_files($files){

        foreach( $files as $file ){
            $path = "doc/" . $file["name"] . "." . $file["extensions"] ;
            unlink($path);
        }

    }

    public function clean_files($files){

        $failed = [];
        $upload_files = [];

        // cek apakah  ada file yang diupload
        if( !empty($files['name'][0]) ){
            $valid_extensions = ['txt'];

            foreach( $files['name'] as $position => $file_name ){

                $file_tmp = $files['tmp_name'][$position];
                $file_size = $files['size'][$position];
                $file_error = $files['error'][$position];

                // pisahkan nama file dan ekstensinya
                $exploded_filename = explode('.', $file_name);
                $file_ext = end($exploded_filename);
                array_pop($exploded_filename);
                $file_name = implode('.', $exploded_filename);

                // validasi ekstensi file
                if( in_array($file_ext, $valid_extensions) ){

                    // cek file error
                    if($file_error === 0){

                        // cek batas file max 20 mb
                        if( $file_size <= 20971520 ){
                            
                            // pindahkan file ke folder doc
                            $file_destination = 'doc/' . $file_name . '.' . $file_ext;
                            if(move_uploaded_file($file_tmp, $file_destination)){
                                $upload_files[$position] = [
                                    "name" => $file_name,
                                    "ext" => $file_ext
                                ];
                            } else {
                                $failed[$position] = "[{$file_name}] failed to upload.";
                            }

                        } else {
                            $failed[$position] = "[{$file_name}] is too large.";
                        }

                    } else {
                        $failed[$position] = "[{$file_name}] errored with code {$file_error}.";
                    }

                } else{
                    $failed[$position] = "[{$file_name}] file extension {$file_ext}.";
                }
            }
        }

        $this->failed_files = $failed;
        return $upload_files;
    }

}
