<?php

class database extends PDO
{

    function __construct($username = '', $password = '', $dbName = '')
    {
        try {
            if ($dbName === '') {
                $username = 'root';
                $password = '';
                $dbName = 'todos'; //hiddenad_web';
            }

            parent::__construct("mysql:host=localhost;dbname=$dbName", "$username", "$password");
            //                       echo "Connected successfully to" . '   ' . $dbName.'\n';

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    function select($query, $array = array())
    {
        $con = $this->prepare($query);
        //        print_r($con);
        foreach ($array as $key => $value) {
            $con->bindValue($key, $value);
        }
        $con->execute();
        return $con->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($table, $data, $where)
    {
        ksort($data);
        $fieldDetails = NULL;
        foreach ($data as $key => $value) {
            $fieldDetails .= "`$key`=:$key,";
        }
        $fieldDetails = rtrim($fieldDetails, ',');
        //        print("UPDATE $table SET $fieldDetails WHERE $where");
        $sth = $this->prepare("UPDATE $table SET $fieldDetails WHERE $where");
        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
            //            print($value);
        }
        if ($sth->execute()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function delete($table, $where, $limit = 1)
    {
        return $this->exec("DELETE FROM $table WHERE $where LIMIT $limit");
    }

    function insert($table, $array)
    {

        $key = array_keys($array);
        //print_r($key);
        $ope = implode("`,`", $key);
        $yemi = "`" . $ope . "`";
        $ola = implode(",:", $key);
        $mide = ":" . $ola;
        //echo $mide;
        $wuraola = "insert into $table ($yemi) values($mide)";
        //   echo $wuraola;
        $con = $this->prepare($wuraola);
        foreach ($array as $key => $value) {

            //            $enc_value=encryptdata($value);
            //             print_r($value."<br/>");
            $con->bindValue($key, $value);
        }
        //        print_r($con);
        if ($con->execute()) {
            return $id = $this->select("select * from $table order by idx DESC LIMIT 1 ");
        } else {
            return FALSE;
        }


        //        echo($con->execute());
        //        echo "successful";
    }

    function e($q)
    {
        $this->exec($q);
    }
}
