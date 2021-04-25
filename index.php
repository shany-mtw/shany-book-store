<?php

include 'dbconnect.php';
$json = file_get_contents('CodeChallengeSales.json');
//converting json object to php associative array
$data = json_decode($json, true);
$sql = "SELECT * FROM book_sales";
$result = mysqli_query($connection, $sql);
if ($result->num_rows == 0) {
    //processing array data
    foreach ($data as $sale) {

        // preparing statement for insert query
        $st = mysqli_prepare($connection, 'INSERT INTO book_sales(customer_name, customer_mail, product_id, product_name,product_price,sale_date) VALUES (?, ?, ?, ?,?,?)');

        // bind variables to insert query params
        mysqli_stmt_bind_param($st, 'ssssss', $sale['customer_name'], $sale['customer_mail'], $sale['product_id'], $sale['product_name'], $sale['product_price'], $sale['sale_date']);

        // executing insert query
        $ss = mysqli_stmt_execute($st);
    }
}
echo "<center><h2>Search sales details</h2></center>\n";
echo "<form method='post'><input type='text' name='customername' placeholder='Customer Name'>&nbsp;&nbsp;<input type='text' name='product' placeholder='Product Name' size='100'>&nbsp;&nbsp;<input type='text' name='price' placeholder='Product Price' >&nbsp;<input type='submit' name='search' value='SEARCH'></form>";
if (isset($_POST['search'])) {
    $customer_name = trim($_POST['customername']);
    $product_name = trim($_POST['product']);
    $price = trim($_POST['price']);
    $get_data = "SELECT * FROM book_sales WHERE ";
    if (!empty($customer_name)) {
        $get_data .= "customer_name LIKE '%$customer_name%'";
    }
    if (!empty($product_name)) {
        $get_data .= " AND product_name LIKE '%$product_name%'";
    }
    if (!empty($price)) {
        $get_data .= " AND product_price LIKE '%$price%'";
    }
    $result = mysqli_query($connection, $get_data);
    $sum = 0;
    if (!empty($result)) {

        echo '<table border=1><tr><th>Customer</th><th>Customer Email</th><th>Product ID</th><th>Product Name</th><th>Product Price</th><th>Sale Date</th></tr>';
        foreach ($result as $res) {
            $sum+= $res['product_price'];
            echo '<tr><td>' . $res['customer_name'] . '</td><td>' . $res['customer_mail'] . '</td><td>' . $res['product_id'] . '</td><td>' . $res['product_name'] . '</td><td>' . $res['product_price'] . '</td><td>' . $res['sale_date'] . '</td></tr>';
        }
        echo '<tr><td></td><td></td><td></td><td></td><td style="float:right;"><b>Total Price: </b>' . $sum . '</td></center></tr>';
        echo '</table>';
    }
}
?>