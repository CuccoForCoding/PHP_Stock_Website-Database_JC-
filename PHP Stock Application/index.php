<!DOCTYPE html>
<!-- comment --><!-- <p>test</p> -->
<?php
//database server type, location, and database name in the string below
$data_source_name = 'mysql:host=localhost;dbname=Stocks';

$username = 'root';
$password = '';

try {

    $database = new PDO($data_source_name, $username, $password);

    echo "<p>Database Connection Successful!</p>";

    $action_stock = htmlspecialchars(filter_input(INPUT_POST, "action_stock"));
    $symbol = htmlspecialchars(filter_input(INPUT_POST, "symbol"));
    $name = htmlspecialchars(filter_input(INPUT_POST, "name"));
    $current_price = filter_input(INPUT_POST, "current_price", FILTER_VALIDATE_FLOAT);

    if ($action_stock == "insert_stock" && $symbol != "" && $name != "" && $current_price != 0) {
       
        $query = "INSERT INTO stocks (symbol, name, current_price)"
                . "VALUES (:symbol, :name, :current_price)";

        //value binding in PDO protects against SQL Injection
        $statement = $database->prepare($query);
        $statement->bindValue(":symbol", $symbol);
        $statement->bindValue(":name", $name);
        $statement->bindValue(":current_price", $current_price);

        $statement->execute();

        $statement->closeCursor();
    } else if ($action_stock == "update_s" && $symbol != "" && $name != "" && $current_price != 0) {

        //Instead use substitutions with ':'
        $query = "update stocks set name = :name, current_price = :current_price "
                . " where symbol = :symbol";

        //value binding in PDO protects against SQL Injection
        $statement = $database->prepare($query);
        $statement->bindValue(":symbol", $symbol);
        $statement->bindValue(":name", $name);
        $statement->bindValue(":current_price", $current_price);

        $statement->execute();

        $statement->closeCursor();
    } else if ($action_stock == "delete_s" && $symbol != "") {

        //Instead use substitutions with ':'
        $query = "delete from stocks "
                . " where symbol = :symbol";

        //value binding in PDO protects against SQL Injection
        $statement = $database->prepare($query);
        $statement->bindValue(":symbol", $symbol);

        $statement->execute();

        $statement->closeCursor();
    }

    //************************************************************************************************************
    //************************************************************************************************************
    //************************************************************************************************************
    //************************************************************************************************************
    //************************************************************************************************************
    //************************************************************************************************************
    //************************************************************************************************************


    $action_user = htmlspecialchars(filter_input(INPUT_POST, "action_user"));
    $name_user = htmlspecialchars(filter_input(INPUT_POST, "name_user"));
    $email_address = htmlspecialchars(filter_input(INPUT_POST, "email_address"));
    $cash_balance = filter_input(INPUT_POST, "cash_balance", FILTER_VALIDATE_FLOAT);

    if ($action_user == "insert_user" && $name_user != "" && $email_address != "" && $cash_balance != 0) {
      
        $query = "INSERT INTO users (name, email_address, cash_balance)"
                . "VALUES (:name, :email_address, :cash_balance)";

        //value binding in PDO protects against SQL Injection
        $statement = $database->prepare($query);
        $statement->bindValue(":name", $name_user);
        $statement->bindValue(":email_address", $email_address);
        $statement->bindValue(":cash_balance", $cash_balance);

        $statement->execute();

        $statement->closeCursor();
    } else if ($action_user == "update_user" && $name_user != "" && $email_address != "" && $cash_balance != 0) {
        // DANGER!!!!!!
        //Never just plug values into a query!
        //Instead use substitutions with ':'
        $query = "update users set name = :name, email_address = :email_address, cash_balance=:cash_balance  "
                . " where email_address = :email_address";

        //value binding in PDO protects against SQL Injection
        $statement = $database->prepare($query);
        $statement->bindValue(":name", $name_user);
        $statement->bindValue(":email_address", $email_address);
        $statement->bindValue(":cash_balance", $cash_balance);

        $statement->execute();

        $statement->closeCursor();
    } else if ($action_user == "delete_user" && $name_user != "") {

        //Instead use substitutions with ':'
        $query = "delete from users "
                . " where name = :name";

        //value binding in PDO protects against SQL Injection
        $statement = $database->prepare($query);
        $statement->bindValue(":name", $name_user);

        $statement->execute();

        $statement->closeCursor();
    }

    //************************************************************************************************************
    //************************************************************************************************************
    //************************************************************************************************************
    //************************************************************************************************************
    //************************************************************************************************************
    //************************************************************************************************************
    //************************************************************************************************************

    $action = htmlspecialchars(filter_input(INPUT_POST, "action"));
    $user_id = htmlspecialchars(filter_input(INPUT_POST, "user_id"));
    $stock_id = htmlspecialchars(filter_input(INPUT_POST, "stock_id"));
    $quantity = htmlspecialchars(filter_input(INPUT_POST, "quantity"));
    $transaction_id = htmlspecialchars(filter_input(INPUT_POST, "transaction_id"));

    if ($action == "insert_transaction" && $user_id != "" && $stock_id != "" && $quantity != 0) {

        $query = 'SELECT name, email_address, cash_balance, id FROM users WHERE id = :user_id';
        
        $statement_current_price = $database->prepare($query);
        $statement_current_price->bindValue(':user_id', $user_id);
        $statement_current_price->execute();
        $stock = $statement_current_price->fetch();
        $cash_bal = $stock['cash_balance'];
        $statement_current_price->closeCursor();

        $query = 'SELECT symbol, name, current_price, id FROM stocks WHERE id = :stock_id';

        $statement_stock_price = $database->prepare($query);
        $statement_stock_price->bindValue(':stock_id', $stock_id);
        $statement_stock_price->execute();
        $stock = $statement_stock_price->fetch();
        $current_p = $stock['current_price'];
        $statement_stock_price->closeCursor();

        $current_p = $current_p * $quantity;

        if ($cash_bal > $current_p) {
          echo "Success!";
          
            $query = 'INSERT INTO transaction(user_id, stock_id, quantity, price)'
                    . ' VALUES (:user_id,:stock_id,:quantity, :price)';

            //prepare the query please
            $statement_transaction_price = $database->prepare($query);

            $statement_transaction_price->bindValue(':user_id', $user_id);
            $statement_transaction_price->bindValue(':stock_id', $stock_id);
            $statement_transaction_price->bindValue(':quantity', $quantity);
            $statement_transaction_price->bindValue(':price', $current_p);

            //run  the query please
            $statement_transaction_price->execute();
            $statement_transaction_price->closeCursor();
            
             $cash_bal = $cash_bal - $current_p;
            
            $query = "update users set cash_balance = :cash_balance WHERE id = :user_id";

        //value binding in PDO protects against SQL Injection
        $statement = $database->prepare($query);
        $statement->bindValue(":cash_balance", $cash_bal);
        $statement->bindValue(":user_id", $user_id);
        $statement->execute();
        $statement->closeCursor();
            
        } else {
            echo"user doesnt have fund";
        }
        
    } else if ($action == "update_trans" && $transaction_id != "" && $stock_id != "" && $quantity != 0) {
   
        $query = "UPDATE transaction SET stock_id = :stock_id, quantity = :quantity WHERE id = :transaction_id";

        //value binding in PDO protects against SQL Injection
        $statement = $database->prepare($query);
        $statement->bindValue(':transaction_id', $transaction_id);
        $statement->bindValue(':stock_id', $stock_id);
        $statement->bindValue(':quantity', $quantity);
        $statement->execute();
        $statement->closeCursor();
        
        
    } else if ($action == "delete_trans" && $stock_id != "" && $transaction_id != "") {      
        
        $query = 'SELECT quantity, user_id FROM transaction WHERE id = :transaction_id';

        $statement = $database->prepare($query);
        $statement->bindValue(':transaction_id', $transaction_id);
        $statement->execute();
        $stock = $statement->fetch();
        $quantity = $stock['quantity'];
        $user_id = $stock['user_id'];
        $statement->closeCursor();
        
        
        $query = 'SELECT current_price FROM stocks WHERE id = :stock_id';

        $statement = $database->prepare($query);
        $statement->bindValue(':stock_id', $stock_id);
        $statement->execute();
        $stock = $statement->fetch();
        $current_p = $stock['current_price'];
        $statement->closeCursor();

        $current_p = $current_p * $quantity;
        
        
        $query = 'SELECT cash_balance FROM users WHERE id = :user_id';

        $statement = $database->prepare($query);
        $statement->bindValue(':user_id', $user_id);
        $statement->execute();
        $stock = $statement->fetch();
        $cash_bal = $stock['cash_balance'];
        $statement->closeCursor();

        $cash_bal = $cash_bal + $current_p;

        $query = "UPDATE users set cash_balance = :cash_balance WHERE id = :user_id";
        
         $statement = $database->prepare($query);
         $statement->bindValue(':cash_balance', $cash_bal);
         $statement->bindValue(':user_id', $user_id);
         $statement->execute();
         $statement->closeCursor();
         
        
        //Instead use substitutions with ':'
        $query = "DELETE from transaction WHERE stock_id = :stock_id AND id = :transaction_id";

        //value binding in PDO protects against SQL Injection
        $statement = $database->prepare($query);
        $statement->bindValue(":stock_id", $stock_id); 
        $statement->bindValue(":transaction_id", $transaction_id);    // ":user_id" WHERE MY QUERY IS GETTING ITS VALUE FROM 
        $statement->execute();    
        $statement->closeCursor();
    }

    //************************************************************************************************************
    //************************************************************************************************************
    //************************************************************************************************************
    //************************************************************************************************************
    //************************************************************************************************************
    //************************************************************************************************************
    //************************************************************************************************************


    $query = 'SELECT symbol, name, current_price, id from stocks';

    //prepare the query please
    $statement = $database->prepare($query);

    //run  the query please
    $statement->execute();

    ///might be risky if you have HUGE amounts of data
    $stock = $statement->fetchAll();

    $statement->closeCursor();

    $query2 = 'SELECT name, email_address, cash_balance, id from users';

    //prepare the query please
    $statement2 = $database->prepare($query2);

    //run  the query please
    $statement2->execute();

    ///might be risky if you have HUGE amounts of data
    $stock2 = $statement2->fetchAll();

    $statement2->closeCursor();

    $query3 = 'SELECT user_id, stock_id, quantity, price, timestamp, id from transaction';

    //prepare the query please
    $statement3 = $database->prepare($query3);

    //run  the query please
    $statement3->execute();

    ///might be risky if you have HUGE amounts of data
    $stocks3 = $statement3->fetchAll();

    $statement->closeCursor();
    
} catch (Exception $ex) {
    $error_message = $ex->getMessage();
    echo "<p>Error Message: $error_message </p>";
}
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <table>
            <tr>
                <th>Name</th>
                <th>Symbol</th>
                <th>Current Price</th>
                <th>ID</th>
            </tr>
            <?php foreach ($stock as $stock) : ?>
                <tr>
                    <td> <?php echo $stock['symbol'] ?> </td>
                    <td> <?php echo $stock['name'] ?> </td>
                    <td> <?php echo $stock['current_price'] ?> </td>
                    <td> <?php echo $stock['id'] ?> </td>
                </tr>

            <?php endforeach; ?>
        </table>
        <br>
        <h2>Add Stock</h2>
        <form action="index.php" method="post">
            <label>Symbol: </label><br>
            <input type="text" name="symbol"><br><br>
            <label>Name:  </label><br>
            <input type="text" name="name"><br><br>
            <label>Current Price: </label><br>
            <input type="text" name="current_price"><br><br>
            <input type="hidden" name="action_stock" value="insert_stock">
            <label>&nbsp; </label><br><br>
            <input type="submit" value="Add Stock">
        </form> 
        <br>

        <h2>Update Stock</h2>
        <form action="index.php" method="post">
            <label>Symbol: </label><br>
            <input type="text" name="symbol"><br><br>
            <label>Name:  </label><br>
            <input type="text" name="name"><br><br>
            <label>Current Price: </label><br>
            <input type="text" name="current_price"><br><br>
            <input type="hidden" name="action_stock" value="update_s">
            <label>&nbsp; </label><br>
            <input type="submit" value="Update Stock"><br><br>
        </form> 
        <br>

        <h2>Delete Stock</h2>
        <form action="index.php" method="post">
            <label>Symbol: </label><br>
            <input type="text" name="symbol"><br><br>
            <input type="hidden" name="action_stock" value="delete_s">
            <label>&nbsp; </label><br>
            <input type="submit" value="Delete Stock"><br><br>
        </form> 


        <table>
            <tr>
                <th>Name</th>
                <th>Email Address</th>
                <th>Cash Balance</th>
                <th>ID</th>
            </tr>
            <?php foreach ($stock2 as $users) : ?>
                <tr>
                    <td> <?php echo $users['name'] ?> </td>
                    <td> <?php echo $users['email_address'] ?> </td>
                    <td> <?php echo $users['cash_balance'] ?> </td>
                    <td> <?php echo $users['id'] ?> </td>
                </tr>

            <?php endforeach; ?>
        </table>
        <br>
        <h2>Add User</h2>
        <form action="index.php" method="post">
            <label>Name: </label><br>
            <input type="text" name="name_user"><br><br>
            <label>Email Address:  </label><br>
            <input type="text" name="email_address"><br><br>
            <label>Cash Balance: </label><br>
            <input type="text" name="cash_balance"><br><br>
            <input type="hidden" name="action_user" value="insert_user">
            <label>&nbsp; </label><br><br>
            <input type="submit" value="Add User">
        </form> 
        <br>

        <h2>Update User</h2>
        <form action="index.php" method="post">
            <label>Name: </label><br>
            <input type="text" name="name_user"><br><br>
            <label>Email Address:  </label><br>
            <input type="text" name="email_address"><br><br>
            <label>Cash Balance: </label><br>
            <input type="text" name="cash_balance"><br><br>
            <input type="hidden" name="action_user" value="update_user">
            <label>&nbsp; </label><br><br>
            <input type="submit" value="Update User">
        </form> 
        <br>

        <h2>Delete User</h2>
        <form action="index.php" method="post">
            <label>Name: </label><br>
            <input type="text" name="name_user"><br><br>
            <input type="hidden" name="action_user" value="delete_user">
            <label>&nbsp; </label><br>
            <input type="submit" value="Delete User"><br><br>
        </form> 


        <table>
            <tr>
                <th>User ID</th>
                <th>Stock Id</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Timestamp</th><!-- comment -->
                <th>ID</th>
            </tr>
            <?php foreach ($stocks3 as $transaction) : ?>
                <tr>
                    <td> <?php echo $transaction['user_id'] ?> </td>
                    <td> <?php echo $transaction['stock_id'] ?> </td>
                    <td> <?php echo $transaction['quantity'] ?> </td>
                    <td> <?php echo $transaction['price'] ?> </td>
                    <td> <?php echo $transaction['timestamp'] ?> </td><!-- comment -->
                    <td> <?php echo $transaction['id'] ?> </td>
                </tr>

            <?php endforeach; ?>
        </table>
        <br>
        <h2>Add Transaction</h2>
        <form action="index.php" method="post">
            <label>User ID: </label><br>
            <input type="text" name="user_id"><br><br>
            <label>Stock ID:  </label><br>
            <input type="text" name="stock_id"><br><br>
            <label>Quantity: </label><br>
            <input type="text" name="quantity"><br><br>

            <input type="hidden" name="action" value="insert_transaction">
            <label>&nbsp; </label><br><br>
            <input type="submit" value="Add Transaction">
        </form> 
        <br>

        <h2>Update Transaction</h2>
        <form action="index.php" method="post">
            <label>Transaction ID: </label><br>
            <input type="text" name="transaction_id"><br><br>
            <label>Stock ID:  </label><br>
            <input type="text" name="stock_id"><br><br>
            <label>Quantity: </label><br>
            <input type="text" name="quantity"><br><br>
            <input type="hidden" name="action" value="update_trans">
            <label>&nbsp; </label><br><br>
            <input type="submit" value="Update Transaction">
        </form> 
        <br>

        <h2>Delete Transaction</h2>
        <form action="index.php" method="post">
            <label>Stock ID: </label><br>
            <input type="text" name="stock_id"><br><br>
            <label>Transaction:  </label><br>
             <input type="text" name="transaction_id"><br><br>
            <input type="hidden" name="action" value="delete_trans">
            <label>&nbsp; </label><br>
            <input type="submit" value="Delete Transaction"><br><br>
        </form> 

    </body>
</html>

