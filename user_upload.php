<?php

define("HELP", " --file [csv file name] – this is the name of the CSV to be parsed
        \n --create_table – this will cause the MySQL users table to be built (and no further
        \n action will be taken)
        \n --dry_run – this will be used with the --file directive in case we want to run the
        script but not insert into the DB. All other functions will be executed, but the
        database won't be altered
        \n -u – MySQL username
        \n -p – MySQL password
        \n -h – MySQL host
        \n -s – MySQL schema name
        \n --help – which will output the above list of directives with details.");

function db_connect($db_server,$dbuser,$dbpass,$dbname)// Database connection functions 
    {

        $connection  = mysqli_connect($db_server,
                                        $dbuser,
                                        $dbpass,
                                        $dbname);
        if (!$connection) {
            die("Could not connected:" . mysqli_connect_error());
        };
        echo 'Connected Database successfully';

        return $connection;
    }

 function db_close($connection) // closing the db connection.
    {
        mysqli_close($connection);
    }

function create_table($connection) // Create table function
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
        $sql = 'INSERT into test.userlist(name,surname,email)
        VALUES ("'.trim($name).'","'.trim($surname).'","'.trim($email).'")';

        //echo $sql;

        if(mysqli_query($connection, $sql))
        {  
            echo "\r\nRecord inserted successfully";  
           }else{  
            echo "\r\nCould not insert record: ". mysqli_error($connection);
            echo "\r\n";
            echo $sql;
           }  

    }
    function convert_capitilize($text)
    {
        return ucwords(strtolower($text));
    }

    function check_cliparameter(){
    
        if(isset($options['u'])&&isset($options['p'])&&isset($options['h'])&&isset($options['s']))
        {
        }
        else 
        {
            die("Missing the database parameter, see --help\n");
        }


    }

    function get_csv($filename)
    {
        $user_array = array_map('str_getcsv', file($filename));
        array_shift($user_array);
        return($user_array);
    }


    $clioption = array(
        "file::",
        "create_table::",
        "dry_run::",
        "help::");
    
    $cliparameter="";
    $cliparameter.="u:";
    $cliparameter.="p:";
    $cliparameter.="h:";
    $cliparameter.="s";
    
    $options = getopt($cliparameter,$clioption);

// Checking the user options
if(isset($options['help']))
{
    echo HELP;
}
elseif(isset($options['create_table']))
{
    check_cliparameter();
    var_dump($options);
    
    $db = db_connect($options['h'],$options['u'],$options['h'],$options['s']);
    create_table($db);
    db_close($db);
}
elseif(isset($options['file'])) //  Checking the normal option
{
    var_dump($options);
    if(isset($options['u'])&&isset($options['p'])&&isset($options['h'])&&isset($options['s'])) //Checking the short options 
    {
        $csvdata= get_csv($options['file']);
        //print_r($csvdata);
        $db = db_connect($options['h'],$options['u'],$options['p'],$options['s']);

        foreach($csvdata as $data)
        {
            if (filter_var($data[2], FILTER_VALIDATE_EMAIL)) //Checking the email validation
            {
                $email = $data[2];
                if(!isset($options['dry_run'])) // Checking the Dry run option 
                {
                    insert_data($db,convert_capitilize($data[0]),convert_capitilize($data[1]),$email);
                }
                
            } else {
                echo("\n$data[2] is not a valid email address");
            }
        }
        db_close($db);
    }
    else
    {
        echo "Missing database parameter, see --help";
    }
}

else
{
    echo "Invalid option, see --help";
}