<div id="recent_orders_container">
    <a class="mobile" href="index.php"><img src="./img/icons/return_arrow_left.svg" alt=""> Home</a>
    <h1>Recent Orders</h1>
</div>

<div id="recent_orders">
    <?php
    function generate_order($order_date, $icon_link, $order_item, $order_price, $orderID){
        echo '    <div class="order">';
        echo '        <img src="./img/menu/thumbnail/' . $icon_link . '" alt="">';
        echo '        <div><h2 class="item_name">' . $order_item . '</h2>';
        echo '        <p>Total: $' . $order_price . '</p>';
        echo '        <p>' . $order_date . '</p>';
        echo '<a href="./receipt.php?o_id=' . $orderID . '">';
        echo '    View Receipt →';
        echo '</a>';
        echo '    </div></div>';
    }

    if(isset($_COOKIE["name"]) && isset($_COOKIE["phone_number"]) && userExists($conn, $_COOKIE["name"], $_COOKIE["phone_number"])){
        $userID = getUserID($conn, $_COOKIE["name"], $_COOKIE["phone_number"]);
        $sql = "SELECT * FROM orders WHERE o_u_id = '". $userID . "'";
        $result = mysqli_query($conn,$sql);
    
        if ($result && !($result->num_rows == 0)) {
            $resultCheck = mysqli_num_rows($result);
            if($resultCheck>=1){
                while ($row = mysqli_fetch_assoc($result)) {
                    $orders = unserialize($row["o_order_detail"]);
                    $items = "";
                    foreach($orders as $theOrder){
                        $items .= $theOrder->getName();
                        if($theOrder != end($orders)){
                            $items .= ", ";
                        }
                    }
                    
                    $img = reset($orders)->getImg();
                    generate_order($row["o_ts"], $img, $items, $row["o_price"],$row["o_id"]);
                }
            }
        }
        else{
            echo '<div id="cart_empty">';
            echo '<div>';
            echo '    <img src="./img/icons/cart_empty_crying_bird.svg" alt="" width="50%" height="50%">';
            echo '    <h2>You have no past orders!</h2>';
            echo '</div>';
            echo '<a href="./menu.php" class="btn">Order Now</a>';
            echo '</div>';
        }
    }
    ?>
</div>
