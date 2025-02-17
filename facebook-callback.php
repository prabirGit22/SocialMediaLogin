<?php
session_start();
require 'vendor/autoload.php';
require 'dbConfig.php';

$fb = new \Facebook\Facebook([
    'app_id' => 'YOUR_FACEBOOK_APP_ID',
    'app_secret' => 'YOUR_FACEBOOK_APP_SECRET',
    'default_graph_version' => 'v15.0',
]);

$helper = $fb->getRedirectLoginHelper();
try {
    $accessToken = $helper->getAccessToken();
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}

if (isset($accessToken)) {
    $response = $fb->get('/me?fields=id,name,email,picture', $accessToken);
    $user = $response->getGraphUser();

    $name = $user['name'];
    $email = $user['email'];
    $picture = $user['picture']['url'];
    $oauth_provider = 'facebook';
    $oauth_uid = $user['id'];

    $query = "SELECT * FROM users WHERE oauth_provider='$oauth_provider' AND oauth_uid='$oauth_uid'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $query = "INSERT INTO users (oauth_provider, oauth_uid, name, email, picture) VALUES ('$oauth_provider', '$oauth_uid', '$name', '$email', '$picture')";
        $conn->query($query);
    }

    $_SESSION['userData'] = [
        'name' => $name,
        'email' => $email,
        'picture' => $picture
    ];

    header('Location: index.php');
    exit;
}
?>
