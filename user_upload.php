<?php

// Data base functions 
function db_connect($db_server,$dbuser,$dbname){
    $config = new stdClass();
    $config->dbserver = $db_server;
    $config->dbuser=$dbuser;
    $config->dbpass='';
    $config->dbname=$dbname;

    $connection  = mysqli_connect($config->dbserver,
                                $config->dbuser,
                                $config->dbpass,
                                $config->dbname);

    if (!$connection) {
        die("Could not connect:" . mysqli_connect_error());
      };
     echo 'Connected successfully';

    return $connection;
 }

 function db_close($connection)
    {
        mysqli_close($connection);
    }

    function create_table($connection)
    {
        $sql = "create table userlist(
            id INT AUTO_INCREMENT, 
            name VARCHAR(50) NOT NULL,
            surname VARCHAR(50) NOT NULL,
            email VARCHAR(50) NOT NULL,
            PRIMARY KEY (`id`))";
        if(mysqli_query($connection,$sql))
        {
            echo "\r\nUser table created successfully";
        }
        else
        {
            echo "\r\nCould not create table: ". mysqli_error($connection); 
        }
    }

    function insert_data($connection,$name,$surname,$email)
    {
        $sql = "INSERT into userlist(name,surname,email)
        VALUES ('$name','$surname','$email')";

        if(mysqli_query($connection, $sql)){  
            echo "\r\nRecord inserted successfully";  
           }else{  
            echo "\r\nCould not insert record: ". mysqli_error($connection);  
           }  

    }
    function convert_capitilize($text)
    {
        return ucwords(strtolower($text));
    }

    function check_cliparameter(){
    
        $err_msg = "";
        if(!isset($options['u']))
        {
            $err_msg.= " \n please enter MySQL - username";
        }
        elseif (!isset($options['p'])) 
        {
            $err_msg.= " \n please enter MySQL - password";
        } 

        echo $err_msg;

    }

    function get_csv($filename)
    {

    $user_array = array_map('str_getcsv', file($filename));

    array_shift($user_array);

    return($user_array);
    }


    $clioption = array(
        "file:",
        "create_table::",
        "dry_run::",
        "help::");
    
    $cliparameter="";
    $cliparameter.="u:";
    $cliparameter.="p:";
    $cliparameter.="h:";
    
    $options = getopt($cliparameter,$clioption);

// Checking the user options
if(isset($options['help']))
{
    echo " --file [csv file name] – this is the name of the CSV to be parsed
    \n --create_table – this will cause the MySQL users table to be built (and no further
    \n action will be taken)
    \n --dry_run – this will be used with the --file directive in case we want to run the
    script but not insert into the DB. All other functions will be executed, but the
    database won't be altered
    \n -u – MySQL username
    \n -p – MySQL password
    \n -h – MySQL host
    \n --help – which will output the above list of directives with details.";
}
elseif(isset($options['create_table']))
{
    var_dump($options);
    //check_cliparameter();

    $db = db_connect('127.0.0.1',$options['u'],$options['h']);

    create_table($db);
    db_close($db);
}
elseif(isset($options['file']))
{
    echo "file ";

    $csvdata= get_csv($options['file']);
    print_r($csvdata);
    $db = db_connect();

    foreach($csvdata as $data)
    {

        insert_data($db,convert_capitilize($data[0]),convert_capitilize($data[1]),$email);
    }
    db_close($db);
}
elseif(isset($options['file'])&&isset($options['dry_run']))
{
    echo "file & dry_run";
}