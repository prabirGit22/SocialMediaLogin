<?php
session_start();
require 'vendor/autoload.php';
require 'dbConfig.php';

$client = new Google_Client();
$client->setClientId('YOUR_GOOGLE_CLIENT_ID');
$client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
$client->setRedirectUri('http://localhost/social-login/google-callback.php');
$client->addScope('email');
$client->addScope('profile');

try {
    if (isset($_GET['code'])) {
        $client->authenticate($_GET['code']);
        $token = $client->getAccessToken();
        
        if ($client->isAccessTokenExpired()) {
            throw new Exception('Access token expired. Please try again.');
        }

        $client->setAccessToken($token);

        // Validate token
        $payload = $client->verifyIdToken();
        if (!$payload) {
            throw new Exception('Invalid ID token.');
        }

        // Get user profile
        $oauth2 = new Google_Service_Oauth2($client);
        $userInfo = $oauth2->userinfo->get();

        $name = $conn->real_escape_string($userInfo->name);
        $email = $conn->real_escape_string($userInfo->email);
        $picture = $conn->real_escape_string($userInfo->picture);
        $oauth_provider = 'google';
        $oauth_uid = $conn->real_escape_string($userInfo->id);

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
    } else {
        throw new Exception('Authorization code not found.');
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
