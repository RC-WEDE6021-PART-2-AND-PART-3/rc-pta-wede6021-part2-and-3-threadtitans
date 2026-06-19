<?php

include 'config/DBConn.php';

function ProcessInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function Login($conn, $email, $password) {
    $email = mysqli_real_escape_string($conn, $email);
    $hashed = md5($password);
    
    $query = "SELECT * FROM tblUsers WHERE Email = '$email' AND PasswordHash = '$hashed'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if ($user['IsVerified'] == 'verified' || $user['UserType'] == 'admin') {
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['username'] = $user['Username'];
            $_SESSION['user_type'] = $user['UserType'];
            $_SESSION['fullname'] = $user['FullName'];
            return true;
        }
    }
    return false;
}

function AddItem($conn, $user_id, $clothe_id, $quantity = 1) {

    $check = "SELECT * FROM tblCart WHERE UserID = $user_id AND ClotheID = $clothe_id";
    $result = mysqli_query($conn, $check);
    
    if (mysqli_num_rows($result) > 0) {
        $cart = mysqli_fetch_assoc($result);
        $new_qty = $cart['Quantity'] + $quantity;
        $update = "UPDATE tblCart SET Quantity = $new_qty WHERE CartID = {$cart['CartID']}";
        return mysqli_query($conn, $update);
    } else {
        $insert = "INSERT INTO tblCart (UserID, ClotheID, Quantity) VALUES ($user_id, $clothe_id, $quantity)";
        return mysqli_query($conn, $insert);
    }
}

function RemoveItem($conn, $cart_id, $user_id) {
    $query = "DELETE FROM tblCart WHERE CartID = $cart_id AND UserID = $user_id";
    return mysqli_query($conn, $query);
}

function EmptyCart($conn, $user_id) {
    $query = "DELETE FROM tblCart WHERE UserID = $user_id";
    return mysqli_query($conn, $query);
}

function GetCartItems($conn, $user_id) {
    $query = "SELECT c.*, cl.ProductName, cl.Price, cl.ImagePath, cl.SellerID 
              FROM tblCart c 
              JOIN tblClothes cl ON c.ClotheID = cl.ClotheID 
              WHERE c.UserID = $user_id";
    return mysqli_query($conn, $query);
}

function GetCartTotal($conn, $user_id) {
    $query = "SELECT SUM(c.Quantity * cl.Price) as total 
              FROM tblCart c 
              JOIN tblClothes cl ON c.ClotheID = cl.ClotheID 
              WHERE c.UserID = $user_id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function Checkout($conn, $user_id, $address, $payment_method) {

    $cartItems = GetCartItems($conn, $user_id);
    if (mysqli_num_rows($cartItems) == 0) {
        return false;
    }
    
    $total = GetCartTotal($conn, $user_id);
    
    $orderNum = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
    $sessionId = session_id();
    
    $orderQuery = "INSERT INTO tblOrders (UserID, TotalAmount, ShippingAddress, PaymentMethod, OrderStatus, SessionID, OrderNumber) 
                   VALUES ($user_id, $total, '$address', '$payment_method', 'pending', '$sessionId', '$orderNum')";
    
    if (mysqli_query($conn, $orderQuery)) {
        $order_id = mysqli_insert_id($conn);
        
        mysqli_data_seek($cartItems, 0);
        while ($item = mysqli_fetch_assoc($cartItems)) {

            $lineQuery = "INSERT INTO tblOrderLines (OrderID, ClotheID, Quantity, Price) 
                          VALUES ($order_id, {$item['ClotheID']}, {$item['Quantity']}, {$item['Price']})";
            mysqli_query($conn, $lineQuery);
            
            $decrementQuery = "UPDATE tblClothes SET Quantity = Quantity - {$item['Quantity']} 
                               WHERE ClotheID = {$item['ClotheID']}";
            mysqli_query($conn, $decrementQuery);
        }
        
        EmptyCart($conn, $user_id);
        
        return ['order_id' => $order_id, 'order_num' => $orderNum, 'session_id' => $sessionId];
    }
    
    return false;
}
?>