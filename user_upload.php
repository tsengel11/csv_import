<?php

function db_connect(){
    $config = new stdClass();
    $config->dbserver = '127.0.0.1';
    $config->dbuser='root';
    $config->dbpass='';
    $config->dbname='test';

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


$clioption = array(
    "file:",
    "create_table::",
    "dry_run::",
    "help");
    
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
    
}