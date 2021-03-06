<?php
include_once __DIR__ . "/dbh_inc.php";
// Menu Related
    function get_menu_items($conn){
        $sql = "SELECT * FROM menu";
        $result = mysqli_query($conn,$sql);

        if($result != false){
            $resultCheck = mysqli_num_rows($result);
            if($resultCheck>0){
                while ($row = mysqli_fetch_assoc($result)) {
                    menu_template($row["m_name"], $row["m_icon"], $row['m_id']);
                }
            }
        }
    }

    function menu_template($food_name, $icon_link, $page_link){
        echo '    <a class="food_item" href="customize.php?id=' . $page_link . '">';
        echo '        <img src="img/menu/icons/' . $icon_link . '" alt="">';
        echo '        <p>' . $food_name . '</p>';
        echo '    </a>';
    }

    function customization_section_template($conn, $m_name, $category_name, $is_single){
        $sql = "SELECT * FROM customization WHERE c_nameofdish = '". $m_name . "' AND c_category = '" . $category_name ."'";
        $result = mysqli_query($conn,$sql); 

        if($result && !($result->num_rows == 0)){
            echo '<div class="customize_section">';
            echo '    <p class="section_title">'. $category_name .'</p>';
            if($is_single == 0){
                echo '    <p>Choose 1</p>';
            }
            else{
                echo '    <p>Optional</p>';
            }
            echo '    <div class="selection">';

            if(mysqli_num_rows($result)>0){
                while ($row = mysqli_fetch_assoc($result)) {
                    customization_item_template($row["c_category"], $row["c_option"], $row['c_ischeckbox'], $row['c_isdefault'], $row['c_additionalprice']);
                }
            }
            echo '    </div>';
            echo '</div>';
        }
    }

    function customization_item_template($item_category, $item_name, $is_single, $is_default, $price){
        if ($is_single == 0){
            $type = "radio";
        }
        else{
            $type = "checkbox";
            $item_category .= "[]";
        }

        if ($price == 0){
            $class = "without_price_tag";
        }
        else{
            $class = "with_price_tag";
        }

        if ($is_default == 1){
            $checkmark = "checked";
        }
        else{
            $checkmark = "";
        }

        echo '        <div  class="'. $class . '">';
        if ($price != 0){
            echo '<p class="price_tag">+$'. number_format($price,2) . '</p>';
        }
        echo '            <input class="button_input" type="'. $type . '" name="'. $item_category . '" value="'. $item_name . '" '. $checkmark . '>';
        echo '            <label class="button_label" for="'. $item_name . '">'. $item_name . '</label>';
        echo '        </div>';
    }

    function get_thumbnail_img($conn, $m_name){
        $sql = "SELECT * FROM menu WHERE m_name = '". $m_name . "'";
        $result = mysqli_query($conn,$sql);

        if($result != false){
            $resultCheck = mysqli_num_rows($result);
            if($resultCheck==1){
                while ($row = mysqli_fetch_assoc($result)) {
                    return $row["m_image"];
                }
            }
        }
    }

    class cart_item{
        public function __construct(string $uid, int $id, string $name, string $img)
        {
            $this->id = $id;
            $this->uid = $uid;
            $this->name = $name;
            $this->items = array();
            $this->price = 0.00;
            $this->img = $img;
        }
        
        public function addItem(string $category, string $name){
            if(array_key_exists($category, $this->items)){
                array_push($this->items[$category], $name);
            }
            else{
                $this->items[$category] = array($name);
            }
        }

        public function getItems(){
            return $this->items;
        }

        public function getItems_as_array(){
            $array = [];
            foreach($this->items as $key){
                foreach($key as $i){
                    array_push($array, $i);
                }
            }
            return $array;
        }

        public function getUID(){
            return $this->uid;
        }

        public function getID(){
            return $this->id;
        }

        public function getName(){
            return $this->name;
        }
        
        public function getPrice(){
            return $this->price;
        }

        public function getImg(){
            return $this->img;
        }
    }

function cart_item_template(string $name, array $items, $price, string $img){
    echo '<div class="cart_item_card">';
    echo '    <div class="item">';
    echo '        <div class="img" style="background-image:url(./img/menu/thumbnail/' . $img . ');"></div>';
    echo '        <div class="item_description">';
    echo '            <h3>' . $name . '</h3>';
    echo '            <p>';
    $echo = "";
    foreach($items as $item){
        if($item != "None"){
            $echo .= $item;
            if($item != end($items)){
                $echo .= ", ";
            }

        }
    }
    echo substr($echo, 0, -2);
    echo '</p>';
    echo '        </div>';
    echo '    </div>';
    echo '    <div class="price">$' . number_format($price,2) . '</div>';
    echo '    <div class="item_mod">';
    echo '        <a href="">Edit</a>';
    echo '        <a href="">Remove</a>';
    echo '        <div class="quantity">';
    echo '            <div class="subtract"></div>';
    echo '            <p>1</p>';
    echo '            <div class="add"></div>';
    echo '        </div>';
    echo '    </div>';
    echo '</div>';
}

function confirm_item_template(string $name, array $items, $price, string $img){
    echo '<div class="item_info">';
    echo '    <img src="./img/menu/thumbnail/' . $img . '" alt="Cart Item Photo">';
    echo '    <div>';
    echo '        <p class="item_name">' . $name . '</p>';
    echo '        <p class="item_content">';
    $echo = "";
    foreach($items as $item){
        if($item != "None"){
            $echo .= $item;
            if($item != end($items)){
                $echo .= ", ";
            }

        }
    }
    echo substr($echo, 0, -2);
    echo '</p>';
    echo '        <p class="item_price">Price: $' . number_format($price,2) . '</p>';
    echo '    </div>';
    echo '</div>';

}


function receipt_item_template(string $name, array $items, $price, string $img){
    echo '<div class="cart_item_card">';
    echo '<div class="item">';
    echo '        <div class="img" style="background-image:url(./img/menu/thumbnail/' . $img . ');"></div>';
    echo '    <div class="item_description">';
    echo '    <h3>' . $name . '</h3>';
    echo '            <p>';
    $echo = "";
    foreach($items as $item){
        if($item != "None"){
            $echo .= $item;
            if($item != end($items)){
                $echo .= ", ";
            }
        
        }
    }
    echo substr($echo, 0, -2);
    echo '</p>';
    echo '</div>';
    echo '</div>';
    echo '<p class="price">$' . number_format($price, 2) . '</p>';
    echo '</div>';
}